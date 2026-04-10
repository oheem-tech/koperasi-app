<?php

namespace App\Controllers;

use App\Models\JenisSimpananModel;

class JenisSimpanan extends BaseController
{
    protected $jenisSimpananModel;

    public function __construct()
    {
        $this->jenisSimpananModel = new JenisSimpananModel();
    }

    public function index()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $data = [
            'title' => 'Jenis Simpanan | Koperasi',
            'simpanan' => $this->jenisSimpananModel->findAll()
        ];
        return view('jenis_simpanan/index', $data);
    }

    public function create()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $data = ['title' => 'Tambah Jenis Simpanan | Koperasi'];
        return view('jenis_simpanan/create', $data);
    }

    public function store()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $this->jenisSimpananModel->save([
            'nama_simpanan'   => $this->request->getPost('nama_simpanan'),
            'minimal_setoran' => str_replace('.', '', $this->request->getPost('minimal_setoran'))
        ]);

        return redirect()->to('/jenis-simpanan')->with('success', 'Jenis simpanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $data = [
            'title' => 'Edit Jenis Simpanan | Koperasi',
            'simpanan' => $this->jenisSimpananModel->find($id)
        ];
        return view('jenis_simpanan/edit', $data);
    }

    public function update($id)
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $this->jenisSimpananModel->update($id, [
            'nama_simpanan'   => $this->request->getPost('nama_simpanan'),
            'minimal_setoran' => str_replace('.', '', $this->request->getPost('minimal_setoran'))
        ]);

        return redirect()->to('/jenis-simpanan')->with('success', 'Jenis simpanan berhasil diubah.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $this->jenisSimpananModel->delete($id);
        return redirect()->to('/jenis-simpanan')->with('success', 'Jenis simpanan berhasil dihapus.');
    }
}
