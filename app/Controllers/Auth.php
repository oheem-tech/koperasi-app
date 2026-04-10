<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        $data['title'] = 'Login | Koperasi Simpan Pinjam';
        return view('auth/login', $data);
    }

    public function process()
    {
        $db = \Config\Database::connect();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Check user in DB
        $builder = $db->table('users');
        $user = $builder->where('username', $username)->get()->getRowArray();

        // If not found by username, try to find by no_anggota
        if (!$user) {
            $anggota = $db->table('anggota')->where('no_anggota', $username)->get()->getRowArray();
            if ($anggota) {
                $user = $builder->where('id', $anggota['user_id'])->get()->getRowArray();
            }
        }

        if ($user) {
            if (password_verify($password, $user['password'])) {
                
                // Fetch permissions for this role
                $roleData = $db->table('roles')->where('name', $user['role'])->get()->getRowArray();
                $permissions = [];
                if ($roleData && !empty($roleData['permissions'])) {
                    $permissions = json_decode($roleData['permissions'], true);
                }
                
                $sessionData = [
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'role'       => $user['role'],
                    'permissions'=> $permissions,
                    'isLoggedIn' => true,
                ];
                session()->set($sessionData);
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Password salah.');
                return redirect()->to('/auth');
            }
        } else {
            session()->setFlashdata('error', 'Username/No Anggota tidak ditemukan.');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}
