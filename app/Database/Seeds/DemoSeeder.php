<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
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
            [
                'username' => 'budi123',
                'password' => password_hash('budi123', PASSWORD_DEFAULT),
                'role' => 'anggota',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'citra456',
                'password' => password_hash('citra456', PASSWORD_DEFAULT),
                'role' => 'anggota',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'dewi789',
                'password' => password_hash('dewi789', PASSWORD_DEFAULT),
                'role' => 'anggota',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
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

        // 3. Data Anggota
        if (!empty($userIds) && count($userIds) >= 3) {
            $anggotaData = [
                [
                    'user_id' => $userIds[0],
                    'no_anggota' => 'A-0001',
                    'nama_lengkap' => 'Budi Santoso',
                    'alamat' => 'Jl. Mawar No. 12',
                    'no_telp' => '081234567890',
                    'tanggal_bergabung' => date('Y-m-d', strtotime('-6 months')),
                    'status' => 'aktif',
                    'jabatan' => 'anggota',
                    'kelompok' => 'Umum', // Umum used directly by name instead of id
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[1],
                    'no_anggota' => 'A-0002',
                    'nama_lengkap' => 'Citra Lestari',
                    'alamat' => 'Jl. Melati No. 45',
                    'no_telp' => '089876543210',
                    'tanggal_bergabung' => date('Y-m-d', strtotime('-3 months')),
                    'status' => 'aktif',
                    'jabatan' => 'anggota',
                    'kelompok' => 'Umum',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[2],
                    'no_anggota' => 'A-0003',
                    'nama_lengkap' => 'Dewi Kirana',
                    'alamat' => 'Jl. Anggrek No. 8',
                    'no_telp' => '087766554433',
                    'tanggal_bergabung' => date('Y-m-d', strtotime('-1 months')),
                    'status' => 'aktif',
                    'jabatan' => 'anggota',
                    'kelompok' => 'Umum',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
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
                $this->db->table('kas_koperasi')->insert([
                    'tanggal' => date('Y-m-d', strtotime('-6 months')),
                    'keterangan' => 'Modal Awal Koperasi',
                    'jenis' => 'masuk',
                    'nominal' => 50000000,
                    'kategori' => 'modal_awal',
                    'saldo_akhir' => 50000000
                ]);
            }

            // Let's assume Simpanan Pokok ID is 1, Wajib is 2
            $simpananPokokRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Pokok')->get()->getRow();
            $simpananWajibRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Wajib')->get()->getRow();
            
            $sp_id = $simpananPokokRow ? $simpananPokokRow->id : 1;
            $sw_id = $simpananWajibRow ? $simpananWajibRow->id : 2;

            // 5. Data Simpanan Anggota 1
            if ($this->db->table('simpanan')->where('anggota_id', $anggotaIds[0])->countAllResults() == 0) {
                $this->db->table('simpanan')->insertBatch([
                    [
                        'anggota_id' => $anggotaIds[0],
                        'jenis_simpanan_id' => $sp_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')),
                        'jumlah' => 100000,
                        'jenis_transaksi' => 'setor',
                        'keterangan' => 'Setoran Awal Pokok',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'anggota_id' => $anggotaIds[0],
                        'jenis_simpanan_id' => $sw_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')),
                        'jumlah' => 50000,
                        'jenis_transaksi' => 'setor',
                        'keterangan' => 'Setoran Wajib Bulan 1',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);

                // Update saldo kas
                $saldoKas = $this->db->table('kas_koperasi')->orderBy('id', 'DESC')->get()->getRow()->saldo_akhir ?? 50000000;
                $this->db->table('kas_koperasi')->insertBatch([
                    [
                        'tanggal' => date('Y-m-d', strtotime('-6 months')),
                        'keterangan' => 'Simpanan Pokok Budi Santoso',
                        'jenis' => 'masuk',
                        'nominal' => 100000,
                        'kategori' => 'simpanan_pokok',
                        'saldo_akhir' => $saldoKas + 100000
                    ],
                    [
                        'tanggal' => date('Y-m-d', strtotime('-6 months')),
                        'keterangan' => 'Simpanan Wajib Budi Santoso',
                        'jenis' => 'masuk',
                        'nominal' => 50000,
                        'kategori' => 'simpanan_wajib',
                        'saldo_akhir' => $saldoKas + 150000
                    ]
                ]);
            }

            // Simpanan Citra dan Dewi
            if ($this->db->table('simpanan')->where('anggota_id', $anggotaIds[1])->countAllResults() == 0) {
                $this->db->table('simpanan')->insertBatch([
                    [
                        'anggota_id' => $anggotaIds[1],
                        'jenis_simpanan_id' => $sp_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-3 months')),
                        'jumlah' => 100000,
                        'jenis_transaksi' => 'setor',
                        'keterangan' => 'Setoran Pokok Citra',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'anggota_id' => $anggotaIds[2],
                        'jenis_simpanan_id' => $sp_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-1 months')),
                        'jumlah' => 100000,
                        'jenis_transaksi' => 'setor',
                        'keterangan' => 'Setoran Pokok Dewi',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);
                $saldoKas = $this->db->table('kas_koperasi')->orderBy('id', 'DESC')->get()->getRow()->saldo_akhir ?? 50150000;
                $this->db->table('kas_koperasi')->insertBatch([
                    [
                        'tanggal' => date('Y-m-d', strtotime('-3 months')),
                        'keterangan' => 'Simpanan Pokok Citra Lestari',
                        'jenis' => 'masuk',
                        'nominal' => 100000,
                        'kategori' => 'simpanan_pokok',
                        'saldo_akhir' => $saldoKas + 100000
                    ],
                    [
                        'tanggal' => date('Y-m-d', strtotime('-1 months')),
                        'keterangan' => 'Simpanan Pokok Dewi Kirana',
                        'jenis' => 'masuk',
                        'nominal' => 100000,
                        'kategori' => 'simpanan_pokok',
                        'saldo_akhir' => $saldoKas + 200000
                    ]
                ]);
            }

            // 6. Data Pinjaman Anggota 1
            if ($this->db->table('pinjaman')->where('anggota_id', $anggotaIds[0])->countAllResults() == 0) {
                $this->db->table('pinjaman')->insert([
                    'anggota_id' => $anggotaIds[0],
                    'jenis_pinjaman' => 'uang',
                    'tanggal_pengajuan' => date('Y-m-d', strtotime('-1 months')),
                    'jumlah_pinjaman' => 5000000,
                    'lama_tenor' => 10,
                    'bunga_persen' => 1.5,
                    'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+9 months')),
                    'status' => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $pinjamanId = $this->db->insertID();

                // Kas keluar
                $saldoKas = $this->db->table('kas_koperasi')->orderBy('id', 'DESC')->get()->getRow()->saldo_akhir ?? 50150000;
                $this->db->table('kas_koperasi')->insert([
                    'tanggal' => date('Y-m-d', strtotime('-1 months')),
                    'keterangan' => 'Pencairan Pinjaman Budi Santoso',
                    'jenis' => 'keluar',
                    'nominal' => 5000000,
                    'kategori' => 'pinjaman_keluar',
                    'saldo_akhir' => $saldoKas - 5000000
                ]);
                
                // 7. Angsuran Pinjaman (Angsuran ke-1 sudah dibayar)
                $this->db->table('angsuran')->insert([
                    'pinjaman_id' => $pinjamanId,
                    'tanggal_bayar' => date('Y-m-d'),
                    'jumlah_bayar' => 575000, // 500rb pokok + 75rb bunga
                    'jumlah_pokok' => 500000,
                    'jumlah_jasa' => 75000,
                    'denda' => 0,
                    'cicilan_ke' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // Kas masuk untuk angsuran
                $saldoKas = $this->db->table('kas_koperasi')->orderBy('id', 'DESC')->get()->getRow()->saldo_akhir ?? 45150000;
                $this->db->table('kas_koperasi')->insertBatch([
                    [
                        'tanggal' => date('Y-m-d'),
                        'keterangan' => 'Angsuran Pokok ke-1 Budi Santoso',
                        'jenis' => 'masuk',
                        'nominal' => 500000,
                        'kategori' => 'angsuran_masuk',
                        'saldo_akhir' => $saldoKas + 500000
                    ],
                    [
                        'tanggal' => date('Y-m-d'),
                        'keterangan' => 'Pendapatan Jasa Pinjaman M-1 Budi Santoso',
                        'jenis' => 'masuk',
                        'nominal' => 75000,
                        'kategori' => 'pendapatan_jasa',
                        'saldo_akhir' => $saldoKas + 575000
                    ]
                ]);
            }
            
            // Generate Master Kelompok jika belum ada
            if ($this->db->table('master_kelompok')->countAllResults() == 0) {
                $now = date('Y-m-d H:i:s');
                $seeds = [
                    ['nama_kelompok' => 'Umum', 'keterangan' => 'Kelompok standar untuk anggota umum'],
                    ['nama_kelompok' => 'ASN/PNS', 'keterangan' => 'Pegawai Negeri Sipil & ASN'],
                ];
                foreach ($seeds as $s) {
                    $s['created_at'] = $now;
                    $this->db->table('master_kelompok')->insert($s);
                }
            }

            // Set kebijakan pelunasan aktif ke 0 secara khusus untuk data demo (gratis)
            $this->db->table('pengaturan')->where('pengaturan_key', 'kebijakan_pelunasan_aktif')->update(['pengaturan_value' => '0']);

            echo "✅ Data Demo berhasil di-seed (Anggota, Kas, Simpanan, Pinjaman, Angsuran)\n";
        }
    }
}
