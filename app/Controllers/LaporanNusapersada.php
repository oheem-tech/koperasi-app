<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class LaporanNusapersada extends BaseController
{
    protected $db;

    public function __construct()
    {
        helper(['auth', 'pengaturan', 'log']);
        $this->db = \Config\Database::connect();
        // Proteksi hak akses laporan khusus Nusapersada
        if (!has_permission('view_laporan')) {
            session()->setFlashdata('error', 'Akses Ditolak. Anda tidak memiliki izin untuk melihat laporan ini.');
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * Helper kalkulasi SHU global dan saldo modal/jasa per anggota
     * mengikuti formula standar koperasi & pengaturan master.
     */
    private function getShuCalculation($tahun = null)
    {
        $tahun = $tahun ?? ($this->request->getGet('tahun') ?? date('Y'));

        // Ambil semua pengaturan
        $settings = [];
        $rows = $this->db->table('pengaturan')->get()->getResultArray();
        foreach ($rows as $r) {
            if ($r['pengaturan_key'] === 'shu_metode_modal') {
                $settings[$r['pengaturan_key']] = $r['pengaturan_value'];
            } elseif (is_numeric($r['pengaturan_value'])) {
                $settings[$r['pengaturan_key']] = (float)$r['pengaturan_value'];
            } else {
                $settings[$r['pengaturan_key']] = $r['pengaturan_value'];
            }
        }

        // 1. Data Anggota (termasuk yang non-aktif jika berkontribusi pada tahun berjalan)
        $anggotaDB = $this->db->table('anggota')
            ->select('id, id as anggota_id, no_anggota, nama_lengkap, jabatan, status')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        // 2. Total Pendapatan Jasa dari Angsuran per Anggota
        $totalJasaPerAnggota = $this->db->table('angsuran')
            ->select('anggota.id as anggota_id, SUM(angsuran.jumlah_jasa) as total_jasa')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where("YEAR(angsuran.tanggal_bayar)", $tahun)
            ->groupBy('anggota.id')
            ->get()->getResultArray();
        $jasaMap = array_column($totalJasaPerAnggota, 'total_jasa', 'anggota_id');
        $totalJasaGlobal = array_sum(array_filter($jasaMap, function($v) { return $v > 0; }));

        // 3. Total Simpanan Historis per Anggota (Kapital Modal)
        $metode_modal = $settings['shu_metode_modal'] ?? 'akumulasi_akhir';
        if ($metode_modal === 'rata_rata_berjalan') {
            $totalSimpananPerAnggota = $this->db->table('simpanan')
                ->select("anggota_id, SUM(
                    CASE 
                        WHEN YEAR(tanggal_transaksi) < $tahun THEN 
                            (CASE WHEN jenis_transaksi='setor' THEN jumlah ELSE -jumlah END) * 12
                        WHEN YEAR(tanggal_transaksi) = $tahun THEN
                            (CASE WHEN jenis_transaksi='setor' THEN jumlah ELSE -jumlah END) * (13 - MONTH(tanggal_transaksi))
                        ELSE 0 
                    END
                ) / 12 as saldo_simpanan")
                ->where("YEAR(tanggal_transaksi) <=", $tahun)
                ->groupBy('anggota_id')
                ->get()->getResultArray();
        } else {
            $totalSimpananPerAnggota = $this->db->table('simpanan')
                ->select('anggota_id, SUM(CASE WHEN jenis_transaksi="setor" THEN jumlah ELSE -jumlah END) as saldo_simpanan')
                ->where("YEAR(tanggal_transaksi) <=", $tahun)
                ->groupBy('anggota_id')
                ->get()->getResultArray();
        }
        $saldoSimpananMap = [];
        foreach ($totalSimpananPerAnggota as $row) {
            $saldoSimpananMap[$row['anggota_id']] = max(0, (float)$row['saldo_simpanan']);
        }
        $totalSimpananGlobal = array_sum($saldoSimpananMap);

        // 4. Denda & Pendapatan Kas Operasional
        $totalDendaGlobal = $this->db->table('angsuran')->where("YEAR(tanggal_bayar)", $tahun)->selectSum('denda')->get()->getRow()->denda ?? 0;
        $pendapatanMasukGlobal = $this->db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'masuk')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $biayaKeluarGlobal = $this->db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'keluar')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;

        $pendapatan = (float)$totalJasaGlobal + (float)$totalDendaGlobal + (float)$pendapatanMasukGlobal;
        $biaya = (float)$biayaKeluarGlobal;
        $totalSHU = max(0, $pendapatan - $biaya);

        // 5. Pools
        $p_modal = (float)($settings['shu_jasa_modal'] ?? 30);
        $p_anggota = (float)($settings['shu_jasa_anggota'] ?? 20);

        $poolJasaModal = $totalSHU * ($p_modal / 100);
        $poolJasaAnggota = $totalSHU * ($p_anggota / 100);

        return [
            'tahun'               => $tahun,
            'settings'            => $settings,
            'anggotaDB'           => $anggotaDB,
            'jasaMap'             => $jasaMap,
            'totalJasaGlobal'     => (float)$totalJasaGlobal,
            'saldoSimpananMap'    => $saldoSimpananMap,
            'totalSimpananGlobal' => (float)$totalSimpananGlobal,
            'totalPendapatan'     => $pendapatan,
            'totalBiaya'          => $biaya,
            'totalSHU'            => $totalSHU,
            'p_modal'             => $p_modal,
            'p_anggota'           => $p_anggota,
            'poolJasaModal'       => $poolJasaModal,
            'poolJasaAnggota'     => $poolJasaAnggota,
        ];
    }

    public function jasa_pinjaman_dan_simpanan()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $calc = $this->getShuCalculation($tahun);

        $data = [];
        $totalSimpanan = 0; $totalPinjaman = 0; $totalSHU = 0; $totalTerima = 0;

        foreach ($calc['anggotaDB'] as $a) {
            $jasaIndividu = (float)($calc['jasaMap'][$a['id']] ?? 0);
            $saldoAnggota = (float)($calc['saldoSimpananMap'][$a['id']] ?? 0);

            // Jasa proporsional / pro-rata standar koperasi
            $jasaSimpanan = ($calc['totalSimpananGlobal'] > 0 && $saldoAnggota > 0)
                ? round(($saldoAnggota / $calc['totalSimpananGlobal']) * $calc['poolJasaModal'])
                : 0;

            $jasaPinjamanHitung = ($calc['totalJasaGlobal'] > 0 && $jasaIndividu > 0)
                ? round(($jasaIndividu / $calc['totalJasaGlobal']) * $calc['poolJasaAnggota'])
                : 0;

            $shuToko = 0;

            if ($jasaSimpanan > 0 || $jasaPinjamanHitung > 0 || $shuToko > 0) {
                $terima = $jasaSimpanan + $jasaPinjamanHitung + $shuToko;
                $data[] = [
                    $a['nama_lengkap'],
                    $jasaSimpanan > 0 ? number_format($jasaSimpanan, 0, ',', '.') : "-",
                    $jasaPinjamanHitung > 0 ? number_format($jasaPinjamanHitung, 0, ',', '.') : "-",
                    $shuToko > 0 ? number_format($shuToko, 0, ',', '.') : "-",
                    number_format($terima, 0, ',', '.')
                ];
                $totalSimpanan += $jasaSimpanan;
                $totalPinjaman += $jasaPinjamanHitung;
                $totalSHU += $shuToko;
                $totalTerima += $terima;
            }
        }

        return view('laporan_nusapersada/laporan_jasa_pinjaman_dan_simpanan', [
            'tahun'  => $tahun,
            'data'   => $data,
            'totals' => [
                number_format($totalSimpanan, 0, ',', '.'),
                number_format($totalPinjaman, 0, ',', '.'),
                number_format($totalSHU, 0, ',', '.'),
                number_format($totalTerima, 0, ',', '.')
            ]
        ]);
    }

    public function pembagian_jasa_pinjaman()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $calc = $this->getShuCalculation($tahun);

        $data2 = [];
        $totals = array_fill(0, 14, 0);

        foreach ($calc['anggotaDB'] as $a) {
            $row = [$a['nama_lengkap']];
            $totalJasaMember = 0;
            $hasData = false;

            for ($m = 1; $m <= 12; $m++) {
                $jasaBulanQuery = $this->db->query("SELECT SUM(a.jumlah_jasa) as total FROM angsuran a JOIN pinjaman p ON p.id = a.pinjaman_id WHERE p.anggota_id = ? AND MONTH(a.tanggal_bayar) = ? AND YEAR(a.tanggal_bayar) = ?", [$a['id'], $m, $tahun])->getRow();
                $jasaBulan = $jasaBulanQuery ? (float)$jasaBulanQuery->total : 0;

                if ($jasaBulan > 0) {
                    $row[] = number_format($jasaBulan, 0, ',', '.');
                    $totalJasaMember += $jasaBulan;
                    $totals[$m - 1] += $jasaBulan;
                    $hasData = true;
                } else {
                    $row[] = "-";
                }
            }

            if ($hasData || ($calc['jasaMap'][$a['id']] ?? 0) > 0) {
                // Jasa untuk Anggota pro-rata dari pool jasa anggota
                $jasaFinal = ($calc['totalJasaGlobal'] > 0 && $totalJasaMember > 0)
                    ? round(($totalJasaMember / $calc['totalJasaGlobal']) * $calc['poolJasaAnggota'])
                    : 0;

                $row[] = number_format($totalJasaMember, 0, ',', '.');
                $row[] = number_format($jasaFinal, 0, ',', '.');
                $totals[12] += $totalJasaMember;
                $totals[13] += $jasaFinal;
                $data2[] = $row;
            }
        }

        $formattedTotals = array_map(function ($val) {
            return number_format($val, 0, ',', '.');
        }, $totals);

        return view('laporan_nusapersada/laporan_pembagian_jasa_pinjaman', [
            'tahun'  => $tahun,
            'data2'  => $data2,
            'totals' => $formattedTotals
        ]);
    }

    public function perhitungan_jasa_simpanan()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $calc = $this->getShuCalculation($tahun);

        $data = [];
        $totalPokok = 0; $totalWajib = 0; $totalSukarela = 0; $totalJumlah = 0; $totalJasa = 0;
        $idx = 1;

        foreach ($calc['anggotaDB'] as $a) {
            $pokok = (float)($this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 1 AND YEAR(tanggal_transaksi) <= ?", [$a['id'], $tahun])->getRow()->total ?? 0);
            $wajib = (float)($this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 2 AND YEAR(tanggal_transaksi) <= ?", [$a['id'], $tahun])->getRow()->total ?? 0);
            $sukarela = (float)($this->db->query("SELECT SUM(IF(jenis_transaksi='setor', jumlah, -jumlah)) as total FROM simpanan WHERE anggota_id = ? AND jenis_simpanan_id = 3 AND YEAR(tanggal_transaksi) <= ?", [$a['id'], $tahun])->getRow()->total ?? 0);

            $jumlah = $pokok + $wajib + $sukarela;
            $saldoAnggota = (float)($calc['saldoSimpananMap'][$a['id']] ?? 0);

            // Jasa diterima pro-rata dari pool jasa modal
            $jasa = ($calc['totalSimpananGlobal'] > 0 && $saldoAnggota > 0)
                ? round(($saldoAnggota / $calc['totalSimpananGlobal']) * $calc['poolJasaModal'])
                : 0;

            if ($jumlah > 0 || $jasa > 0) {
                $data[] = [
                    $idx++,
                    $a['nama_lengkap'],
                    $pokok > 0 ? number_format($pokok, 0, ',', '.') : "-",
                    $wajib > 0 ? number_format($wajib, 0, ',', '.') : "-",
                    $sukarela > 0 ? number_format($sukarela, 0, ',', '.') : "-",
                    number_format($jumlah, 0, ',', '.'),
                    number_format($jasa, 0, ',', '.')
                ];
                $totalPokok += $pokok;
                $totalWajib += $wajib;
                $totalSukarela += $sukarela;
                $totalJumlah += $jumlah;
                $totalJasa += $jasa;
            }
        }

        $totals = [
            number_format($totalPokok, 0, ',', '.'),
            number_format($totalWajib, 0, ',', '.'),
            number_format($totalSukarela, 0, ',', '.'),
            number_format($totalJumlah, 0, ',', '.'),
            number_format($totalJasa, 0, ',', '.')
        ];

        return view('laporan_nusapersada/laporan_perhitungan_jasa_simpanan', [
            'tahun'  => $tahun,
            'data'   => $data,
            'totals' => $totals
        ]);
    }

    public function kas_rat()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Pemasukan
        $p_pokok = []; $p_wajib = []; $p_sukarela = [];
        $p_angsuran = []; $p_jasa = []; $p_pelunasan = [];

        // Pengeluaran
        $k_pinjaman = [];
        $k_keluar = [];

        for ($i = 1; $i <= 12; $i++) {
            // Simpanan Pokok, Wajib, Sukarela
            $p_pokok[$i] = (float)($this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=1 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0);
            $p_wajib[$i] = (float)($this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=2 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0);
            $p_sukarela[$i] = (float)($this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='setor' AND jenis_simpanan_id=3 AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0);

            $angsuranData = $this->db->query("SELECT SUM(jumlah_pokok) as pokok, SUM(jumlah_jasa) as jasa FROM angsuran WHERE MONTH(tanggal_bayar)=? AND YEAR(tanggal_bayar)=?", [$i, $tahun])->getRow();
            $p_angsuran[$i] = (float)($angsuranData->pokok ?? 0);
            $p_jasa[$i] = (float)($angsuranData->jasa ?? 0);
            $p_pelunasan[$i] = 0;

            $k_pinjaman[$i] = (float)($this->db->query("SELECT SUM(jumlah_pinjaman) as t FROM pinjaman WHERE status IN ('disetujui', 'lunas') AND MONTH(tanggal_pengajuan)=? AND YEAR(tanggal_pengajuan)=?", [$i, $tahun])->getRow()->t ?? 0);
            $k_keluar[$i] = (float)($this->db->query("SELECT SUM(jumlah) as t FROM simpanan WHERE jenis_transaksi='tarik' AND MONTH(tanggal_transaksi)=? AND YEAR(tanggal_transaksi)=?", [$i, $tahun])->getRow()->t ?? 0);
        }

        return view('laporan_nusapersada/laporan_kas_rat', [
            'tahun'       => $tahun,
            'p_pokok'     => $p_pokok,
            'p_wajib'     => $p_wajib,
            'p_sukarela'  => $p_sukarela,
            'p_angsuran'  => $p_angsuran,
            'p_jasa'      => $p_jasa,
            'p_pelunasan' => $p_pelunasan,
            'k_pinjaman'  => $k_pinjaman,
            'k_keluar'    => $k_keluar
        ]);
    }

    public function perhitungan_shu()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $calc = $this->getShuCalculation($tahun);

        $pendapatan = $calc['totalPendapatan'];
        $biaya = $calc['totalBiaya'];
        $shu_bersih = $calc['totalSHU'];

        $p_cadangan = (float)($calc['settings']['shu_cadangan'] ?? 10);
        $p_modal = (float)($calc['settings']['shu_jasa_modal'] ?? 30);
        $p_anggota = (float)($calc['settings']['shu_jasa_anggota'] ?? 20);
        $p_total_anggota = $p_modal + $p_anggota;
        
        // Porsi pengurus sesuai pengaturan master (atau alokasi pengurus)
        $p_pengurus = (float)($calc['settings']['shu_pengurus_anggota'] ?? 16);

        $p_ketua = (float)($calc['settings']['shu_pengurus_ketua'] ?? 34);
        $p_bendahara = (float)($calc['settings']['shu_pengurus_bendahara'] ?? 33);
        $p_sekretaris = (float)($calc['settings']['shu_pengurus_sekretaris'] ?? 33);
        $p_bp1 = (float)($calc['settings']['shu_pengurus_bp1'] ?? 0);
        $p_bp2 = (float)($calc['settings']['shu_pengurus_bp2'] ?? 0);

        $val_cadangan = round($shu_bersih * ($p_cadangan / 100));
        $val_anggota = round($shu_bersih * ($p_total_anggota / 100));
        $val_pengurus = round($shu_bersih * ($p_pengurus / 100));

        $val_simpanan = round($shu_bersih * ($p_modal / 100));
        $val_pinjaman = round($shu_bersih * ($p_anggota / 100));

        $data = [
            'tahun'           => $tahun,
            'pendapatan'      => $pendapatan,
            'biaya'           => $biaya,
            'shu_bersih'      => $shu_bersih,
            'p_cadangan'      => $p_cadangan,
            'p_total_anggota' => $p_total_anggota,
            'p_pengurus'      => $p_pengurus,
            'p_ketua'         => $p_ketua,
            'p_bendahara'     => $p_bendahara,
            'p_sekretaris'    => $p_sekretaris,
            'p_bp1'           => $p_bp1,
            'p_bp2'           => $p_bp2,
            'val_cadangan'    => $val_cadangan,
            'val_anggota'     => $val_anggota,
            'val_pengurus'    => $val_pengurus,
            'val_ketua'       => round($val_pengurus * ($p_ketua / 100)),
            'val_bendahara'   => round($val_pengurus * ($p_bendahara / 100)),
            'val_sekretaris'  => round($val_pengurus * ($p_sekretaris / 100)),
            'val_bp1'         => round($val_pengurus * ($p_bp1 / 100)),
            'val_bp2'         => round($val_pengurus * ($p_bp2 / 100)),
            'p_modal'         => $p_modal,
            'p_anggota'       => $p_anggota,
            'val_simpanan'    => $val_simpanan,
            'val_pinjaman'    => $val_pinjaman,
        ];

        return view('laporan_nusapersada/laporan_perhitungan_shu', $data);
    }
}
