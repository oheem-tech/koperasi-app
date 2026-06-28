<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\KasKoperasiModel;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $kasModel = new KasKoperasiModel();

        // 1. Jenis Simpanan
        $jenisSimpananData = [
            ['nama_simpanan' => 'Simpanan Pokok', 'minimal_setoran' => 100000],
            ['nama_simpanan' => 'Simpanan Wajib', 'minimal_setoran' => 50000],
            ['nama_simpanan' => 'Simpanan Sukarela', 'minimal_setoran' => 10000],
        ];
        foreach ($jenisSimpananData as $js) {
            if ($this->db->table('jenis_simpanan')->where('nama_simpanan', $js['nama_simpanan'])->countAllResults() == 0) {
                $this->db->table('jenis_simpanan')->insert($js);
            }
        }

        // 2. Tiga User Anggota
        $usersData = [
            ['username' => 'budi123', 'password' => password_hash('budi123', PASSWORD_DEFAULT), 'role' => 'anggota', 'created_at' => date('Y-m-d H:i:s')],
            ['username' => 'citra456', 'password' => password_hash('citra456', PASSWORD_DEFAULT), 'role' => 'anggota', 'created_at' => date('Y-m-d H:i:s')],
            ['username' => 'dewi789', 'password' => password_hash('dewi789', PASSWORD_DEFAULT), 'role' => 'anggota', 'created_at' => date('Y-m-d H:i:s')]
        ];
        
        $userIds = [];
        foreach ($usersData as $u) {
            if ($this->db->table('users')->where('username', $u['username'])->countAllResults() == 0) {
                $this->db->table('users')->insert($u);
                $userIds[] = $this->db->insertID();
            } else {
                $userIds[] = $this->db->table('users')->where('username', $u['username'])->get()->getRow()->id;
            }
        }

        // Generate Master Kelompok jika belum ada
        if ($this->db->table('master_kelompok')->countAllResults() == 0) {
            $now = date('Y-m-d H:i:s');
            $seeds = [
                ['nama_kelompok' => 'Umum', 'keterangan' => 'Kelompok standar untuk anggota umum', 'created_at' => $now],
                ['nama_kelompok' => 'ASN/PNS', 'keterangan' => 'Pegawai Negeri Sipil & ASN', 'created_at' => $now],
            ];
            $this->db->table('master_kelompok')->insertBatch($seeds);
        }

        // 3. Data Anggota
        if (!empty($userIds) && count($userIds) >= 3) {
            $anggotaData = [
                [
                    'user_id' => $userIds[0], 'no_anggota' => 'A-0001', 'nama_lengkap' => 'Budi Santoso', 
                    'alamat' => 'Jl. Mawar No. 12', 'no_telp' => '081234567890', 'tanggal_bergabung' => date('Y-m-d', strtotime('-6 months')), 
                    'status' => 'aktif', 'jabatan' => 'anggota', 'kelompok' => 'Umum', 'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[1], 'no_anggota' => 'A-0002', 'nama_lengkap' => 'Citra Lestari', 
                    'alamat' => 'Jl. Melati No. 45', 'no_telp' => '089876543210', 'tanggal_bergabung' => date('Y-m-d', strtotime('-3 months')), 
                    'status' => 'aktif', 'jabatan' => 'anggota', 'kelompok' => 'Umum', 'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[2], 'no_anggota' => 'A-0003', 'nama_lengkap' => 'Dewi Kirana', 
                    'alamat' => 'Jl. Anggrek No. 8', 'no_telp' => '087766554433', 'tanggal_bergabung' => date('Y-m-d', strtotime('-1 months')), 
                    'status' => 'aktif', 'jabatan' => 'anggota', 'kelompok' => 'Umum', 'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            $anggotaIds = [];
            foreach ($anggotaData as $a) {
                if ($this->db->table('anggota')->where('no_anggota', $a['no_anggota'])->countAllResults() == 0) {
                    $this->db->table('anggota')->insert($a);
                    $anggotaIds[] = $this->db->insertID();
                } else {
                    $anggotaIds[] = $this->db->table('anggota')->where('no_anggota', $a['no_anggota'])->get()->getRow()->id;
                }
            }

            // 4. Kas Awal (Modal)
            if ($this->db->table('kas_koperasi')->countAllResults() == 0) {
                $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Modal Awal Koperasi', 'masuk', 50000000, 'modal_awal');
            }

            $simpananPokokRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Pokok')->get()->getRow();
            $simpananWajibRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Wajib')->get()->getRow();
            $sp_id = $simpananPokokRow ? $simpananPokokRow->id : 1;
            $sw_id = $simpananWajibRow ? $simpananWajibRow->id : 2;

            // 5. Data Simpanan Anggota 1
            if ($this->db->table('simpanan')->where('anggota_id', $anggotaIds[0])->countAllResults() == 0) {
                $kasId1 = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Simpanan Pokok Budi Santoso', 'masuk', 100000, 'simpanan');
                $kasId2 = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Simpanan Wajib Budi Santoso', 'masuk', 50000, 'simpanan');

                $this->db->table('simpanan')->insertBatch([
                    [
                        'anggota_id' => $anggotaIds[0], 'jenis_simpanan_id' => $sp_id, 'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')), 
                        'jumlah' => 100000, 'jenis_transaksi' => 'setor', 'keterangan' => 'Setoran Awal Pokok', 'kas_id' => $kasId1, 'created_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'anggota_id' => $anggotaIds[0], 'jenis_simpanan_id' => $sw_id, 'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')), 
                        'jumlah' => 50000, 'jenis_transaksi' => 'setor', 'keterangan' => 'Setoran Wajib Bulan 1', 'kas_id' => $kasId2, 'created_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            }

            // Simpanan Citra dan Dewi
            if ($this->db->table('simpanan')->where('anggota_id', $anggotaIds[1])->countAllResults() == 0) {
                $kasId3 = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-3 months')), 'Simpanan Pokok Citra Lestari', 'masuk', 100000, 'simpanan');
                $kasId4 = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-1 months')), 'Simpanan Pokok Dewi Kirana', 'masuk', 100000, 'simpanan');

                $this->db->table('simpanan')->insertBatch([
                    [
                        'anggota_id' => $anggotaIds[1], 'jenis_simpanan_id' => $sp_id, 'tanggal_transaksi' => date('Y-m-d', strtotime('-3 months')), 
                        'jumlah' => 100000, 'jenis_transaksi' => 'setor', 'keterangan' => 'Setoran Pokok Citra', 'kas_id' => $kasId3, 'created_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'anggota_id' => $anggotaIds[2], 'jenis_simpanan_id' => $sp_id, 'tanggal_transaksi' => date('Y-m-d', strtotime('-1 months')), 
                        'jumlah' => 100000, 'jenis_transaksi' => 'setor', 'keterangan' => 'Setoran Pokok Dewi', 'kas_id' => $kasId4, 'created_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            }

            // 6. Data Pinjaman Anggota 1
            if ($this->db->table('pinjaman')->where('anggota_id', $anggotaIds[0])->countAllResults() == 0) {
                $this->db->table('pinjaman')->insert([
                    'anggota_id' => $anggotaIds[0], 'jenis_pinjaman' => 'uang', 'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 months')), 
                    'jumlah_pinjaman' => 5000000, 'lama_tenor' => 10, 'bunga_persen' => 1.5, 'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+9 months')), 
                    'status' => 'aktif', 'created_at' => date('Y-m-d H:i:s')
                ]);
                $pinjamanId = $this->db->insertID();

                // Kas keluar (Pinjaman)
                $kasModel->catatTransaksi(date('Y-m-d', strtotime('-1 months')), 'Pencairan Pinjaman Budi Santoso', 'keluar', 5000000, 'pinjaman');
                
                // 7. Angsuran Pinjaman (Angsuran ke-1 sudah dibayar)
                $kasIdAngsuran = $kasModel->catatTransaksi(date('Y-m-d'), 'Angsuran Pembayaran ke-1 Budi Santoso', 'masuk', 575000, 'angsuran');
                
                $this->db->table('angsuran')->insert([
                    'pinjaman_id' => $pinjamanId, 'tanggal_bayar' => date('Y-m-d'), 'jumlah_bayar' => 575000, 
                    'jumlah_pokok' => 500000, 'jumlah_jasa' => 75000, 'denda' => 0, 'cicilan_ke' => 1, 'kas_id' => $kasIdAngsuran, 
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Set kebijakan pelunasan aktif ke 0 secara khusus untuk data demo
            $this->db->table('pengaturan')->where('pengaturan_key', 'kebijakan_pelunasan_aktif')->update(['pengaturan_value' => '0']);
            echo "✅ Data Demo berhasil di-seed dengan integrasi Kas (kas_id) yang benar\n";
        }
    }
}
