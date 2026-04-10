<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profil extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Profil Saya | Koperasi',
            'username' => session()->get('username'),
            'role'     => session()->get('role'),
        ];
        return view('profil/index', $data);
    }

    public function update_password()
    {
        $userId = session()->get('user_id');
        $passwordLama  = $this->request->getPost('password_lama');
        $passwordBaru  = $this->request->getPost('password_baru');
        $konfirmasi    = $this->request->getPost('konfirmasi_password');

        // 1. Ambil data user dari DB
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // 2. Verifikasi password lama
        if (!password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        // 3. Validasi password baru
        if (strlen($passwordBaru) < 6) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
        }
        if ($passwordBaru !== $konfirmasi) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        // 4. Update password
        $this->userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui. Silakan login ulang untuk keamanan.');
    }
}
