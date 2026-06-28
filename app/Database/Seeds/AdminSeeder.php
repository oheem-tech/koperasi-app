<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // ── 1. Buat user admin ──────────────────────────────────────────
        $this->db->table('users')->insert([
            'username'   => 'admin',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // ── 2. Isi tabel roles (jika belum ada datanya) ─────────────────
        $roles = [
            [
                'name'        => 'admin',
                'description' => 'Super Administrator dengan hak akses penuh',
                'permissions' => json_encode([
                    'manage_anggota', 'manage_simpanan', 'manage_pinjaman',
                    'manage_angsuran', 'manage_kas', 'view_laporan',
                    'manage_pengaturan', 'manage_backup', 'manage_roles', 'view_log'
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'anggota',
                'description' => 'Anggota Koperasi Biasa (Akses Terbatas)',
                'permissions' => json_encode(['view_simpanan', 'view_pinjaman']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'bendahara',
                'description' => 'Bendahara (Penyelaras Kas dan Pembukuan)',
                'permissions' => json_encode([
                    'manage_anggota', 'manage_simpanan', 'manage_pinjaman',
                    'manage_angsuran', 'manage_kas', 'view_laporan'
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'ketua',
                'description' => 'Ketua Koperasi',
                'permissions' => json_encode([
                    'manage_anggota', 'manage_simpanan', 'manage_kas',
                    'view_laporan', 'manage_pengaturan', 'manage_backup'
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($roles as $role) {
            // Cek agar tidak duplikat jika seeder dijalankan ulang
            $exists = $this->db->table('roles')->where('name', $role['name'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('roles')->insert($role);
            }
        }

        // ── 3. Data default: jenis_simpanan ────────────────────────────
        $jenisSimpanan = [
            ['nama_simpanan' => 'Simpanan Pokok',    'minimal_setoran' => 100000],
            ['nama_simpanan' => 'Simpanan Wajib',    'minimal_setoran' => 50000],
            ['nama_simpanan' => 'Simpanan Sukarela', 'minimal_setoran' => 10000],
        ];
        foreach ($jenisSimpanan as $js) {
            $exists = $this->db->table('jenis_simpanan')->where('nama_simpanan', $js['nama_simpanan'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('jenis_simpanan')->insert($js);
            }
        }

        // ── 4. Data default: master_kelompok ───────────────────────────
        $kelompok = [
            ['nama_kelompok' => 'Umum',      'keterangan' => 'Kelompok standar untuk anggota umum',        'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'ASN/PNS',   'keterangan' => 'Pegawai Negeri Sipil & ASN',                 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'PPPK',      'keterangan' => 'Pegawai Pemerintah dengan Perjanjian Kerja', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Honorer',   'keterangan' => 'Tenaga Honorer / Kontrak',                   'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Pensiunan', 'keterangan' => 'Anggota yang telah pensiun',                 'created_at' => date('Y-m-d H:i:s')],
        ];
        foreach ($kelompok as $k) {
            $exists = $this->db->table('master_kelompok')->where('nama_kelompok', $k['nama_kelompok'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('master_kelompok')->insert($k);
            }
        }
    }
}
