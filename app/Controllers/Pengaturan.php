<?php

namespace App\Controllers;

use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        // Pastikan pengaturan profil/identitas koperasi ada
        $identitas = [
            ['pengaturan_key' => 'koperasi_nama',      'pengaturan_value' => 'Koperasi Bahagia Bersama', 'keterangan' => 'Nama Koperasi'],
            ['pengaturan_key' => 'koperasi_alamat',    'pengaturan_value' => 'Jl. Merdeka No. 1, Jakarta', 'keterangan' => 'Alamat Koperasi'],
            ['pengaturan_key' => 'koperasi_kota',      'pengaturan_value' => 'Jakarta',                   'keterangan' => 'Kota (dipakai di tanda tangan laporan)'],
            ['pengaturan_key' => 'koperasi_telepon',   'pengaturan_value' => '021-123456',                'keterangan' => 'Nomor Telepon'],
            ['pengaturan_key' => 'koperasi_ketua',     'pengaturan_value' => 'Ahmad Santoso',             'keterangan' => 'Nama Ketua Koperasi'],
            ['pengaturan_key' => 'koperasi_bendahara', 'pengaturan_value' => 'Budi Setiawan',             'keterangan' => 'Nama Bendahara'],
            ['pengaturan_key' => 'koperasi_pengawas',  'pengaturan_value' => 'Citra Lestari',             'keterangan' => 'Nama Pengawas'],
            ['pengaturan_key' => 'koperasi_pembina',   'pengaturan_value' => 'Doni Kusuma',               'keterangan' => 'Nama Pembina'],
            ['pengaturan_key' => 'koperasi_logo',      'pengaturan_value' => '',                          'keterangan' => 'File Logo (Format Gambar)'],
            ['pengaturan_key' => 'shu_metode_modal',   'pengaturan_value' => 'akumulasi_akhir',           'keterangan' => 'Metode Hitung Jasa Modal SHU'],
            ['pengaturan_key' => 'kode_lisensi',       'pengaturan_value' => '',                          'keterangan' => 'Kode Lisensi White-Label / Premium'],
            
            // Pengaturan WhatsApp Gateway (Fonnte)
            ['pengaturan_key' => 'wa_token',           'pengaturan_value' => '',                          'keterangan' => 'Token API WhatsApp Gateway (Fonnte)'],
            ['pengaturan_key' => 'wa_simpanan_aktif',  'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Simpanan (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_simpanan', 'pengaturan_value' => 'Halo {Nama}, Setoran Simpanan {Jenis} Anda sebesar Rp {Nominal} pada tanggal {Tanggal} telah berhasil kami terima. Salam, Koperasi.', 'keterangan' => 'Template Pesan Simpanan'],
            ['pengaturan_key' => 'wa_pinjaman_aktif',  'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Pencairan Pinjaman (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_pinjaman', 'pengaturan_value' => 'Halo {Nama}, Pinjaman Anda sebesar Rp {Nominal} telah dicairkan pada tanggal {Tanggal}.', 'keterangan' => 'Template Pesan Pinjaman'],
            ['pengaturan_key' => 'wa_angsuran_aktif',  'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Pembayaran Angsuran (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_angsuran', 'pengaturan_value' => 'Halo {Nama}, Pembayaran Angsuran ke-{CicilanKe} sebesar Rp {Nominal} telah berhasil diterima pada tanggal {Tanggal}. Sisa tagihan Anda: Rp {Sisa}.', 'keterangan' => 'Template Pesan Angsuran'],
        ];

        foreach ($identitas as $idData) {
            if (!$this->pengaturanModel->where('pengaturan_key', $idData['pengaturan_key'])->first()) {
                $this->pengaturanModel->insert($idData);
            }
        }

        $data = [
            'title' => 'Pengaturan Koperasi',
            'pengaturan' => $this->pengaturanModel->findAll()
        ];
        return view('pengaturan/index', $data);
    }

    public function update()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $settings = $this->request->getPost('settings');
        
        // Handle logo upload separately
        $logoFile = $this->request->getFile('koperasi_logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $newName = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads/logo', $newName);
            
            $logoRow = $this->pengaturanModel->where('pengaturan_key', 'koperasi_logo')->first();
            if ($logoRow) {
                // Delete old logo if exists
                if (!empty($logoRow['pengaturan_value']) && file_exists(FCPATH . 'uploads/logo/' . $logoRow['pengaturan_value'])) {
                    unlink(FCPATH . 'uploads/logo/' . $logoRow['pengaturan_value']);
                }
                $this->pengaturanModel->update($logoRow['id'], ['pengaturan_value' => $newName]);
            }
        }

        if ($settings) {
            foreach ($settings as $id => $val) {
                $this->pengaturanModel->update($id, ['pengaturan_value' => $val]);
            }
        }
        return redirect()->to('/pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');

        return redirect()->to('/pengaturan')->with('error', 'Tidak ada data yang diperbarui.');
    }
}
