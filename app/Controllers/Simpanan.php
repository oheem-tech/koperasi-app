<?php

namespace App\Controllers;

use App\Models\SimpananModel;
use App\Models\AnggotaModel;
use App\Models\JenisSimpananModel;
use App\Models\KasKoperasiModel; // Assume KasKoperasiModel exists for kas updates

class Simpanan extends BaseController
{
    protected $simpananModel;
    protected $anggotaModel;
    protected $jenisSimpananModel;

    public function __construct()
    {
        $this->simpananModel = new SimpananModel();
        $this->anggotaModel = new AnggotaModel();
        $this->jenisSimpananModel = new JenisSimpananModel();
    }

    public function index()
    {
        // Join with anggota and jenis_simpanan
        $db = \Config\Database::connect();
        $builder = $db->table('simpanan');
        $builder->select('simpanan.*, anggota.nama_lengkap, anggota.no_anggota, jenis_simpanan.nama_simpanan, kas_koperasi.keterangan as kas_keterangan');
        $builder->join('anggota', 'anggota.id = simpanan.anggota_id');
        $builder->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id');
        $builder->join('kas_koperasi', 'kas_koperasi.id = simpanan.kas_id', 'left');
        
        $anggotaList = [];
        
        // If user doesn't have manage rights, only see their own
        if (!has_permission('manage_simpanan')) {
            $anggota = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if ($anggota) {
                $builder->where('simpanan.anggota_id', $anggota['id']);
            } else {
                $builder->where('simpanan.anggota_id', 0); // No record
            }
        } else {
            $anggotaList = $this->anggotaModel->findAll();
            if ($this->request->getGet('anggota_id')) {
                $builder->where('simpanan.anggota_id', $this->request->getGet('anggota_id'));
            }
        }
        
        $builder->orderBy('simpanan.tanggal_transaksi', 'DESC');
        $builder->orderBy('simpanan.id', 'DESC');
        
        $data = [
            'title' => 'Transaksi Simpanan | Koperasi',
            'simpanan' => $builder->get()->getResultArray(),
            'anggota' => $anggotaList,
            'selected_anggota' => $this->request->getGet('anggota_id')
        ];
        return view('simpanan/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');

        $data = [
            'title' => 'Input Transaksi Simpanan | Koperasi',
            'anggota' => $this->anggotaModel->where('status', 'aktif')->findAll(),
            'jenis_simpanan' => $this->jenisSimpananModel->findAll()
        ];
        return view('simpanan/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');

        $jumlah = str_replace('.', '', $this->request->getPost('jumlah'));
        $jenis_transaksi = $this->request->getPost('jenis_transaksi'); // 'setor' or 'tarik'
        $tanggal_transaksi = $this->request->getPost('tanggal_transaksi');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Eksekusi Kas Koperasi terlebih dahulu untuk mendapatkan ID-nya
        $kasModel = new KasKoperasiModel();
        $anggota = $this->anggotaModel->find($this->request->getPost('anggota_id'));
        $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
        
        $keteranganKas = ($jenis_transaksi == 'setor' ? 'Setoran' : 'Penarikan') . ' Simpanan - ' . $namaAnggota;
        $jenisKas = ($jenis_transaksi == 'setor') ? 'masuk' : 'keluar';
        
        $kas_id = $kasModel->catatTransaksi($tanggal_transaksi, $keteranganKas, $jenisKas, $jumlah, 'simpanan');

        // 2. Simpan Simpanan dengan membawa referensi kas_id
        $this->simpananModel->save([
            'anggota_id'        => $this->request->getPost('anggota_id'),
            'jenis_simpanan_id' => $this->request->getPost('jenis_simpanan_id'),
            'tanggal_transaksi' => $tanggal_transaksi,
            'jumlah'            => $jumlah,
            'jenis_transaksi'   => $jenis_transaksi,
            'keterangan'        => $this->request->getPost('keterangan'),
            'kas_id'            => $kas_id, // Link yang solid
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi.');
        }

        return redirect()->to('/simpanan')->with('success', 'Transaksi simpanan berhasil diproses.');
    }

    public function print($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap, anggota.no_anggota, jenis_simpanan.nama_simpanan')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan.jenis_simpanan_id')
            ->where('simpanan.id', $id)
            ->get()->getRowArray();

        if (!$row) return redirect()->to('/simpanan')->with('error', 'Data tidak ditemukan.');

        if (!has_permission('manage_simpanan')) {
            $anggotaSelf = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if (!$anggotaSelf || $anggotaSelf['id'] != $row['anggota_id']) {
                return redirect()->to('/simpanan')->with('error', 'Akses ditolak.');
            }
        }

        return view('simpanan/print_kwitansi', ['data' => $row]);
    }

    public function edit($id)
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');

        $simpanan = $this->simpananModel->find($id);
        if (!$simpanan) return redirect()->to('/simpanan')->with('error', 'Data simpanan tidak ditemukan.');

        $data = [
            'title' => 'Edit Transaksi Simpanan | Koperasi',
            'simpanan' => $simpanan,
            'anggota' => $this->anggotaModel->where('status', 'aktif')->findAll(),
            'jenis_simpanan' => $this->jenisSimpananModel->findAll()
        ];
        return view('simpanan/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');

