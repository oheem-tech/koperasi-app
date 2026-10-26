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
            
            // Pengaturan WA Chatbot Inbound (Anggota tanya via WA)
            ['pengaturan_key' => 'wa_chatbot_aktif',  'pengaturan_value' => '0', 'keterangan' => 'Fitur WA Chatbot Inbound (Anggota cek saldo/pinjaman via WA) (1=Aktif, 0=Nonaktif)'],
        ];

        foreach ($identitas as $idData) {
            if (!$this->pengaturanModel->where('pengaturan_key', $idData['pengaturan_key'])->first()) {
                $this->pengaturanModel->insert($idData);
            }
        }

                // Pastikan parameter dana pendidikan ada jika belum pernah dibuat
        if (!$this->pengaturanModel->where('pengaturan_key', 'shu_dana_pendidikan')->first()) {
            $this->pengaturanModel->insert([
                'pengaturan_key'   => 'shu_dana_pendidikan',
                'pengaturan_value' => '5.5',
                'keterangan'       => 'Persentase Pembagian SHU untuk Dana Pendidikan (%)',
            ]);
        }

        // Hapus duplikat parameter lama shu_dana_cadangan jika shu_cadangan sudah ada
        $cadangan = $this->pengaturanModel->where('pengaturan_key', 'shu_cadangan')->first();
        if ($cadangan) {
            $this->pengaturanModel->where('pengaturan_key', 'shu_dana_cadangan')->delete();
        }

        // Pastikan parameter bawaan pembagian internal pengurus ada jika belum pernah dibuat
        $hasPengurus = $this->pengaturanModel
            ->like('pengaturan_key', 'shu_pengurus_', 'after')
            ->where('pengaturan_key !=', 'shu_pengurus_anggota')
            ->first();
        if (!$hasPengurus) {
            $defaultPengurus = [
                ['pengaturan_key' => 'shu_pengurus_ketua', 'pengaturan_value' => '34', 'keterangan' => 'Porsi SHU Pengurus - Ketua (%)'],
                ['pengaturan_key' => 'shu_pengurus_bendahara', 'pengaturan_value' => '33', 'keterangan' => 'Porsi SHU Pengurus - Bendahara (%)'],
                ['pengaturan_key' => 'shu_pengurus_sekretaris', 'pengaturan_value' => '33', 'keterangan' => 'Porsi SHU Pengurus - Sekretaris (%)'],
            ];
            foreach ($defaultPengurus as $dp) {
                $this->pengaturanModel->insert($dp);
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
            // Validasi persentase Distribusi SHU Koperasi (Level 1) & Pembagian Internal Pengurus (Level 2)
            $shuRows = $this->pengaturanModel
                ->whereIn('id', array_keys($settings))
                ->like('pengaturan_key', 'shu_', 'after')
                ->where('pengaturan_key !=', 'shu_metode_modal')
                ->findAll();

            if (!empty($shuRows)) {
                $totalGlobal = 0;
                $totalPengurus = 0;

                foreach ($shuRows as $sr) {
                    $k = $sr['pengaturan_key'];
                    $val = floatval(str_replace(',', '.', $settings[$sr['id']] ?? 0));

                    if (strpos($k, 'shu_pengurus_') === 0 && $k !== 'shu_pengurus_anggota') {
                        // Level 2: Pembagian Internal Pengurus
                        $totalPengurus += $val;
                    } else {
                        // Level 1: Alokasi SHU Global Koperasi
                        $totalGlobal += $val;
                    }
                }

                $totalGlobal = round($totalGlobal, 2);
                $totalPengurus = round($totalPengurus, 2);

                if ($totalGlobal > 100.0) {
                    $lebih = round($totalGlobal - 100, 2);
                    return redirect()->to('/pengaturan')->with('error', "Gagal menyimpan: Total alokasi Distribusi SHU Koperasi adalah {$totalGlobal}%, melebihi batas maksimal 100% (Kelebihan {$lebih}%). Harap sesuaikan persentase alokasi utama.");
                }

                if ($totalPengurus > 100.0) {
                    $lebih = round($totalPengurus - 100, 2);
                    return redirect()->to('/pengaturan')->with('error', "Gagal menyimpan: Total Pembagian Internal Pengurus adalah {$totalPengurus}%, melebihi batas maksimal 100% (Kelebihan {$lebih}%). Harap sesuaikan persentase porsi jabatan pengurus.");
                }
            }

            foreach ($settings as $id => $val) {
                $this->pengaturanModel->update($id, ['pengaturan_value' => $val]);
            }
        }
        if (function_exists('catat_log')) {
            catat_log('Edit Pengaturan', 'Mengubah konfigurasi / pengaturan sistem');
        }

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function add_parameter()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');
        
        $nama = trim($this->request->getPost('nama_jabatan') ?? '');
        $nilai = floatval(str_replace(',', '.', $this->request->getPost('nilai') ?? 0));
        
        if ($nama) {
            $key = 'shu_pengurus_' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $nama));
            
            // Cek apakah sudah ada
            $exists = $this->pengaturanModel->where('pengaturan_key', $key)->first();
            if ($exists) {
                return redirect()->to('/pengaturan')->with('error', 'Parameter pengurus tersebut sudah ada.');
            }

            // Validasi agar total pembagian internal pengurus tidak melebihi 100%
            $pengurusRows = $this->pengaturanModel
                ->like('pengaturan_key', 'shu_pengurus_', 'after')
                ->where('pengaturan_key !=', 'shu_pengurus_anggota')
                ->findAll();

            $currentTotal = 0;
            foreach ($pengurusRows as $sr) {
                $currentTotal += floatval(str_replace(',', '.', $sr['pengaturan_value']));
            }
            $currentTotal = round($currentTotal, 2);
            $newTotal = round($currentTotal + $nilai, 2);

            if ($newTotal > 100.0) {
                $kelebihan = round($newTotal - 100, 2);
                return redirect()->to('/pengaturan')->with('error', "Gagal menambahkan: Total pembagian internal pengurus saat ini ({$currentTotal}%) jika ditambah jabatan baru ({$nilai}%) menjadi {$newTotal}%, melebihi batas 100% (Kelebihan {$kelebihan}%).");
            }

            $this->pengaturanModel->insert([
                'pengaturan_key' => $key,
                'pengaturan_value' => $nilai,
                'keterangan' => 'Persentase SHU Pengurus - ' . ucwords($nama) . ' (%)'
            ]);
            return redirect()->to('/pengaturan')->with('success', 'Parameter jabatan pengurus berhasil ditambahkan.');
        }
        return redirect()->to('/pengaturan')->with('error', 'Nama jabatan tidak boleh kosong.');
    }

    public function delete_parameter($id)
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $param = $this->pengaturanModel->find($id);
        if (!$param) {
            return redirect()->to('/pengaturan')->with('error', 'Parameter tidak ditemukan.');
        }

        // Hanya boleh menghapus parameter dinamis shu_pengurus_... yang bukan parameter bawaan sistem
        $coreKeys = [
            'shu_jasa_modal', 'shu_jasa_anggota', 'shu_cadangan', 
            'shu_pengurus_anggota', 'shu_pengawas', 'shu_pembina', 
            'shu_dana_sosial', 'shu_dana_pendidikan', 'shu_metode_modal'
        ];

        if (in_array($param['pengaturan_key'], $coreKeys)) {
            return redirect()->to('/pengaturan')->with('error', 'Parameter standar sistem tidak dapat dihapus.');
        }

        if (strpos($param['pengaturan_key'], 'shu_pengurus_') !== 0) {
            return redirect()->to('/pengaturan')->with('error', 'Parameter ini tidak dapat dihapus.');
        }

        $this->pengaturanModel->delete($id);
        if (function_exists('catat_log')) {
            catat_log('Hapus Parameter Pengaturan', 'Menghapus parameter SHU: ' . $param['pengaturan_key']);
        }

        return redirect()->to('/pengaturan')->with('success', 'Parameter dinamis berhasil dihapus.');
    }
}
