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
        $bulanParam = $this->request->getGet('bulan');
        $filterType = $this->request->getGet('filter_type');
        $filterBulan = $this->request->getGet('filter_bulan');
        $filterTahun = $this->request->getGet('filter_tahun');
        $filterSemester = $this->request->getGet('filter_semester');

        if ($bulanParam === 'all') {
            $periode = 'all';
            $periodeDesc = 'Semua Waktu';
        } else if ($filterType) {
            $periode = [
                'type' => $filterType,
                'tahun' => $filterTahun,
                'bulan' => $filterBulan,
                'semester' => $filterSemester
            ];
            
            if ($filterType === 'bulan') {
                $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                $periodeDesc = $namaBulan[str_pad($filterBulan, 2, '0', STR_PAD_LEFT)] . ' ' . $filterTahun;
            } else if ($filterType === 'semester') {
                $periodeDesc = 'Semester ' . $filterSemester . ' Tahun ' . $filterTahun;
            } else {
                $periodeDesc = 'Tahun ' . $filterTahun;
            }
        } else {
            // Default to current month if no filter submitted
            if ($bulanParam && preg_match('/^\d{4}-\d{2}$/', $bulanParam)) {
                $periode = [
                    'type' => 'bulan',
                    'tahun' => substr($bulanParam, 0, 4),
                    'bulan' => substr($bulanParam, 5, 2)
                ];
            } else {
                $periode = [
                    'type' => 'bulan',
                    'tahun' => date('Y'),
                    'bulan' => date('m')
                ];
            }
            $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            $periodeDesc = $namaBulan[str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT)] . ' ' . $periode['tahun'];
        }

        $applyFilter = function($builder, $colTanggal) use ($periode) {
            if ($periode === 'all') return;
            if ($periode['type'] === 'bulan') {
                $bulanStr = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
                $builder->where("DATE_FORMAT($colTanggal, '%Y-%m')", $bulanStr);
            } else if ($periode['type'] === 'semester') {
                $builder->where("YEAR($colTanggal)", $periode['tahun']);
                if ($periode['semester'] == '1') {
                    $builder->where("MONTH($colTanggal) <=", 6);
                } else {
                    $builder->where("MONTH($colTanggal) >", 6);
                }
            } else if ($periode['type'] === 'tahun') {
                $builder->where("YEAR($colTanggal)", $periode['tahun']);
            }
        };

        $simpananMasuk = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap, jenis_simpanan.nama_simpanan')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('jenis_transaksi', 'setor');
        $applyFilter($simpananMasuk, 'tanggal_transaksi');
        $simpananMasuk = $simpananMasuk->get()->getResultArray();

        // Simpanan keluar (tarik)
        $simpananKeluar = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap, jenis_simpanan.nama_simpanan')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('jenis_transaksi', 'tarik');
        $applyFilter($simpananKeluar, 'tanggal_transaksi');
        $simpananKeluar = $simpananKeluar->get()->getResultArray();

        // Pinjaman cair (disetujui atau lunas)
        $pinjamanCair = $db->table('pinjaman')
            ->select('pinjaman.*, anggota.nama_lengkap, anggota.no_anggota, kas_koperasi.tanggal as tanggal_cair')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->join('kas_koperasi', "kas_koperasi.kategori = 'pinjaman' AND kas_koperasi.jenis = 'keluar' AND kas_koperasi.nominal = pinjaman.jumlah_pinjaman AND kas_koperasi.keterangan LIKE CONCAT('%', anggota.nama_lengkap, '%')", 'left')
            ->whereIn('pinjaman.status', ['disetujui', 'lunas']);
        if ($periode !== 'all') {
            $pinjamanCair->groupStart();
            if ($periode['type'] === 'bulan') {
                $b = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
                $pinjamanCair->where("DATE_FORMAT(COALESCE(kas_koperasi.tanggal, tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH), '%Y-%m')", $b);
            } else if ($periode['type'] === 'semester') {
                $y = $periode['tahun'];
                $mOp = $periode['semester'] == '1' ? '<=' : '>';
                $pinjamanCair->where("YEAR(COALESCE(kas_koperasi.tanggal, tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH)) = $y AND MONTH(COALESCE(kas_koperasi.tanggal, tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH)) $mOp 6");
            } else if ($periode['type'] === 'tahun') {
                $pinjamanCair->where("YEAR(COALESCE(kas_koperasi.tanggal, tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH))", $periode['tahun']);
            }
            $pinjamanCair->groupEnd();
        }
        $pinjamanCair = $pinjamanCair->get()->getResultArray();

        // Angsuran masuk
        $angsuranMasuk = $db->table('angsuran')
            ->select('angsuran.*, anggota.nama_lengkap, anggota.no_anggota, pinjaman.jenis_pinjaman')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id');
        $applyFilter($angsuranMasuk, 'tanggal_bayar');
        $angsuranMasuk = $angsuranMasuk->get()->getResultArray();

        // Kas Operasional / Manual Masuk
        $manualMasuk = $db->table('kas_koperasi')
            ->where('jenis', 'masuk')
            ->whereNotIn('kategori', ['simpanan', 'angsuran', 'pinjaman']);
        $applyFilter($manualMasuk, 'tanggal');
        $manualMasuk = $manualMasuk->get()->getResultArray();

        // Kas Operasional / Manual Keluar
        $manualKeluar = $db->table('kas_koperasi')
            ->where('jenis', 'keluar')
            ->whereNotIn('kategori', ['simpanan', 'angsuran', 'pinjaman']);
        $applyFilter($manualKeluar, 'tanggal');
        $manualKeluar = $manualKeluar->get()->getResultArray();

        // Real Kas dari Buku Kas Umum untuk garansi sinkronisasi 100%
        $semuaKasMasuk = $db->table('kas_koperasi')->where('jenis', 'masuk');
        $applyFilter($semuaKasMasuk, 'tanggal');
        $totalMasukReal = $semuaKasMasuk->selectSum('nominal')->get()->getRow()->nominal ?? 0;

        $semuaKasKeluar = $db->table('kas_koperasi')->where('jenis', 'keluar');
        $applyFilter($semuaKasKeluar, 'tanggal');
        $totalKeluarReal = $semuaKasKeluar->selectSum('nominal')->get()->getRow()->nominal ?? 0;

        $totalMasukKomponen = array_sum(array_column($simpananMasuk, 'jumlah'))
                            + array_sum(array_column($angsuranMasuk, 'jumlah_bayar'))
                            + array_sum(array_column($manualMasuk, 'nominal'));

        $totalKeluarKomponen = array_sum(array_column($simpananKeluar, 'jumlah'))
                             + array_sum(array_column($pinjamanCair, 'jumlah_pinjaman'))
                             + array_sum(array_column($manualKeluar, 'nominal'));

        // Inject selisih (data yatim/terhapus sebagian) agar match dengan Buku Kas
        $selisihMasuk = $totalMasukReal - $totalMasukKomponen;
        if (abs($selisihMasuk) > 0) {
            $manualMasuk[] = [
                'tanggal' => date('Y-m-d'),
                'keterangan' => 'Penyesuaian Sistem (Data yatim/tidak sinkron)',
                'nominal' => $selisihMasuk
            ];
        }

        $selisihKeluar = $totalKeluarReal - $totalKeluarKomponen;
        if (abs($selisihKeluar) > 0) {
            $manualKeluar[] = [
                'tanggal' => date('Y-m-d'),
                'keterangan' => 'Penyesuaian Sistem (Data yatim/tidak sinkron)',
                'nominal' => $selisihKeluar
            ];
        }

        $totalMasuk = $totalMasukReal;
        $totalKeluar = $totalKeluarReal;

        // Hitung Saldo Awal
        $awalSaldo = 0;
        if ($periode !== 'all') {
            if ($periode['type'] === 'bulan') {
                $tanggalAwal = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT) . '-01';
            } else if ($periode['type'] === 'semester') {
                if ($periode['semester'] == '1') {
                    $tanggalAwal = $periode['tahun'] . '-01-01';
                } else {
                    $tanggalAwal = $periode['tahun'] . '-07-01';
                }
            } else if ($periode['type'] === 'tahun') {
                $tanggalAwal = $periode['tahun'] . '-01-01';
            }
            
            $masukSebelum = $db->table('kas_koperasi')->where("DATE(tanggal) <", $tanggalAwal)->where('jenis', 'masuk')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
            $keluarSebelum = $db->table('kas_koperasi')->where("DATE(tanggal) <", $tanggalAwal)->where('jenis', 'keluar')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
            $awalSaldo = $masukSebelum - $keluarSebelum;
        }

        $saldoBersih = $totalMasuk - $totalKeluar;
        $saldoAkhir = $awalSaldo + $saldoBersih;

        $data = [
            'title'          => 'Laporan Arus Kas',
            'periode'        => $periode,
            'periodeDesc'    => $periodeDesc,
            'simpananMasuk'  => $simpananMasuk,
            'simpananKeluar' => $simpananKeluar,
            'pinjamanCair'   => $pinjamanCair,
            'angsuranMasuk'  => $angsuranMasuk,
            'manualMasuk'    => $manualMasuk,
            'manualKeluar'   => $manualKeluar,
            'totalMasuk'     => $totalMasuk,
            'totalKeluar'    => $totalKeluar,
            'saldoBersih'    => $saldoBersih,
            'awalSaldo'      => $awalSaldo,
            'saldoAkhir'     => $saldoAkhir,
        ];
        $action = $this->request->getGet('action');
        if ($action == 'excel') {
            header("Content-type: application/vnd-ms-excel");
            $filenameBulan = str_replace(' ', '_', $periodeDesc);
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

        // 2b. Denda dan Pendapatan Lainnya untuk dimasukkan ke Total SHU
        $totalDendaGlobal = $db->table('angsuran')->where("YEAR(tanggal_bayar)", $tahun)->selectSum('denda')->get()->getRow()->denda ?? 0;
        $pendapatanMasukGlobal = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'masuk')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $biayaKeluarGlobal = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'keluar')->where("YEAR(tanggal)", $tahun)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $pendapatanLainnyaGlobal = $pendapatanMasukGlobal - $biayaKeluarGlobal;

        $totalSHU = $totalJasaGlobal + $totalDendaGlobal + $pendapatanLainnyaGlobal;

        // 4. Kalkulasi Alokasi SHU Global
        $alokasiSHU = [
            'total_shu'          => $totalSHU,
            'jasa_modal'         => round($totalSHU * ($settings['shu_jasa_modal']        ?? 20) / 100),
            'jasa_anggota'       => round($totalSHU * ($settings['shu_jasa_anggota']      ?? 25) / 100),
            'pengurus_anggota'   => round($totalSHU * ($settings['shu_pengurus_anggota']  ?? 10) / 100),
            'pengawas'           => round($totalSHU * ($settings['shu_pengawas']          ?? 5)  / 100),
            'pembina'            => round($totalSHU * ($settings['shu_pembina']           ?? 5)  / 100),
            'dana_sosial'        => round($totalSHU * ($settings['shu_dana_sosial']     ?? 5)  / 100),
            'dana_pendidikan'    => round($totalSHU * ($settings['shu_dana_pendidikan'] ?? 5)  / 100),
            'dana_cadangan'      => round($totalSHU * ($settings['shu_cadangan']        ?? 20) / 100),
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

            $shu_jasa_angsuran = ($totalJasaGlobal > 0)
                ? ($total_jasa_individu / $totalJasaGlobal) * ($totalSHU * $persen_jasa_anggota)
                : 0;
            $shu_modal = ($totalSimpananGlobal > 0)
                ? ($saldo_anggota / $totalSimpananGlobal) * ($totalSHU * $persen_jasa_modal)
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
        $tahunCutoff = date('Y', strtotime($cutoff));
        $awalTahun   = $tahunCutoff . '-01-01';

        $totalJasaDiterima  = $db->table('angsuran')->where('tanggal_bayar >=', $awalTahun)->where('DATE(tanggal_bayar) <=', $cutoff)->selectSum('jumlah_jasa')->get()->getRow()->jumlah_jasa ?? 0;
        $totalDendaDiterima = $db->table('angsuran')->where('tanggal_bayar >=', $awalTahun)->where('DATE(tanggal_bayar) <=', $cutoff)->selectSum('denda')->get()->getRow()->denda ?? 0;
        
        $pendapatanManualMasuk = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'masuk')->where('tanggal >=', $awalTahun)->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
        $biayaManualKeluar = $db->table('kas_koperasi')->where('kategori', 'pendapatan_biaya')->where('jenis', 'keluar')->where('tanggal >=', $awalTahun)->where('DATE(tanggal) <=', $cutoff)->selectSum('nominal')->get()->getRow()->nominal ?? 0;
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