        $simpananLama = $this->simpananModel->find($id);
        if (!$simpananLama) return redirect()->to('/simpanan')->with('error', 'Data simpanan tidak ditemukan.');

        $jumlahBaru = str_replace('.', '', $this->request->getPost('jumlah'));
        $jenis_transaksiBaru = $this->request->getPost('jenis_transaksi');
        $tanggal_transaksiBaru = $this->request->getPost('tanggal_transaksi');
        $anggotaIdBaru = $this->request->getPost('anggota_id');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus catatan Kas lama berdasarkan kas_id yang terikat dengan Simpanan
        $kasModel = new KasKoperasiModel();
        if ($simpananLama['kas_id']) {
            $kasModel->delete($simpananLama['kas_id']);
        } else {
            // Fallback (jika data jadul belum termigrasi)
            $anggotaLama = $this->anggotaModel->find($simpananLama['anggota_id']);
            $namaAnggotaLama = $anggotaLama ? $anggotaLama['nama_lengkap'] : 'Anggota';
            $ketKasLama = ($simpananLama['jenis_transaksi'] == 'setor' ? 'Setoran' : 'Penarikan') . ' Simpanan - ' . $namaAnggotaLama;
            
            $kasLama = $kasModel->where('tanggal', $simpananLama['tanggal_transaksi'])
                                ->where('nominal', $simpananLama['jumlah'])
                                ->like('keterangan', $ketKasLama, 'both')
                                ->first();

            if ($kasLama) $kasModel->delete($kasLama['id']);
        }

        // 2. Catat transaksi Kas baru dan dapatkan ID-nya
        $anggotaBaru = $this->anggotaModel->find($anggotaIdBaru);
        $namaAnggotaBaru = $anggotaBaru ? $anggotaBaru['nama_lengkap'] : 'Anggota';
        $keteranganKasBaru = ($jenis_transaksiBaru == 'setor' ? 'Setoran' : 'Penarikan') . ' Simpanan - ' . $namaAnggotaBaru;
        $jenisKasBaru = ($jenis_transaksiBaru == 'setor') ? 'masuk' : 'keluar';
        
        $kas_id_baru = $kasModel->catatTransaksi($tanggal_transaksiBaru, $keteranganKasBaru, $jenisKasBaru, $jumlahBaru, 'simpanan');

        // 3. Update data simpanan
        $this->simpananModel->update($id, [
            'anggota_id'        => $anggotaIdBaru,
            'jenis_simpanan_id' => $this->request->getPost('jenis_simpanan_id'),
            'tanggal_transaksi' => $tanggal_transaksiBaru,
            'jumlah'            => $jumlahBaru,
            'jenis_transaksi'   => $jenis_transaksiBaru,
            'keterangan'        => $this->request->getPost('keterangan'),
            'kas_id'            => $kas_id_baru, // Simpan kas_id yang baru
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pembaruan transaksi.');
        }

        return redirect()->to('/simpanan')->with('success', 'Transaksi simpanan berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_simpanan')) return redirect()->to('/dashboard');

        $simpanan = $this->simpananModel->find($id);
        if (!$simpanan) return redirect()->to('/simpanan')->with('error', 'Data simpanan tidak ditemukan.');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus dari Kas Koperasi menggunakan kas_id
        $kasModel = new KasKoperasiModel();
        if ($simpanan['kas_id']) {
            $kasModel->delete($simpanan['kas_id']);
        } else {
            // Fallback backward compatibility
            $anggota = $this->anggotaModel->find($simpanan['anggota_id']);
            $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
            $keteranganKas = ($simpanan['jenis_transaksi'] == 'setor' ? 'Setoran' : 'Penarikan') . ' Simpanan - ' . $namaAnggota;

            $kasSekarang = $kasModel->where('tanggal', $simpanan['tanggal_transaksi'])
                                    ->where('nominal', $simpanan['jumlah'])
                                    ->like('keterangan', $keteranganKas, 'both')
                                    ->first();

            if ($kasSekarang) $kasModel->delete($kasSekarang['id']);
        }

        // 2. Hapus simpanan
        $this->simpananModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/simpanan')->with('error', 'Gagal menghapus transaksi simpanan.');
        }

        return redirect()->to('/simpanan')->with('success', 'Data simpanan berhasil dihapus beserta catatan kas terkait.');
    }

    public function getSaldo()
    {
        $anggotaId = $this->request->getGet('anggota_id');
        $jenisSimpananId = $this->request->getGet('jenis_simpanan_id');

        if (!$anggotaId || !$jenisSimpananId) {
            return $this->response->setJSON(['saldo' => 0]);
        }

        $db = \Config\Database::connect();
        
        $setor = $db->table('simpanan')
            ->selectSum('jumlah')
            ->where('anggota_id', $anggotaId)
            ->where('jenis_simpanan_id', $jenisSimpananId)
            ->where('jenis_transaksi', 'setor')
            ->get()->getRow()->jumlah ?? 0;

        $tarik = $db->table('simpanan')
            ->selectSum('jumlah')
            ->where('anggota_id', $anggotaId)
            ->where('jenis_simpanan_id', $jenisSimpananId)
            ->where('jenis_transaksi', 'tarik')
            ->get()->getRow()->jumlah ?? 0;

        $saldo = $setor - $tarik;

        return $this->response->setJSON(['saldo' => $saldo > 0 ? $saldo : 0]);
    }
}
