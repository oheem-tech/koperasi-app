<?php

namespace App\Controllers;

use App\Models\PengaturanModel;

class Laporan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    /**
     * Laporan Arus Kas: Simpanan Masuk, Pinjaman Keluar, Angsuran Masuk
     */
    public function kas()
    {
        if (!has_permission('view_laporan')) return redirect()->to('/dashboard');
        
        $db = \Config\Database::connect();
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');

        $simpananMasuk = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap, jenis_simpanan.nama_simpanan')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('jenis_transaksi', 'setor');
        if ($bulan !== 'all') {
            $simpananMasuk->where("DATE_FORMAT(tanggal_transaksi, '%Y-%m')", $bulan);
        }
        $simpananMasuk = $simpananMasuk->get()->getResultArray();

        // Simpanan keluar (tarik)
        $simpananKeluar = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap, jenis_simpanan.nama_simpanan')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('jenis_transaksi', 'tarik');
        if ($bulan !== 'all') {
            $simpananKeluar->where("DATE_FORMAT(tanggal_transaksi, '%Y-%m')", $bulan);
        }
        $simpananKeluar = $simpananKeluar->get()->getResultArray();

        // Pinjaman cair (disetujui atau lunas)
        $pinjamanCair = $db->table('pinjaman')
            ->select('pinjaman.*, anggota.nama_lengkap, anggota.no_anggota')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->whereIn('pinjaman.status', ['disetujui', 'lunas']);
        if ($bulan !== 'all') {
            $pinjamanCair->groupStart()
                ->where("DATE_FORMAT(tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH, '%Y-%m')", $bulan)
                ->orWhere("DATE_FORMAT(tanggal_pengajuan, '%Y-%m')", $bulan)
            ->groupEnd();
        }
        $pinjamanCair = $pinjamanCair->get()->getResultArray();

        // Angsuran masuk
        $angsuranMasuk = $db->table('angsuran')
            ->select('angsuran.*, anggota.nama_lengkap, anggota.no_anggota, pinjaman.jenis_pinjaman')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id');
        if ($bulan !== 'all') {
            $angsuranMasuk->where("DATE_FORMAT(tanggal_bayar, '%Y-%m')", $bulan);
        }
        $angsuranMasuk = $angsuranMasuk->get()->getResultArray();

        // Kas Operasional / Manual Masuk
        // Gunakan kategori='operasional' — lebih andal daripada string matching keterangan.
        // Ini memastikan "Setoran SHR", "Pelunasan Angsuran", dll. tidak masuk ke kelompok ini.
        $manualMasuk = $db->table('kas_koperasi')
            ->where('jenis', 'masuk')
            ->where('kategori', 'operasional');
        if ($bulan !== 'all') {
            $manualMasuk->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        }
        $manualMasuk = $manualMasuk->get()->getResultArray();

        // Kas Operasional / Manual Keluar
        $manualKeluar = $db->table('kas_koperasi')
            ->where('jenis', 'keluar')
            ->where('kategori', 'operasional');
        if ($bulan !== 'all') {
            $manualKeluar->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        }
        $manualKeluar = $manualKeluar->get()->getResultArray();

        $totalMasuk = array_sum(array_column($simpananMasuk, 'jumlah'))
                    + array_sum(array_column($angsuranMasuk, 'jumlah_bayar'))
                    + array_sum(array_column($manualMasuk, 'nominal'));

        $totalKeluar = array_sum(array_column($simpananKeluar, 'jumlah'))
                     + array_sum(array_column($pinjamanCair, 'jumlah_pinjaman'))
                     + array_sum(array_column($manualKeluar, 'nominal'));

        $data = [
            'title'          => 'Laporan Arus Kas',
            'bulan'          => $bulan,
            'simpananMasuk'  => $simpananMasuk,
            'simpananKeluar' => $simpananKeluar,
            'pinjamanCair'   => $pinjamanCair,
            'angsuranMasuk'  => $angsuranMasuk,
            'manualMasuk'    => $manualMasuk,
            'manualKeluar'   => $manualKeluar,
            'totalMasuk'     => $totalMasuk,
            'totalKeluar'    => $totalKeluar,
            'saldoBersih'    => $totalMasuk - $totalKeluar,
        ];
        $action = $this->request->getGet('action');
        if ($action == 'excel') {
            header("Content-type: application/vnd-ms-excel");
            $filenameBulan = $bulan === 'all' ? 'Semua_Waktu' : $bulan;
            header("Content-Disposition: attachment; filename=Laporan_Arus_Kas_{$filenameBulan}.xls");
            return view('laporan/print_kas', $data);
        } elseif ($action == 'print') {
            return view('laporan/print_kas', $data);
        }
        return view('laporan/kas', $data);
    }

    /**
     * Laporan SHU (Sisa Hasil Usaha) – berdasarkan jasa/bunga angsuran
     */
    public function shu()
    {
        if (!has_permission('view_laporan')) return redirect()->to('/dashboard');
        
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Ambil semua setting SHU (versi baru + backward compat)
        $settings = [];
        $rows = $this->pengaturanModel->whereIn('pengaturan_key', [
            'shu_jasa_modal', 'shu_jasa_anggota',
            'shu_pengurus_anggota', 'shu_pengawas', 'shu_pembina',
            'shu_dana_sosial', 'shu_dana_pendidikan', 'shu_cadangan',
            'bunga_pinjaman', 'shu_metode_modal'
        ])->findAll();
        foreach ($rows as $r) {
            if ($r['pengaturan_key'] === 'shu_metode_modal') {
                $settings[$r['pengaturan_key']] = $r['pengaturan_value'];
            } else {
                $settings[$r['pengaturan_key']] = (float)$r['pengaturan_value'];
            }
        }

        // 1. Ambil semua anggota
        $anggotaDB = $db->table('anggota')->select('id as anggota_id, no_anggota, nama_lengkap, jabatan')->get()->getResultArray();

        // 2. Total pendapatan jasa dari angsuran per anggota
        $totalJasaPerAnggota = $db->table('angsuran')
            ->select('anggota.id as anggota_id, SUM(angsuran.jumlah_jasa) as total_jasa')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where("YEAR(angsuran.tanggal_bayar)", $tahun)
            ->groupBy('anggota.id')
            ->get()->getResultArray();
        $jasaMap = array_column($totalJasaPerAnggota, 'total_jasa', 'anggota_id');
        $totalJasaGlobal = array_sum($jasaMap);

        // 3. Total simpanan historis per anggota (Kapital Modal)
        $metode_modal = $settings['shu_metode_modal'] ?? 'akumulasi_akhir';
        
        if ($metode_modal === 'rata_rata_berjalan') {
            $totalSimpananPerAnggota = $db->table('simpanan')
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
            $totalSimpananPerAnggota = $db->table('simpanan')
                ->select('anggota_id, SUM(CASE WHEN jenis_transaksi="setor" THEN jumlah ELSE -jumlah END) as saldo_simpanan')
                ->where("YEAR(tanggal_transaksi) <=", $tahun)
                ->groupBy('anggota_id')
                ->get()->getResultArray();
        }
        $saldoSimpananMap = array_column($totalSimpananPerAnggota, 'saldo_simpanan', 'anggota_id');
        $totalSimpananGlobal = array_sum($saldoSimpananMap);

        // 4. Kalkulasi Alokasi SHU Global
        $alokasiSHU = [
            'total_shu'          => $totalJasaGlobal,
            'jasa_modal'         => round($totalJasaGlobal * ($settings['shu_jasa_modal']        ?? 20) / 100),
            'jasa_anggota'       => round($totalJasaGlobal * ($settings['shu_jasa_anggota']      ?? 25) / 100),
            'pengurus_anggota'   => round($totalJasaGlobal * ($settings['shu_pengurus_anggota']  ?? 10) / 100),
            'pengawas'           => round($totalJasaGlobal * ($settings['shu_pengawas']          ?? 5)  / 100),
            'pembina'            => round($totalJasaGlobal * ($settings['shu_pembina']           ?? 5)  / 100),
            'dana_sosial'        => round($totalJasaGlobal * ($settings['shu_dana_sosial']     ?? 5)  / 100),
            'dana_pendidikan'    => round($totalJasaGlobal * ($settings['shu_dana_pendidikan'] ?? 5)  / 100),
            'dana_cadangan'      => round($totalJasaGlobal * ($settings['shu_cadangan']        ?? 20) / 100),
        ];
        $alokasiSHU['total_dialokasikan'] = array_sum($alokasiSHU) - $alokasiSHU['total_shu'];

        // 5. Hitung jumlah personel per jabatan
        $countJabatan = ['pengurus' => 0, 'pengawas' => 0, 'pembina' => 0];
        foreach ($anggotaDB as $a) {
            if (isset($countJabatan[$a['jabatan']])) $countJabatan[$a['jabatan']]++;
        }
        $jasaPerPengurus = $countJabatan['pengurus'] > 0 ? $alokasiSHU['pengurus_anggota'] / $countJabatan['pengurus'] : 0;
        $jasaPerPengawas = $countJabatan['pengawas'] > 0 ? $alokasiSHU['pengawas']         / $countJabatan['pengawas'] : 0;
        $jasaPerPembina  = $countJabatan['pembina']  > 0 ? $alokasiSHU['pembina']          / $countJabatan['pembina']  : 0;

        // 6. Hitung Rincian Hak Penerimaan SHU per Anggota Individu
        $shuPerAnggota = [];
        $persen_jasa_anggota = ($settings['shu_jasa_anggota'] ?? 25) / 100;
        $persen_jasa_modal   = ($settings['shu_jasa_modal']   ?? 20) / 100;

        foreach ($anggotaDB as $a) {
            $total_jasa_individu = $jasaMap[$a['anggota_id']] ?? 0;
            $saldo_anggota       = $saldoSimpananMap[$a['anggota_id']] ?? 0;

            $shu_jasa_angsuran = $total_jasa_individu * $persen_jasa_anggota;
            $shu_modal = ($totalSimpananGlobal > 0)
                ? ($saldo_anggota / $totalSimpananGlobal) * ($totalJasaGlobal * $persen_jasa_modal)
                : 0;

            // Distribusi Jasa Jabatan
            $shu_jabatan = 0;
            if ($a['jabatan'] === 'pengurus') $shu_jabatan = $jasaPerPengurus;
            elseif ($a['jabatan'] === 'pengawas') $shu_jabatan = $jasaPerPengawas;
            elseif ($a['jabatan'] === 'pembina') $shu_jabatan = $jasaPerPembina;

            $total_shu_diterima = round($shu_jasa_angsuran + $shu_modal + $shu_jabatan);

            // Hanya masukan anggota yang punya nominal (baik dari pinjaman, simpanan, atau dari jabatan)
            if ($total_jasa_individu > 0 || $saldo_anggota > 0 || $shu_jabatan > 0) {
                $a['total_jasa']       = $total_jasa_individu;
                $a['saldo_simpanan']   = $saldo_anggota;
                $a['shu_jasa_anggota'] = round($shu_jasa_angsuran);
                $a['shu_jasa_modal']   = round($shu_modal);
                $a['shu_jabatan']      = round($shu_jabatan);
                $a['shu_total']        = $total_shu_diterima;
                
                $shuPerAnggota[] = $a;
            }
        }

        $data = [
            'title'          => 'Laporan Sisa Hasil Usaha (SHU)',
            'tahun'          => $tahun,
            'shuPerAnggota'  => $shuPerAnggota,
            'alokasiSHU'     => $alokasiSHU,
            'settings'       => $settings,
        ];
        $action = $this->request->getGet('action');
        if ($action == 'excel') {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan_SHU_{$tahun}.xls");
            return view('laporan/print_shu', $data);
        } elseif ($action == 'print') {
            return view('laporan/print_shu', $data);
        }
        return view('laporan/shu', $data);
    }

    /**
     * Laporan Neraca (Balance Sheet) - Standar Koperasi
     */
    public function neraca()
    {
        if (!has_permission('view_laporan')) return redirect()->to('/dashboard');
        
        $db = \Config\Database::connect();

        $cutoff = $this->request->getGet('cutoff') ?? date('Y-m-d');

        // ================================================================
        // AKTIVA (ASSETS)
        // ================================================================

        // 1. Kas & Bank: Saldo kas terakhir dari Buku Kas Umum
        $kasMasuk = $db->table('kas_koperasi')->where('jenis', 'masuk')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $kasKeluar = $db->table('kas_koperasi')->where('jenis', 'keluar')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $kasBank = max(0, $kasMasuk - $kasKeluar);

        // 2. Piutang Pinjaman: Total dikucurkan − Total pokok yang sudah terbayar (semua status)
        $totalDikucurkan = $db->table('pinjaman')
            ->whereIn('status', ['disetujui', 'lunas'])
            ->where('DATE(tanggal_pengajuan) <=', $cutoff)
            ->selectSum('jumlah_pinjaman')
            ->get()->getRow()->jumlah_pinjaman ?? 0;
        $totalPokokTerbayar = $db->table('angsuran')
            ->where('DATE(tanggal_bayar) <=', $cutoff)
            ->selectSum('jumlah_pokok')
            ->get()->getRow()->jumlah_pokok ?? 0;
        $piutangPinjaman = max(0, $totalDikucurkan - $totalPokokTerbayar);

        $totalAktiva = $kasBank + $piutangPinjaman;

        // ================================================================
        // PENGELOMPOKAN JENIS SIMPANAN
        // ================================================================
        // Ambil data simpanan beserta keterangan jenis simpanannya
        $semuaSimpanan = $db->table('simpanan')
            ->select('simpanan.jumlah, simpanan.jenis_transaksi, jenis_simpanan.nama_simpanan')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('DATE(simpanan.tanggal_transaksi) <=', $cutoff)
            ->get()->getResultArray();

        $simpananPokokWajib = 0; // (EKUITAS)
        $simpananSukarela = 0;   // (KEWAJIBAN)
        
        foreach ($semuaSimpanan as $s) {
            $nama = strtolower($s['nama_simpanan']);
            $isPokokWajib = (strpos($nama, 'pokok') !== false || strpos($nama, 'wajib') !== false);
            
            if ($s['jenis_transaksi'] == 'setor') {
                if ($isPokokWajib) $simpananPokokWajib += $s['jumlah'];
                else $simpananSukarela += $s['jumlah'];
            } else {
                if ($isPokokWajib) $simpananPokokWajib -= $s['jumlah'];
                else $simpananSukarela -= $s['jumlah'];
            }
        }

        // ================================================================
        // PASIVA — KEWAJIBAN (LIABILITIES)
        // ================================================================

        // Kewajiban Kas Manual (Titipan / Hutang Luar)
        $kewajibanManualMasuk = $db->table('kas_koperasi')->where('kategori', 'kewajiban_hutang')->where('jenis', 'masuk')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $kewajibanManualKeluar = $db->table('kas_koperasi')->where('kategori', 'kewajiban_hutang')->where('jenis', 'keluar')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $kewajibanManual = $kewajibanManualMasuk - $kewajibanManualKeluar;

        // Kewajiban kepada anggota (Simpanan Sukarela / Hari Raya) yang bisa ditarik
        $totalKewajiban = max(0, $simpananSukarela) + $kewajibanManual;

        // Per anggota (rincian kewajiban simpanan sukarela untuk breakdown detail)
        $perAnggota = $db->table('simpanan as s')
            ->select('s.anggota_id, anggota.nama_lengkap, anggota.no_anggota,
                      SUM(CASE WHEN s.jenis_transaksi="setor" THEN s.jumlah ELSE -s.jumlah END) as saldo')
            ->join('anggota', 'anggota.id = s.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = s.jenis_simpanan_id')
            ->where('LOWER(jenis_simpanan.nama_simpanan) NOT LIKE', '%pokok%')
            ->where('LOWER(jenis_simpanan.nama_simpanan) NOT LIKE', '%wajib%')
            ->where('DATE(s.tanggal_transaksi) <=', $cutoff)
            ->groupBy('s.anggota_id')
            ->having('saldo !=', 0)
            ->get()->getResultArray();

        // ================================================================
        // PASIVA — EKUITAS (EQUITY)
        // ================================================================

        // 1. Modal Anggota (Hak Anggota): Simpanan Pokok & Wajib
        $modalAnggota = max(0, $simpananPokokWajib);

        // 2. SHU Belum Dibagi: Total jasa + denda angsuran + Kas Pendapatan/Biaya Lainnya
        $totalJasaDiterima  = $db->table('angsuran')->where('DATE(tanggal_bayar) <=', $cutoff)->selectSum('jumlah_jasa')->get()->getRow()->jumlah_jasa ?? 0;
        $totalDendaDiterima = $db->table('angsuran')->where('DATE(tanggal_bayar) <=', $cutoff)->selectSum('denda')->get()->getRow()->denda ?? 0;
        
        $pendapatanManualMasuk = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'masuk')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $biayaManualKeluar = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'keluar')->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $pendapatanLainnya = $pendapatanManualMasuk - $biayaManualKeluar;

        $shuBelumDibagi     = $totalJasaDiterima + $totalDendaDiterima + $pendapatanLainnya;

        // 3. Dana Cadangan: estimasi berdasarkan persentase setting dari total SHU historis
        $settingCadangan = (float)($db->table('pengaturan')->where('pengaturan_key', 'shu_cadangan')->get()->getRow()->pengaturan_value ?? 20);
        $danaCadangan = round($shuBelumDibagi * $settingCadangan / 100);

        // 4. Saldo Penyeimbang Lainnya (Modal Awal, Laba Ditahan, atau Hibah/Transaksi Kas Manual)
        // Karena aplikasi ini belum memiliki modul input ekuitas formal selain kas manual.
        $modalLainnya = $totalAktiva - $totalKewajiban - $modalAnggota - $shuBelumDibagi - $danaCadangan;

        $totalEkuitas = $modalAnggota + $shuBelumDibagi + $danaCadangan + $modalLainnya;
        $totalPasiva  = $totalKewajiban + $totalEkuitas;

        $data = [
            'title'               => 'Neraca Koperasi',
            'cutoff'              => $cutoff,
            // Aktiva
            'kasBank'             => $kasBank,
            'totalDikucurkan'     => $totalDikucurkan,
            'totalPokokTerbayar'  => $totalPokokTerbayar,
            'piutangPinjaman'     => $piutangPinjaman,
            'totalAktiva'         => $totalAktiva,
            // Kewajiban
            'simpananSukarela'    => $simpananSukarela,
            'kewajibanManual'     => $kewajibanManual,
            'totalKewajiban'      => $totalKewajiban,
            'perAnggota'          => $perAnggota,
            // Ekuitas
            'simpananPokokWajib'  => $modalAnggota,
            'totalJasaDiterima'   => $totalJasaDiterima,
            'totalDendaDiterima'  => $totalDendaDiterima,
            'pendapatanLainnya'   => $pendapatanLainnya,
            'shuBelumDibagi'      => $shuBelumDibagi,
            'danaCadangan'        => $danaCadangan,
            'settingCadangan'     => $settingCadangan,
            'modalLainnya'        => $modalLainnya,
            'totalEkuitas'        => $totalEkuitas,
            'totalPasiva'         => $totalPasiva,
        ];
        $action = $this->request->getGet('action');
        if ($action == 'excel') {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan_Neraca_Per_{$cutoff}.xls");
            return view('laporan/print_neraca', $data);
        } elseif ($action == 'print') {
            return view('laporan/print_neraca', $data);
        }
        return view('laporan/neraca', $data);
    }

    /**
     * Laporan Keuangan per Anggota (Rekapitulasi Simpanan & Pinjaman)
     */
    public function anggota()
    {
        if (!has_permission('view_laporan')) return redirect()->to('/dashboard');
        
        $db = \Config\Database::connect();
        $kelompokModel = new \App\Models\KelompokModel();

        // Filter kelompok (hanya aktif di versi PRO)
        $filterKelompok = (is_premium() && $this->request->getGet('kelompok')) 
                         ? $this->request->getGet('kelompok') 
                         : 'all';

        // 1. Ambil data semua anggota (hanya yang aktif)
        $queryAnggota = $db->table('anggota')
            ->select('id, no_anggota, nama_lengkap, status, kelompok')
            ->where('status', 'aktif')
            ->orderBy('kelompok', 'ASC')
            ->orderBy('nama_lengkap', 'ASC');
        if ($filterKelompok !== 'all') {
            $queryAnggota->where('kelompok', $filterKelompok);
        }
        $anggotaDB = $queryAnggota->get()->getResultArray();

        // 2. Ambil seluruh transaksi simpanan untuk dipetakan
        $simpananDB = $db->table('simpanan')
            ->select('simpanan.anggota_id, simpanan.jumlah, simpanan.jenis_transaksi, jenis_simpanan.nama_simpanan')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->get()->getResultArray();

        // 3. Ambil data pinjaman yang masih disetujui (belum lunas)
        $pinjamanDB = $db->table('pinjaman')
            ->select('id, anggota_id, jumlah_pinjaman')
            ->where('status', 'disetujui')
            ->get()->getResultArray();

        // 4. Ambil angsuran pokok khusus untuk pinjaman yang disetujui
        $pinjamanIds = array_column($pinjamanDB, 'id');
        $angsuranMap = [];
        if (!empty($pinjamanIds)) {
            $angsuranDB = $db->table('angsuran')
                ->select('pinjaman_id, SUM(jumlah_pokok) as total_dibayar')
                ->whereIn('pinjaman_id', $pinjamanIds)
                ->groupBy('pinjaman_id')
                ->get()->getResultArray();
            $angsuranMap = array_column($angsuranDB, 'total_dibayar', 'pinjaman_id');
        }

        // Kelompokkan sisa pinjaman per anggota dan per ID Pinjaman
        $sisaPinjamanPerAnggota = [];
        foreach ($pinjamanDB as $p) {
            $a_id = $p['anggota_id'];
            $p_id = $p['id'];
            $totalDibayar = $angsuranMap[$p_id] ?? 0;
            $sisa = max(0, $p['jumlah_pinjaman'] - $totalDibayar);
            
            if ($sisa > 0) {
                if (!isset($sisaPinjamanPerAnggota[$a_id])) {
                    $sisaPinjamanPerAnggota[$a_id] = [];
                }
                $sisaPinjamanPerAnggota[$a_id][] = [
                    'pinjaman_id' => $p_id,
                    'sisa'        => $sisa
                ];
            }
        }

        // Proses mapping simpanan
        $simpananMap = [];
        foreach ($simpananDB as $s) {
            $a_id = $s['anggota_id'];
            if (!isset($simpananMap[$a_id])) {
                $simpananMap[$a_id] = ['pokok' => 0, 'wajib' => 0, 'sukarela' => 0];
            }
            
            $nama = strtolower($s['nama_simpanan']);
            $mutasi = ($s['jenis_transaksi'] == 'setor') ? $s['jumlah'] : -$s['jumlah'];
            
            if (strpos($nama, 'pokok') !== false) {
                $simpananMap[$a_id]['pokok'] += $mutasi;
            } elseif (strpos($nama, 'wajib') !== false) {
                $simpananMap[$a_id]['wajib'] += $mutasi;
            } else {
                $simpananMap[$a_id]['sukarela'] += $mutasi;
            }
        }

        // Susun laporan rekap
        $rekapAnggota = [];
        $total_pokok = 0;
        $total_wajib = 0;
        $total_sukarela = 0;
        $total_sisa_pinjaman = 0;

        foreach ($anggotaDB as $a) {
            $id = $a['id'];
            
            $pokok = $simpananMap[$id]['pokok'] ?? 0;
            $wajib = $simpananMap[$id]['wajib'] ?? 0;
            $sukarela = $simpananMap[$id]['sukarela'] ?? 0;
            
            $listPinjaman = $sisaPinjamanPerAnggota[$id] ?? [];
            $totalSisaIni = array_sum(array_column($listPinjaman, 'sisa'));
            
            $rekapAnggota[] = [
                'no_anggota'    => $a['no_anggota'],
                'nama_lengkap'  => $a['nama_lengkap'],
                'status'        => $a['status'],
                'pokok'         => $pokok,
                'wajib'         => $wajib,
                'sukarela'      => $sukarela,
                'list_pinjaman' => $listPinjaman,
                'sisa_pinjaman' => $totalSisaIni
            ];
            
            $total_pokok += $pokok;
            $total_wajib += $wajib;
            $total_sukarela += $sukarela;
            $total_sisa_pinjaman += $totalSisaIni;
        }

        $data = [
            'title'               => 'Laporan Keuangan per Anggota',
            'rekapAnggota'        => $rekapAnggota,
            'total_pokok'         => $total_pokok,
            'total_wajib'         => $total_wajib,
            'total_sukarela'      => $total_sukarela,
            'total_sisa_pinjaman' => $total_sisa_pinjaman,
            'kelompokList'        => $kelompokModel->orderBy('nama_kelompok','ASC')->findAll(),
            'filterKelompok'      => $filterKelompok,
        ];

        $action = $this->request->getGet('action');
        if ($action == 'excel') {
            header("Content-type: application/vnd-ms-excel");
            $suffix = ($filterKelompok !== 'all') ? '_'.str_replace(' ','_',$filterKelompok) : '';
            header("Content-Disposition: attachment; filename=Laporan_Keuangan_Anggota{$suffix}_".date('Ymd').".xls");
            return view('laporan/print_anggota', $data);
        } elseif ($action == 'print') {
            return view('laporan/print_anggota', $data);
        }
        
        return view('laporan/anggota', $data);
    }
}
