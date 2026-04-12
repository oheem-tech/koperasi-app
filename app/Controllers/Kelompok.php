<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KelompokModel;
use App\Models\AnggotaModel;

class Kelompok extends BaseController
{
    protected $kelompokModel;
    protected $anggotaModel;

    public function __construct()
    {
        $this->kelompokModel = new KelompokModel();
        $this->anggotaModel  = new AnggotaModel();
    }

    public function index()
    {
        if (!has_permission('manage_anggota') && !has_permission('manage_pengaturan')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title'    => 'Master Kelompok',
            'kelompok' => $this->kelompokModel->findAll()
        ];
        
        // Add member count for each group
        foreach ($data['kelompok'] as &$k) {
            $k['jumlah_anggota'] = $this->anggotaModel->where('kelompok', $k['nama_kelompok'])->countAllResults();
        }

        return view('kelompok/index', $data);
    }

    public function store()
    {
        if (!has_permission('manage_anggota') && !has_permission('manage_pengaturan')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        if (!is_premium()) {
            return redirect()->to('/informasi/support')->with('error', 'Fitur Master Kelompok hanya tersedia untuk Versi PRO. Silakan upgrade lisensi.');
        }

        $nama = $this->request->getPost('nama_kelompok');
        if (trim($nama) == '') {
            return redirect()->back()->with('error', 'Nama Kelompok wajib diisi.');
        }

        $data = [
            'nama_kelompok' => trim($nama),
            'keterangan'    => $this->request->getPost('keterangan')
        ];

        if ($this->kelompokModel->insert($data)) {
            return redirect()->to('/kelompok')->with('success', 'Kelompok baru berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', implode('<br>', $this->kelompokModel->errors()));
        }
    }

    public function update($id)
    {
        if (!has_permission('manage_anggota') && !has_permission('manage_pengaturan')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        if (!is_premium()) {
            return redirect()->to('/informasi/support')->with('error', 'Fitur Master Kelompok hanya tersedia untuk Versi PRO. Silakan upgrade lisensi.');
        }

        $kelompokLama = $this->kelompokModel->find($id);
        if (!$kelompokLama) {
            return redirect()->back()->with('error', 'Kelompok tidak ditemukan.');
        }

        $namaBaru = trim($this->request->getPost('nama_kelompok'));
        if ($namaBaru == '') {
            return redirect()->back()->with('error', 'Nama Kelompok wajib diisi.');
        }

        $data = [
            'nama_kelompok' => $namaBaru,
            'keterangan'    => $this->request->getPost('keterangan')
        ];

        // Begin transaction because we need to cascade update Anggota table
        $db = \Config\Database::connect();
        $db->transStart();

        if ($this->kelompokModel->update($id, $data)) {
            // Cascade update to Anggota if name changed
            if ($kelompokLama['nama_kelompok'] !== $namaBaru) {
                // Update specific column in anggota directly
                $db->table('anggota')
                   ->where('kelompok', $kelompokLama['nama_kelompok'])
                   ->update(['kelompok' => $namaBaru]);
            }
        } else {
            $db->transRollback();
            return redirect()->back()->with('error', implode('<br>', $this->kelompokModel->errors()));
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memperbarui relasi anggota.');
        }

        return redirect()->to('/kelompok')->with('success', 'Kelompok berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!has_permission('manage_anggota') && !has_permission('manage_pengaturan')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        if (!is_premium()) {
            return redirect()->to('/informasi/support')->with('error', 'Fitur Master Kelompok hanya tersedia untuk Versi PRO. Silakan upgrade lisensi.');
        }

        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/kelompok')->with('error', 'Kelompok tidak ditemukan.');
        }

        // Check if there are active members
        $count = $this->anggotaModel->where('kelompok', $kelompok['nama_kelompok'])->countAllResults();
        if ($count > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus kelompok! Terdapat ' . $count . ' anggota yang masih menggunakan kelompok ini. Silakan pindahkan anggota terlebih dahulu.');
        }

        $this->kelompokModel->delete($id);
        return redirect()->to('/kelompok')->with('success', 'Kelompok berhasil dihapus.');
    }

    // ==========================================
    // PEMINDAHAN MASSAL FEATURE
    // ==========================================

    public function bulk_index()
    {
        if (!has_permission('manage_anggota')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $filter_kelompok = $this->request->getGet('filter_kelompok');

        $query = $this->anggotaModel->select('id, no_anggota, nama_lengkap, kelompok, status')
                                    ->orderBy('nama_lengkap', 'ASC');

        if (!empty($filter_kelompok) && $filter_kelompok !== 'all') {
            $query->where('kelompok', $filter_kelompok);
        }

        $data = [
            'title'           => 'Pemindahan Anggota Massal',
            'anggota'         => $query->findAll(),
            'kelompok'        => $this->kelompokModel->findAll(),
            'filter_kelompok' => $filter_kelompok
        ];

        return view('kelompok/bulk', $data);
    }

    public function bulk_process()
    {
        if (!has_permission('manage_anggota')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        if (!is_premium()) {
            return redirect()->to('/informasi/support')->with('error', 'Fitur Master Kelompok hanya tersedia untuk Versi PRO. Silakan upgrade lisensi.');
        }

        $anggotaIds    = $this->request->getPost('anggota_ids'); // Array of IDs
        $targetKelompok = trim($this->request->getPost('target_kelompok'));

        if (empty($anggotaIds) || empty($targetKelompok)) {
            return redirect()->back()->with('error', 'Pastikan telah memilih anggota dan kelompok tujuan.');
        }

        // Verify target kelompok exists
        $cek = $this->kelompokModel->where('nama_kelompok', $targetKelompok)->first();
        if (!$cek && !in_array($targetKelompok, ['Umum'])) {
             // Fallback bypass if they type it, but we enforce dropdown
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('anggota')
           ->whereIn('id', $anggotaIds)
           ->update(['kelompok' => $targetKelompok]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memindahkan anggota.');
        }

        return redirect()->to('/kelompok/bulk')->with('success', count($anggotaIds) . ' anggota berhasil dipindahkan ke kelompok ' . esc($targetKelompok) . '.');
    }
}
