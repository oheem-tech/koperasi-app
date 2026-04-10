<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\UserModel;
use App\Models\KelompokModel;

class Anggota extends BaseController
{
    protected $anggotaModel;
    protected $userModel;
    protected $kelompokModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->userModel = new UserModel();
        $this->kelompokModel = new KelompokModel();
    }

    public function index()
    {
        if (!has_permission('manage_anggota')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title'   => 'Data Anggota | Koperasi Simpan Pinjam',
            'anggota' => $this->anggotaModel->findAll()
        ];
        return view('anggota/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_anggota')) {
            return redirect()->to('/dashboard');
        }

        $lastAnggota = $this->anggotaModel->orderBy('id', 'DESC')->first();
        $nextNum = 1;
        if ($lastAnggota) {
            if (preg_match('/(\d+)$/', $lastAnggota['no_anggota'], $matches)) {
                $nextNum = intval($matches[1]) + 1;
            } else {
                $nextNum = $lastAnggota['id'] + 1;
            }
        }
        $autoNoAnggota = 'A' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $data = [
            'title' => 'Tambah Anggota | Koperasi',
            'autoNoAnggota' => $autoNoAnggota,
            'kelompok'      => $this->kelompokModel->findAll()
        ];
        return view('anggota/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_anggota')) return redirect()->to('/dashboard');

        $db = \Config\Database::connect();
        $db->transStart();

        $this->userModel->save([
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'anggota'
        ]);
        $userId = $this->userModel->getInsertID();

        $this->anggotaModel->save([
            'user_id'           => $userId,
            'no_anggota'        => $this->request->getPost('no_anggota'),
            'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_telp'           => $this->request->getPost('no_telp'),
            'tanggal_bergabung' => $this->request->getPost('tanggal_bergabung'),
            'kelompok'          => $this->request->getPost('kelompok') ?? 'Umum',
            'status'            => 'aktif'
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menambahkan anggota.');
        }

        return redirect()->to('/anggota')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_anggota')) return redirect()->to('/dashboard');

        $anggota = $this->anggotaModel->find($id);
        $user = $this->userModel->find($anggota['user_id']);

        $data = [
            'title'   => 'Edit Anggota | Koperasi',
            'anggota' => $anggota,
            'user'    => $user,
            'kelompok'=> $this->kelompokModel->findAll()
        ];
        return view('anggota/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_anggota')) return redirect()->to('/dashboard');

        $anggota = $this->anggotaModel->find($id);

        $this->anggotaModel->update($id, [
            'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_telp'           => $this->request->getPost('no_telp'),
            'status'            => $this->request->getPost('status'),
            'jabatan'           => $this->request->getPost('jabatan'),
            'kelompok'          => $this->request->getPost('kelompok') ?? 'Umum',
        ]);

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $this->userModel->update($anggota['user_id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        }

        return redirect()->to('/anggota')->with('success', 'Anggota berhasil diupdate.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_anggota')) return redirect()->to('/dashboard');

        $anggota = $this->anggotaModel->find($id);
        if ($anggota) {
            $this->userModel->delete($anggota['user_id']);
        }
        return redirect()->to('/anggota')->with('success', 'Anggota berhasil dihapus.');
    }
}
