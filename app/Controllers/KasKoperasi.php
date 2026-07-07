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
        $filterType = $this->request->getGet('filter_type');
        $filterBulan = $this->request->getGet('filter_bulan');
        $filterTahun = $this->request->getGet('filter_tahun');
        $filterSemester = $this->request->getGet('filter_semester');

        $prevPeriodeDesc = '';
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
            
            $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            
            if ($filterType === 'bulan') {
                $periodeDesc = $namaBulan[str_pad($filterBulan, 2, '0', STR_PAD_LEFT)] . ' ' . $filterTahun;
                
                $prevM = $filterBulan - 1;
                $prevY = $filterTahun;
                if ($prevM < 1) {
                    $prevM = 12;
                    $prevY--;
                }
                $prevPeriodeDesc = 'Bulan ' . $namaBulan[str_pad($prevM, 2, '0', STR_PAD_LEFT)] . ' ' . $prevY;
                
            } else if ($filterType === 'semester') {
                $periodeDesc = 'Semester ' . $filterSemester . ' Tahun ' . $filterTahun;
                
                $prevS = $filterSemester == '1' ? '2' : '1';
                $prevY = $filterSemester == '1' ? $filterTahun - 1 : $filterTahun;
                $prevPeriodeDesc = 'Semester ' . $prevS . ' Tahun ' . $prevY;
                
            } else {
                $periodeDesc = 'Tahun ' . $filterTahun;
                $prevPeriodeDesc = 'Tahun ' . ($filterTahun - 1);
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
            
            $prevM = $periode['bulan'] - 1;
            $prevY = $periode['tahun'];
            if ($prevM < 1) {
                $prevM = 12;
                $prevY--;
            }
            $prevPeriodeDesc = 'Bulan ' . $namaBulan[str_pad($prevM, 2, '0', STR_PAD_LEFT)] . ' ' . $prevY;
        }

        if ($periode === 'all') {
            $kas = $this->kasModel->orderBy('tanggal', 'ASC')->orderBy('id', 'ASC')->findAll();
            $awalSaldo = 0;
        } else {
            if ($periode['type'] === 'bulan') {
                $bulanStr = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
                $this->kasModel->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulanStr);
                $tanggalAwal = $bulanStr . '-01';
            } else if ($periode['type'] === 'semester') {
                $this->kasModel->where("YEAR(tanggal)", $periode['tahun']);
                if ($periode['semester'] == '1') {
                    $this->kasModel->where("MONTH(tanggal) <=", 6);
                    $tanggalAwal = $periode['tahun'] . '-01-01';
                } else {
                    $this->kasModel->where("MONTH(tanggal) >", 6);
                    $tanggalAwal = $periode['tahun'] . '-07-01';
                }
            } else if ($periode['type'] === 'tahun') {
                $this->kasModel->where("YEAR(tanggal)", $periode['tahun']);
                $tanggalAwal = $periode['tahun'] . '-01-01';
            }
            
            $kas = $this->kasModel
                ->orderBy('tanggal', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
            
            // Hitung total saldo masuk & keluar SEBELUM tanggal awal periode
            $masukSebelum = $this->kasModel->where("DATE(tanggal) <", $tanggalAwal)->where('jenis', 'masuk')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
            $keluarSebelum = $this->kasModel->where("DATE(tanggal) <", $tanggalAwal)->where('jenis', 'keluar')->selectSum('nominal')->get()->getRow()->nominal ?? 0;
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
            'periode' => $periode,
            'periodeDesc' => $periodeDesc,
            'prevPeriodeDesc' => $prevPeriodeDesc,
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
