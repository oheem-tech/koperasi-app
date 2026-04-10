<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class User extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        if (!has_permission('manage_roles')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil semua user kecuali yang ber-role 'anggota', karena ini menu manajemen staf/pengurus
        // Atau ambil semua user tapi tandai mana yang terhubung ke anggota
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*, anggota.nama_lengkap, anggota.no_anggota');
        $builder->join('anggota', 'anggota.user_id = users.id', 'left');
        $builder->orderBy('users.role', 'ASC');
        $builder->orderBy('users.username', 'ASC');

        $users = $builder->get()->getResultArray();

        $data = [
            'title' => 'Manajemen User & Staf',
            'users' => $users
        ];
        return view('user/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $data = [
            'title' => 'Tambah User Sistem',
            'roles' => $this->roleModel->where('name !=', 'anggota')->findAll()
        ];
        return view('user/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $username = trim($this->request->getPost('username'));
        
        // Cek username duplikat
        if ($this->userModel->where('username', $username)->first()) {
            return redirect()->back()->with('error', 'Username sudah digunakan, silakan pilih yang lain.');
        }

        $this->userModel->save([
            'username' => $username,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role')
        ]);

        return redirect()->to('/user')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to('/user')->with('error', 'User tidak ditemukan.');

        $data = [
            'title' => 'Edit User',
            'user'  => $user,
            'roles' => $this->roleModel->findAll() // Tampilkan semua role termasuk anggota jika dia aslinya anggota
        ];
        return view('user/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to('/user')->with('error', 'User tidak ditemukan.');

        $username = trim($this->request->getPost('username'));

        // Cek username duplikat jika berubah
        if ($username !== $user['username']) {
            if ($this->userModel->where('username', $username)->first()) {
                return redirect()->back()->with('error', 'Username sudah terpakai.');
            }
        }

        $updateData = [
            'username' => $username,
            'role'     => $this->request->getPost('role')
        ];

        // Mencegah admin mendowngrade dirinya sendiri
        if ($id == session()->get('user_id') && $updateData['role'] != 'admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri dari Admin menjadi role lain.');
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->to('/user')->with('success', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_roles')) return redirect()->to('/dashboard');

        if ($id == session()->get('user_id')) {
            return redirect()->to('/user')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = $this->userModel->find($id);
        if ($user) {
            // Cek apakah dia terikat dengan anggota
            $db = \Config\Database::connect();
            $anggota = $db->table('anggota')->where('user_id', $id)->get()->getRowArray();
            
            if ($anggota) {
                return redirect()->to('/user')->with('error', 'Gagal: Konfigurasi ini adalah akun Anggota. Silakan hapus via Data Anggota.');
            }

            $this->userModel->delete($id);
            return redirect()->to('/user')->with('success', 'User sistem berhasil dihapus.');
        }

        return redirect()->to('/user')->with('error', 'Data tidak ditemukan.');
    }
}
