<?php

namespace App\Controllers;

class Informasi extends BaseController
{
    public function fitur()
    {
        if (session()->get('role') === 'anggota') {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
            return redirect()->to('/dashboard');
        }

        $data = ['title' => 'Detail Fitur Koperasi'];
        return view('informasi/fitur', $data);
    }

    public function panduan()
    {
        if (session()->get('role') === 'anggota') {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
            return redirect()->to('/dashboard');
        }

        $data = ['title' => 'Panduan Operasional'];
        return view('informasi/panduan', $data);
    }

    public function support()
    {
        if (session()->get('role') === 'anggota') {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
            return redirect()->to('/dashboard');
        }

        $data = ['title' => 'Kustomisasi & Bantuan Teknis'];
        return view('informasi/support', $data);
    }
}
