<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\PinjamanModel;
use App\Models\SimpananModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $anggotaModel = new AnggotaModel();
        $pinjamanModel = new PinjamanModel();
        $simpananModel = new SimpananModel();

        $data = ['title' => 'Dashboard | Koperasi'];

        // If user has any management or reporting permission, show the global dashboard. Otherwise show personal.
        if (has_permission('manage_anggota') || has_permission('manage_kas') || has_permission('view_laporan')) {
            $data['total_anggota'] = $anggotaModel->where('status', 'aktif')->countAllResults();
            $data['pinjaman_pending'] = $pinjamanModel->where('status', 'pending')->countAllResults();
            
            $db = \Config\Database::connect();
            $querySetor = $db->query("SELECT SUM(jumlah) as total FROM simpanan WHERE jenis_transaksi='setor'")->getRow();
            $queryTarik = $db->query("SELECT SUM(jumlah) as total FROM simpanan WHERE jenis_transaksi='tarik'")->getRow();
            $data['saldo_simpanan'] = ($querySetor->total ?? 0) - ($queryTarik->total ?? 0);

            $data['rincian_simpanan'] = $db->query("
                SELECT js.nama_simpanan, 
                       COALESCE(SUM(CASE WHEN s.jenis_transaksi = 'setor' THEN s.jumlah ELSE 0 END), 0) - 
                       COALESCE(SUM(CASE WHEN s.jenis_transaksi = 'tarik' THEN s.jumlah ELSE 0 END), 0) as total
                FROM jenis_simpanan js
                LEFT JOIN simpanan s ON s.jenis_simpanan_id = js.id
                GROUP BY js.id, js.nama_simpanan
            ")->getResultArray();

            $queryPinjaman = $db->query("SELECT SUM(jumlah_pinjaman) as total FROM pinjaman WHERE status='disetujui' OR status='lunas'")->getRow();
            $data['total_pinjaman'] = $queryPinjaman->total ?? 0;

            $rincianPinjamanQuery = $db->query("
                SELECT status, COALESCE(SUM(jumlah_pinjaman), 0) as total 
                FROM pinjaman 
                WHERE status IN ('disetujui', 'lunas') 
                GROUP BY status
            ")->getResultArray();
            
            $rincianPinjaman = ['Aktif' => 0, 'Lunas' => 0];
            foreach ($rincianPinjamanQuery as $rp) {
                if ($rp['status'] == 'disetujui') $rincianPinjaman['Aktif'] = $rp['total'];
                if ($rp['status'] == 'lunas') $rincianPinjaman['Lunas'] = $rp['total'];
            }
            $data['rincian_pinjaman'] = $rincianPinjaman;
            
            // Arus Kas Bulan Ini
            $currentMonthStart = date('Y-m-01');
            $currentMonthEnd   = date('Y-m-t');

            $qPrev = $db->query("SELECT SUM(CASE WHEN jenis='masuk' THEN nominal ELSE 0 END) - SUM(CASE WHEN jenis='keluar' THEN nominal ELSE 0 END) as total FROM kas_koperasi WHERE tanggal < ?", [$currentMonthStart])->getRow();
            $data['arus_kas_sebelumnya'] = $qPrev->total ?? 0;

            $qMasuk = $db->query("SELECT SUM(nominal) as total FROM kas_koperasi WHERE jenis='masuk' AND tanggal >= ? AND tanggal <= ?", [$currentMonthStart, $currentMonthEnd])->getRow();
            $data['arus_kas_masuk'] = $qMasuk->total ?? 0;

            $qKeluar = $db->query("SELECT SUM(nominal) as total FROM kas_koperasi WHERE jenis='keluar' AND tanggal >= ? AND tanggal <= ?", [$currentMonthStart, $currentMonthEnd])->getRow();
            $data['arus_kas_keluar'] = $qKeluar->total ?? 0;

            $data['arus_kas_akhir'] = $data['arus_kas_sebelumnya'] + $data['arus_kas_masuk'] - $data['arus_kas_keluar'];
            
            // Data untuk Chart Trend Arus Kas 6 Bulan Terakhir
            $trendLabels = [];
            $trendMasuk = [];
            $trendKeluar = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $m = date('Y-m', strtotime("-$i months"));
                $start = $m . '-01';
                $end = date('Y-m-t', strtotime($start));
                
                $trendLabels[] = date('M Y', strtotime($start));
                
                $tMasuk = $db->query("SELECT SUM(nominal) as total FROM kas_koperasi WHERE jenis='masuk' AND tanggal >= ? AND tanggal <= ?", [$start, $end])->getRow()->total ?? 0;
                $tKeluar = $db->query("SELECT SUM(nominal) as total FROM kas_koperasi WHERE jenis='keluar' AND tanggal >= ? AND tanggal <= ?", [$start, $end])->getRow()->total ?? 0;
                
                $trendMasuk[] = $tMasuk;
                $trendKeluar[] = $tKeluar;
            }
            $data['chart_trend_labels'] = json_encode($trendLabels);
            $data['chart_trend_masuk'] = json_encode($trendMasuk);
            $data['chart_trend_keluar'] = json_encode($trendKeluar);

            // Data untuk Pie Chart Komposisi Simpanan
            $pieLabels = [];
            $pieData = [];
            foreach ($data['rincian_simpanan'] as $rs) {
                if ($rs['total'] > 0) {
                    $pieLabels[] = $rs['nama_simpanan'];
                    $pieData[] = $rs['total'];
                }
            }
            $data['chart_pie_labels'] = json_encode($pieLabels);
            $data['chart_pie_data'] = json_encode($pieData);
            
        } else {
            $user_id = session()->get('user_id');
            $anggota = $anggotaModel->where('user_id', $user_id)->first();
            
            if ($anggota) {
                $db = \Config\Database::connect();
                $querySetor = $db->query("SELECT SUM(jumlah) as total FROM simpanan WHERE jenis_transaksi='setor' AND anggota_id=" . $anggota['id'])->getRow();
                $queryTarik = $db->query("SELECT SUM(jumlah) as total FROM simpanan WHERE jenis_transaksi='tarik' AND anggota_id=" . $anggota['id'])->getRow();
                $data['saldo_saya'] = ($querySetor->total ?? 0) - ($queryTarik->total ?? 0);

                $data['rincian_simpanan_saya'] = $db->query("
                    SELECT js.nama_simpanan, 
                           COALESCE(SUM(CASE WHEN s.jenis_transaksi = 'setor' THEN s.jumlah ELSE 0 END), 0) - 
                           COALESCE(SUM(CASE WHEN s.jenis_transaksi = 'tarik' THEN s.jumlah ELSE 0 END), 0) as total
                    FROM jenis_simpanan js
                    LEFT JOIN simpanan s ON s.jenis_simpanan_id = js.id AND s.anggota_id = " . $anggota['id'] . "
                    GROUP BY js.id, js.nama_simpanan
                ")->getResultArray();

                $pinjaman = $db->query("
                    SELECT p.*, COALESCE(SUM(a.jumlah_pokok), 0) as pokok_terbayar
                    FROM pinjaman p
                    LEFT JOIN angsuran a ON a.pinjaman_id = p.id
                    WHERE p.anggota_id = " . $anggota['id'] . " AND p.status = 'disetujui'
                    GROUP BY p.id
                ")->getResultArray();
                
                $data['pinjaman_aktif_list'] = $pinjaman;
            } else {
                $data['saldo_saya'] = 0;
                $data['rincian_simpanan_saya'] = [];
                $data['pinjaman_aktif_list'] = [];
            }
        }

        return view('dashboard/index', $data);
    }
}
