<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\JenisSimpananModel;
use App\Models\KasKoperasiModel;

class InputMassal extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $jenisSimpananModel;
    protected $kasModel;
    protected $pengaturanModel;

    protected $kelompokList = [];

    public function __construct()
    {
        $this->anggotaModel      = new AnggotaModel();
        $this->simpananModel     = new SimpananModel();
        $this->jenisSimpananModel = new JenisSimpananModel();
        $this->kasModel          = new KasKoperasiModel();
        $this->pengaturanModel   = new \App\Models\PengaturanModel();
        $this->kelompokModel     = new \App\Models\KelompokModel();

        // Ambil daftar kelompok secara dinamis dari database Master Kelompok
        $kelompoks = $this->kelompokModel->findAll();
        foreach($kelompoks as $k) {
            $this->kelompokList[] = $k['nama_kelompok'];
        }
        
        // Cek juga dari data anggota yang usang namun masih digunakan untuk memastikan tak ada yang bocor 
        // (historikal fallback)
        $legacy = $this->anggotaModel->distinct()->select('kelompok')->where('status', 'aktif')->findAll();
        foreach($legacy as $l) {
            if (!empty($l['kelompok']) && !in_array($l['kelompok'], $this->kelompokList)) {
                $this->kelompokList[] = $l['kelompok'];
            }
        }

        if (empty($this->kelompokList)) {
            $this->kelompokList = ['Umum'];
        }
    }

    /**
     * Halaman Filter / Setup awal
     */
    public function index()
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');
        if (!is_premium()) return redirect()->to('/informasi/support')->with('error', 'Fitur Input Massal hanya tersedia untuk Versi PRO. Silakan upgrade lisensi Anda.');

        $data = [
            'title'        => 'Input Massal | Koperasi',
            'kelompokList' => $this->kelompokList,
        ];
        return view('massal/index', $data);
    }

    /**
     * Tampilkan form tabel massal setelah filter diterapkan
     */
    public function form()
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');
        if (!is_premium()) return redirect()->to('/informasi/support')->with('error', 'Fitur Input Massal hanya tersedia untuk Versi PRO. Silakan upgrade lisensi Anda.');

        $kelompok = $this->request->getGet('kelompok') ?? (isset($this->kelompokList[0]) ? $this->kelompokList[0] : 'Umum');
        $tanggal  = $this->request->getGet('tanggal')  ?? date('Y-m-d');

        $db = \Config\Database::connect();

        // Ambil semua anggota aktif sesuai kelompok
        $anggotaList = $this->anggotaModel
            ->where('status', 'aktif')
            ->where('kelompok', $kelompok)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // Ambil jenis simpanan wajib (default untuk potong gaji)
        $jenisSimpanan = $this->jenisSimpananModel->findAll();
        $jenisSimpananWajib = null;
        foreach ($jenisSimpanan as $js) {
            if (stripos($js['nama_simpanan'], 'wajib') !== false) {
                $jenisSimpananWajib = $js;
                break;
            }
        }
        
        // Ambil pengaturan master terkait pelunasan
        $minTenorConf  = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_min_tenor_persen')->first();
        $aktifConf     = $this->pengaturanModel->where('pengaturan_key', 'kebijakan_pelunasan_aktif')->first();
        $jasaBebasConf = $this->pengaturanModel->where('pengaturan_key', 'pelunasan_jasa_bebas_persen')->first();
        
        $min_persen        = $minTenorConf  ? (float)$minTenorConf['pengaturan_value']  : 50;
        $kebijakan_aktif   = $aktifConf     ? (int)$aktifConf['pengaturan_value']        : 1;
        $jasa_bebas_persen = $jasaBebasConf ? (float)$jasaBebasConf['pengaturan_value'] : 100;

        // Untuk setiap anggota: ambil SEMUA pinjaman aktif (status disetujui, belum lunas)
        foreach ($anggotaList as &$a) {
            $pinjamanList = $db->table('pinjaman')
                ->where('anggota_id', $a['id'])
                ->where('status', 'disetujui')
                ->get()->getResultArray();

            $a['pinjaman_aktif'] = [];

            foreach ($pinjamanList as $p) {
                $cicilanDibayar    = $db->table('angsuran')->where('pinjaman_id', $p['id'])->countAllResults();
                $cicilanBerikutnya = $cicilanDibayar + 1;

                // Skip jika semua cicilan sudah selesai
                if ($cicilanBerikutnya > $p['lama_tenor']) continue;

                $pokokPerBulan = round($p['jumlah_pinjaman'] / $p['lama_tenor']);
                $jasaPerBulan  = round($p['jumlah_pinjaman'] * ($p['bunga_persen'] / 100));
                $totalPerBulan = $pokokPerBulan + $jasaPerBulan;

                // Kalkulasi Pelunasan
                $tenor = (int)$p['lama_tenor'];
                $jumlah_pokok = (float)$p['jumlah_pinjaman'];
                $pokok_terbayar = $cicilanDibayar * $pokokPerBulan;
                
                $sisa_pokok_lunas = max(0, $jumlah_pokok - $pokok_terbayar);

                $batas_cicilan  = ceil($tenor * ($min_persen / 100));
                $kena_jasa_full = $kebijakan_aktif && ($cicilanDibayar < $batas_cicilan);

                $total_jasa_full = $jumlah_pokok * ($p['bunga_persen'] / 100) * $tenor;

                if ($kena_jasa_full) {
                    $jasa_terbayar = $db->table('angsuran')->selectSum('jumlah_jasa')->where('pinjaman_id', $p['id'])->get()->getRow()->jumlah_jasa ?? 0;
                    $sisa_jasa = max(0, $total_jasa_full - $jasa_terbayar);
                } else {
                    $sisa_jasa = round($jasaPerBulan * ($jasa_bebas_persen / 100));
                }

                $sisa_jasa_lunas = $sisa_jasa;
                $total_lunas = $sisa_pokok_lunas + $sisa_jasa_lunas;

                $a['pinjaman_aktif'][] = [
                    'pinjaman_id'     => $p['id'],
                    'jenis_pinjaman'  => $p['jenis_pinjaman'],
                    'lama_tenor'      => $p['lama_tenor'],
                    'cicilan_ke'      => $cicilanBerikutnya,
                    'is_last_installment' => ($cicilanBerikutnya >= $p['lama_tenor']),
                    'sisa_tenor'      => $p['lama_tenor'] - $cicilanDibayar,
                    'pokok_per_bulan' => $pokokPerBulan,
                    'jasa_per_bulan'  => $jasaPerBulan,
                    'total_per_bulan' => $totalPerBulan,
                    'jumlah_pinjaman' => $p['jumlah_pinjaman'],
                    'bunga_persen'    => $p['bunga_persen'],
                    'sisa_pokok_lunas'=> $sisa_pokok_lunas,
                    'sisa_jasa_lunas' => $sisa_jasa_lunas,
                    'total_lunas'     => $total_lunas,
                ];
            }
        }
        unset($a);

        $data = [
            'title'              => 'Form Input Massal — ' . $kelompok . ' | Koperasi',
            'kelompok'           => $kelompok,
            'kelompokList'       => $this->kelompokList,
            'tanggal'            => $tanggal,
            'anggotaList'        => $anggotaList,
            'jenisSimpanan'      => $jenisSimpanan,
            'jenisSimpananWajib' => $jenisSimpananWajib,
        ];
        return view('massal/form', $data);
    }

    /**
     * Proses penyimpanan input massal
     */
    public function store()
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');
        if (!is_premium()) return redirect()->to('/informasi/support')->with('error', 'Fitur Input Massal hanya tersedia untuk Versi PRO. Silakan upgrade lisensi Anda.');

        $db = \Config\Database::connect();
        $tanggal  = $this->request->getPost('tanggal');
        $kelompok = $this->request->getPost('kelompok');
        $bulan    = substr($tanggal, 0, 7); // YYYY-MM

        // ---- Simpanan arrays ----
        $simpananInclude  = $this->request->getPost('simpanan_include')    ?? [];
        $simpananAnggota  = $this->request->getPost('simpanan_anggota_id') ?? [];
        $simpananJenis    = $this->request->getPost('simpanan_jenis_id')   ?? [];
        $simpananAmounts  = $this->request->getPost('simpanan_amount')     ?? [];

        // ---- Angsuran arrays (flat, per pinjaman) ----
        $angInclude    = $this->request->getPost('ang_include')     ?? [];
        $angAnggotaId  = $this->request->getPost('ang_anggota_id') ?? [];
        $angPinjamanId = $this->request->getPost('ang_pinjaman_id') ?? [];
        $angPokok      = $this->request->getPost('ang_pokok')       ?? [];
        $angJasa       = $this->request->getPost('ang_jasa')        ?? [];
        $angCicilanKe  = $this->request->getPost('ang_cicilan_ke')  ?? [];
        $angIsPelunasan= $this->request->getPost('ang_is_pelunasan') ?? [];

        $totalSimpanan = 0;
        $totalAngsuran = 0;
        $jumlahSimpanan = 0;
        $jumlahAngsuran = 0;

        $db->transStart();
        $angsuranModel = new \App\Models\AngsuranModel();

        // ============ PROSES SIMPANAN ============
        foreach ($simpananAnggota as $idx => $anggotaId) {
            if (empty($simpananInclude[$idx])) continue; // skip jika tidak dicentang

            $nominal = (int) str_replace('.', '', $simpananAmounts[$idx] ?? '0');
            if ($nominal <= 0) continue;

            // 1. Catat ke Kas individu agar terhubung dengan fitur Edit/Hapus
            $anggota = $this->anggotaModel->find($anggotaId);
            $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
            $keteranganKas = 'Setoran Simpanan - ' . $namaAnggota . ' [Massal]';
            $kas_id = $this->kasModel->catatTransaksi($tanggal, $keteranganKas, 'masuk', $nominal, 'simpanan');

            // 2. Simpan Simpanan dengan referensi kas_id
            $this->simpananModel->save([
                'anggota_id'        => $anggotaId,
                'jenis_simpanan_id' => $simpananJenis[$idx] ?? null,
                'tanggal_transaksi' => $tanggal,
                'jumlah'            => $nominal,
                'jenis_transaksi'   => 'setor',
                'keterangan'        => 'Setoran Massal Potong Gaji ' . $kelompok,
                'kas_id'            => $kas_id
            ]);

            $totalSimpanan += $nominal;
            $jumlahSimpanan++;
        }

        // ============ PROSES ANGSURAN ============
        foreach ($angPinjamanId as $idx => $pinjamanId) {
            if (empty($angInclude[$idx])) continue; // skip jika tidak dicentang
            if (empty($pinjamanId)) continue;

            $pokok     = (int) str_replace('.', '', $angPokok[$idx]  ?? '0');
            $jasa      = (int) str_replace('.', '', $angJasa[$idx]   ?? '0');
            $cicilanKe = (int) ($angCicilanKe[$idx] ?? 0);
            $total     = $pokok + $jasa;

            if ($total <= 0 || $cicilanKe <= 0) continue;

            $pinjaman = $db->table('pinjaman')->where('id', $pinjamanId)->get()->getRowArray();
            $kas_id = null;
            $is_pelunasan = !empty($angIsPelunasan[$idx]);

            // 1. Catat ke Kas individu agar terhubung dengan fitur Edit/Hapus
            if ($pinjaman) {
                if ($is_pelunasan) {
                    $cicilanKe = $pinjaman['lama_tenor'];
                }
                
                $anggota = $this->anggotaModel->find($pinjaman['anggota_id']);
                $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
                $kodePinjaman = 'PJ-' . str_pad($pinjamanId, 4, '0', STR_PAD_LEFT);
                
                if ($is_pelunasan) {
                    $keteranganKas = 'Pelunasan Pinjaman ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' [' . $kodePinjaman . '] [Massal]';
                } else {
                    $keteranganKas = 'Setoran Angsuran ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' (Cicilan ke-' . $cicilanKe . ') [' . $kodePinjaman . '] [Massal]';
                }
                $kas_id = $this->kasModel->catatTransaksi($tanggal, $keteranganKas, 'masuk', $total, 'angsuran');
            }

            // 2. Simpan angsuran
            $angsuranModel->save([
                'pinjaman_id'   => $pinjamanId,
                'tanggal_bayar' => $tanggal,
                'jumlah_bayar'  => $total,
                'jumlah_pokok'  => $pokok,
                'jumlah_jasa'   => $jasa,
                'denda'         => 0,
                'cicilan_ke'    => $cicilanKe,
                'kas_id'        => $kas_id
            ]);
            
            // Auto lunas jika cicilan terakhir atau pelunasan
            if ($pinjaman && ($is_pelunasan || $cicilanKe >= $pinjaman['lama_tenor'])) {
                $db->table('pinjaman')->where('id', $pinjamanId)->update(['status' => 'lunas']);
            }

            $totalAngsuran += $total;
            $jumlahAngsuran++;
        }

        // ============ TOTAL SUMMARY ============
        $totalMasuk = $totalSimpanan + $totalAngsuran;

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/massal')->with('error', 'Gagal memproses input massal. Silakan coba lagi.');
        }

        return redirect()->to('/massal')->with('success',
            "Input massal berhasil! {$jumlahSimpanan} setoran simpanan & {$jumlahAngsuran} pembayaran angsuran diproses. " .
            "Total: Rp " . number_format($totalMasuk, 0, ',', '.') . ".");
    }
}
