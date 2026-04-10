<?php

namespace App\Controllers;

use App\Models\KasKoperasiModel;

class KasKoperasi extends BaseController
{
    protected $kasModel;

    public function __construct()
    {
        $this->kasModel = new KasKoperasiModel();
    }

    /**
     * Tentukan apakah transaksi kas ini berasal dari sistem otomatis (simpanan, angsuran, pinjaman)
     * atau dari input manual bendahara (operasional).
     * 
     * Menggunakan kolom 'kategori' sebagai penentu utama karena lebih andal daripada
     * string-matching pada keterangan (keterangan dari data lama seperti "Penarikan SHR" 
     * tidak cocok dengan prefix "Penarikan Simpanan").
     */
    private function isAutoSystem($row) {
        // Metode 1: Cek kolom kategori (cara terbaik & paling andal)
        $autoKategori = ['simpanan', 'angsuran', 'pinjaman'];
        if (isset($row['kategori']) && in_array($row['kategori'], $autoKategori)) {
            return true;
        }
        // Metode 2: Fallback string matching untuk data lama yang mungkin tidak punya kategori
        $prefixes = [
            'Setoran Simpanan', 'Penarikan Simpanan',
            'Pencairan Pinjaman', 'Setoran Angsuran', 'Pelunasan Angsuran',
            'Penarikan SHR', 'Setoran SHR',
            'Penarikan Simpanan Hari Raya', 'Setoran Simpanan Hari Raya',
        ];
        $keterangan = $row['keterangan'] ?? '';
        foreach ($prefixes as $p) {
            if (strpos($keterangan, $p) === 0) return true;
        }
        return false;
    }

    public function index()
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $bulanParam = $this->request->getGet('bulan');
        $filterBulan = $this->request->getGet('filter_bulan');
        $filterTahun = $this->request->getGet('filter_tahun');

        if ($filterBulan && $filterTahun) {
            $bulan = $filterTahun . '-' . str_pad($filterBulan, 2, '0', STR_PAD_LEFT);
        } else {
            $bulan = $bulanParam ?? date('Y-m');
        }

        if ($bulan === 'all') {
            $kas = $this->kasModel->orderBy('tanggal', 'ASC')->orderBy('id', 'ASC')->findAll();
            $awalSaldo = 0;
        } else {
            $kas = $this->kasModel
                ->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan)
                ->orderBy('tanggal', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
            
            // Hitung total saldo masuk & keluar SEBELUM bulan ini
            $masukSebelum = $this->kasModel->where("DATE_FORMAT(tanggal, '%Y-%m') <", $bulan)->where('jenis', 'masuk')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
            $keluarSebelum = $this->kasModel->where("DATE_FORMAT(tanggal, '%Y-%m') <", $bulan)->where('jenis', 'keluar')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
            $awalSaldo = $masukSebelum - $keluarSebelum;
        }

        // Kalkulasi dinamis saldo akhir per baris
        $saldoBerjalan = $awalSaldo;
        foreach ($kas as &$k) {
            if ($k['jenis'] == 'masuk') {
                $saldoBerjalan += $k['nominal'];
            } else {
                $saldoBerjalan -= $k['nominal'];
            }
            $k['saldo_akhir'] = $saldoBerjalan; // Override statik DB
            $k['is_auto'] = $this->isAutoSystem($k);
            
            // Check for Massal indicator
            $k['is_massal'] = strpos($k['keterangan'], '[Massal]') !== false;
            $k['keterangan_display'] = str_replace(' [Massal]', '', $k['keterangan']);
        }

        $data = [
            'title' => 'Buku Kas Umum | Koperasi',
            'kas'   => $kas,
            'bulan' => $bulan,
            'awalSaldo' => $awalSaldo
        ];
        return view('kas/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $data = ['title' => 'Input Transaksi Kas Manual'];
        return view('kas/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $tanggal    = $this->request->getPost('tanggal');
        $keterangan = $this->request->getPost('keterangan');
        $jenis      = $this->request->getPost('jenis');
        $kategori   = $this->request->getPost('kategori');
        $nominal    = str_replace('.', '', $this->request->getPost('nominal'));

        $this->kasModel->catatTransaksi($tanggal, $keterangan, $jenis, $nominal, $kategori);

        return redirect()->to('/kas')->with('success', 'Transaksi kas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $kas = $this->kasModel->find($id);
        if (!$kas) return redirect()->to('/kas')->with('error', 'Transaksi tidak ditemukan.');
        
        if ($this->isAutoSystem($kas)) {
            return redirect()->to('/kas')->with('error', 'Transaksi otomatis dari sistem tidak boleh diedit dari menu ini.');
        }

        $data = [
            'title' => 'Edit Transaksi Kas Manual',
            'kas'   => $kas
        ];
        return view('kas/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $kas = $this->kasModel->find($id);
        if (!$kas || $this->isAutoSystem($kas)) {
            return redirect()->to('/kas')->with('error', 'Akses ditolak.');
        }

        $this->kasModel->update($id, [
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'jenis'      => $this->request->getPost('jenis'),
            'kategori'   => $this->request->getPost('kategori'),
            'nominal'    => str_replace('.', '', $this->request->getPost('nominal')),
        ]);

        return redirect()->to('/kas')->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_kas')) return redirect()->to('/dashboard');

        $kas = $this->kasModel->find($id);
        if (!$kas || $this->isAutoSystem($kas)) {
            return redirect()->to('/kas')->with('error', 'Akses ditolak memanipulasi data sistem.');
        }

        $this->kasModel->delete($id);
        return redirect()->to('/kas')->with('success', 'Transaksi kas berhasil dihapus.');
    }
}
