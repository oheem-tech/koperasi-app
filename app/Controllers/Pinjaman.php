<?php

namespace App\Controllers;

use App\Models\PinjamanModel;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;

class Pinjaman extends BaseController
{
    protected $pinjamanModel;
    protected $anggotaModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pinjamanModel = new PinjamanModel();
        $this->anggotaModel = new AnggotaModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pinjaman');
        $builder->select('pinjaman.*, anggota.nama_lengkap, anggota.no_anggota');
        $builder->join('anggota', 'anggota.id = pinjaman.anggota_id');
        
        $anggotaList = [];
        if (!has_permission('manage_pinjaman')) {
            $anggota = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if ($anggota) {
                $builder->where('pinjaman.anggota_id', $anggota['id']);
            } else {
                $builder->where('pinjaman.anggota_id', 0);
            }
        } else {
            $anggotaList = $this->anggotaModel->findAll();
            if ($this->request->getGet('anggota_id')) {
                $builder->where('pinjaman.anggota_id', $this->request->getGet('anggota_id'));
            }
        }
        
        $builder->orderBy('pinjaman.tanggal_pengajuan', 'DESC');
        $builder->orderBy('pinjaman.id', 'DESC');
        
        $data = [
            'title' => 'Data Pinjaman | Koperasi',
            'pinjaman' => $builder->get()->getResultArray(),
            'anggota' => $anggotaList,
            'selected_anggota' => $this->request->getGet('anggota_id')
        ];
        return view('pinjaman/index', $data);
    }

    public function create()
    {
        $opsiTenor = $this->pengaturanModel->where('pengaturan_key', 'opsi_tenor_pinjaman')->first();
        $tenorArr = $opsiTenor ? explode(',', $opsiTenor['pengaturan_value']) : [3, 6, 12, 24];

        $opsiJenis = $this->pengaturanModel->where('pengaturan_key', 'opsi_jenis_pinjaman')->first();
        $jenisArr = $opsiJenis ? explode(',', $opsiJenis['pengaturan_value']) : ['Uang'];

        $data = [
            'title'      => 'Pengajuan Pinjaman',
            'anggota'    => has_permission('manage_pinjaman') ? $this->anggotaModel->where('status', 'aktif')->findAll() : null,
            'opsi_tenor' => $tenorArr,
            'opsi_jenis' => $jenisArr
        ];
        return view('pinjaman/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_pinjaman')) {
            $anggota = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            $anggota_id = $anggota['id'];
        } else {
            $anggota_id = $this->request->getPost('anggota_id');
        }

        $jenis_pinjaman = $this->request->getPost('jenis_pinjaman');
        $jumlah_pinjaman = str_replace('.', '', $this->request->getPost('jumlah_pinjaman'));
        $lama_tenor = $this->request->getPost('lama_tenor');

        // Validasi batas maksimal pinjaman aktif per jenis
        $maksConf = $this->pengaturanModel->where('pengaturan_key', 'maks_pinjaman_aktif')->first();
        $maks = $maksConf ? (int)$maksConf['pengaturan_value'] : 1;

        $existingCount = $this->pinjamanModel
            ->where('anggota_id', $anggota_id)
            ->where('jenis_pinjaman', $jenis_pinjaman)
            ->whereIn('status', ['pending', 'disetujui'])
            ->countAllResults();

        if ($existingCount >= $maks) {
            return redirect()->back()->with('error', "Anggota sudah memiliki {$existingCount} pinjaman aktif/pending untuk jenis '{$jenis_pinjaman}'. Batas maksimal adalah {$maks}.");
        }

        $tanggal_pengajuan = $this->request->getPost('tanggal_pengajuan');
        if (empty($tanggal_pengajuan)) {
            $tanggal_pengajuan = date('Y-m-d');
        }

        $bungaConf = $this->pengaturanModel->where('pengaturan_key', 'bunga_pinjaman')->first();
        $bunga_persen = $bungaConf ? $bungaConf['pengaturan_value'] : 1.5;

        $this->pinjamanModel->save([
            'anggota_id'          => $anggota_id,
            'jenis_pinjaman'      => $jenis_pinjaman,
            'tanggal_pengajuan'   => $tanggal_pengajuan,
            'jumlah_pinjaman'     => $jumlah_pinjaman,
            'lama_tenor'          => $lama_tenor,
            'bunga_persen'        => $bunga_persen,
            'tanggal_jatuh_tempo' => null,
            'status'              => 'pending'
        ]);

        return redirect()->to('/pinjaman')->with('success', 'Pengajuan pinjaman berhasil dikirim.');
    }

    public function approve($id)
    {
        if (!has_permission('manage_pinjaman')) return redirect()->to('/dashboard');
        
        $pinjaman = $this->pinjamanModel->find($id);
        if ($pinjaman && $pinjaman['status'] == 'pending') {
            $jatuh_tempo = date('Y-m-d', strtotime("+" . $pinjaman['lama_tenor'] . " months"));
            
            $db = \Config\Database::connect();
            $db->transStart();

            $this->pinjamanModel->update($id, [
                'status' => 'disetujui',
                'tanggal_jatuh_tempo' => $jatuh_tempo
            ]);

            // Integrate with Kas Koperasi
            $kasModel = new \App\Models\KasKoperasiModel();
            $anggota = $this->anggotaModel->find($pinjaman['anggota_id']);
            $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
            
            $keteranganKas = 'Pencairan Pinjaman - ' . $namaAnggota;
            $kasModel->catatTransaksi(date('Y-m-d'), $keteranganKas, 'keluar', $pinjaman['jumlah_pinjaman'], 'pinjaman');

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/pinjaman')->with('error', 'Gagal memproses persetujuan pinjaman.');
            }
        }
        return redirect()->to('/pinjaman')->with('success', 'Pinjaman berhasil disetujui dan dana kas dicairkan.');
    }

    public function reject($id)
    {
        if (!has_permission('manage_pinjaman')) return redirect()->to('/dashboard');
        
        $pinjaman = $this->pinjamanModel->find($id);
        if ($pinjaman && $pinjaman['status'] == 'pending') {
            $this->pinjamanModel->update($id, [
                'status' => 'ditolak'
            ]);
        }
        return redirect()->to('/pinjaman')->with('error', 'Pinjaman telah ditolak.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_pinjaman')) return redirect()->to('/dashboard');

        $pinjaman = $this->pinjamanModel->find($id);
        if (!$pinjaman) return redirect()->to('/pinjaman')->with('error', 'Data pinjaman tidak ditemukan.');

        $opsiTenor = $this->pengaturanModel->where('pengaturan_key', 'opsi_tenor_pinjaman')->first();
        $tenorArr = $opsiTenor ? explode(',', $opsiTenor['pengaturan_value']) : [3, 6, 12, 24];

        $opsiJenis = $this->pengaturanModel->where('pengaturan_key', 'opsi_jenis_pinjaman')->first();
        $jenisArr = $opsiJenis ? explode(',', $opsiJenis['pengaturan_value']) : ['Uang'];

        $data = [
            'title'      => 'Edit Pinjaman',
            'pinjaman'   => $pinjaman,
            'anggota'    => $this->anggotaModel->where('status', 'aktif')->findAll(),
            'opsi_tenor' => $tenorArr,
            'opsi_jenis' => $jenisArr
        ];
        return view('pinjaman/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_pinjaman')) return redirect()->to('/dashboard');

        $pinjaman = $this->pinjamanModel->find($id);
        if (!$pinjaman) return redirect()->to('/pinjaman')->with('error', 'Data pinjaman tidak ditemukan.');

        $jenis_pinjaman = $this->request->getPost('jenis_pinjaman');
        $jumlah_pinjaman = str_replace('.', '', $this->request->getPost('jumlah_pinjaman'));
        $lama_tenor = $this->request->getPost('lama_tenor');
        $tanggal_pengajuan = $this->request->getPost('tanggal_pengajuan');
        $anggota_id = $this->request->getPost('anggota_id');

        $updateData = [
            'jenis_pinjaman'      => $jenis_pinjaman,
            'tanggal_pengajuan'   => $tanggal_pengajuan,
            'jumlah_pinjaman'     => $jumlah_pinjaman,
            'lama_tenor'          => $lama_tenor,
        ];

        if ($anggota_id) {
            $updateData['anggota_id'] = $anggota_id;
        }

        $this->pinjamanModel->update($id, $updateData);

        return redirect()->to('/pinjaman')->with('success', 'Data pinjaman berhasil diupdate.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_pinjaman')) return redirect()->to('/dashboard');

        $pinjaman = $this->pinjamanModel->find($id);
        if (!$pinjaman) return redirect()->to('/pinjaman')->with('error', 'Data pinjaman tidak ditemukan.');

        $this->pinjamanModel->delete($id);

        return redirect()->to('/pinjaman')->with('success', 'Data pinjaman berhasil dihapus.');
    }

    public function print($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('pinjaman')
            ->select('pinjaman.*, anggota.nama_lengkap, anggota.no_anggota')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where('pinjaman.id', $id)
            ->get()->getRowArray();

        if (!$row) return redirect()->to('/pinjaman')->with('error', 'Data tidak ditemukan.');

        if (!has_permission('manage_pinjaman')) {
            $anggotaSelf = $this->anggotaModel->where('user_id', session()->get('user_id'))->first();
            if (!$anggotaSelf || $anggotaSelf['id'] != $row['anggota_id']) {
                return redirect()->to('/pinjaman')->with('error', 'Akses ditolak.');
            }
        }

        return view('pinjaman/print', ['data' => $row]);
    }
}
