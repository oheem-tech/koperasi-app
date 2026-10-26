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

        // 5. Data default: pengaturan awal koperasi
        $defaultPengaturan = [
            ['pengaturan_key' => 'koperasi_nama',             'pengaturan_value' => 'Koperasi Bahagia Bersama', 'keterangan' => 'Nama Koperasi'],
            ['pengaturan_key' => 'koperasi_alamat',           'pengaturan_value' => 'Jl. Merdeka No. 1, Jakarta', 'keterangan' => 'Alamat Koperasi'],
            ['pengaturan_key' => 'koperasi_kota',             'pengaturan_value' => 'Jakarta',                   'keterangan' => 'Kota (dipakai di tanda tangan laporan)'],
            ['pengaturan_key' => 'koperasi_telepon',          'pengaturan_value' => '021-123456',                'keterangan' => 'Nomor Telepon'],
            ['pengaturan_key' => 'koperasi_ketua',            'pengaturan_value' => 'Ahmad Santoso',             'keterangan' => 'Nama Ketua Koperasi'],
            ['pengaturan_key' => 'koperasi_bendahara',        'pengaturan_value' => 'Budi Setiawan',             'keterangan' => 'Nama Bendahara'],
            ['pengaturan_key' => 'koperasi_pengawas',         'pengaturan_value' => 'Citra Lestari',             'keterangan' => 'Nama Pengawas'],
            ['pengaturan_key' => 'koperasi_pembina',          'pengaturan_value' => 'Doni Kusuma',               'keterangan' => 'Nama Pembina'],
            ['pengaturan_key' => 'koperasi_logo',             'pengaturan_value' => '',                          'keterangan' => 'File Logo (Format Gambar)'],
            ['pengaturan_key' => 'kode_lisensi',              'pengaturan_value' => '',                          'keterangan' => 'Kode Lisensi White-Label / Premium'],
            ['pengaturan_key' => 'bunga_pinjaman',            'pengaturan_value' => '1.5',                       'keterangan' => 'Persentase Bunga/Jasa Pinjaman per Bulan (%)'],
            ['pengaturan_key' => 'denda_keterlambatan',       'pengaturan_value' => '10000',                     'keterangan' => 'Nominal Denda Keterlambatan Angsuran (Rp/Bulan)'],
            ['pengaturan_key' => 'opsi_tenor_pinjaman',       'pengaturan_value' => '3,6,12,24',                 'keterangan' => 'Pilihan Lama Tenor Pinjaman dalam Bulan (Pisahkan dengan koma)'],
            ['pengaturan_key' => 'opsi_jenis_pinjaman',       'pengaturan_value' => 'Uang,Barang,Emas',          'keterangan' => 'Pilihan Jenis Pinjaman (Pisahkan dengan koma)'],
            ['pengaturan_key' => 'maks_pinjaman_aktif',       'pengaturan_value' => '1',                         'keterangan' => 'Batas Maksimal Pinjaman Aktif untuk Jenis yang Sama'],
            ['pengaturan_key' => 'pelunasan_min_tenor_persen','pengaturan_value' => '50',                        'keterangan' => 'Batas Minimum Tenor (%) sebelum pelunasan bebas biaya jasa'],
            ['pengaturan_key' => 'kebijakan_pelunasan_aktif', 'pengaturan_value' => '0',                        'keterangan' => 'Aktifkan Kebijakan Min. Tenor Pelunasan? (1=Ya, 0=Tidak)'],
            ['pengaturan_key' => 'pelunasan_jasa_bebas_persen','pengaturan_value' => '100',                      'keterangan' => 'Persentase Jasa yang Dibebankan Saat Pelunasan Setelah Memenuhi Min. Tenor'],
            ['pengaturan_key' => 'telegram_token',            'pengaturan_value' => '',                          'keterangan' => 'Token Bot Telegram dari @BotFather'],
            ['pengaturan_key' => 'shu_metode_modal',          'pengaturan_value' => 'akumulasi_akhir',           'keterangan' => 'Metode Hitung Jasa Modal SHU'],
            // Level 1 SHU Distribution (Total = 100%)
            ['pengaturan_key' => 'shu_jasa_modal',            'pengaturan_value' => '30',                        'keterangan' => 'Persentase Pembagian SHU - Jasa Modal/Simpanan (%)'],
            ['pengaturan_key' => 'shu_jasa_anggota',          'pengaturan_value' => '20',                        'keterangan' => 'Persentase Pembagian SHU - Jasa Anggota/Pinjaman (%)'],
            ['pengaturan_key' => 'shu_cadangan',              'pengaturan_value' => '10',                        'keterangan' => 'Persentase Pembagian SHU untuk Dana Cadangan (%)'],
            ['pengaturan_key' => 'shu_pengurus_anggota',      'pengaturan_value' => '16',                        'keterangan' => 'Persentase Pembagian SHU untuk Pengurus (%)'],
            ['pengaturan_key' => 'shu_pengawas',              'pengaturan_value' => '7.5',                       'keterangan' => 'Persentase Pembagian SHU untuk Pengawas (%)'],
            ['pengaturan_key' => 'shu_pembina',               'pengaturan_value' => '5',                         'keterangan' => 'Persentase Pembagian SHU untuk Pembina (%)'],
            ['pengaturan_key' => 'shu_dana_sosial',           'pengaturan_value' => '6',                         'keterangan' => 'Persentase Pembagian SHU untuk Dana Sosial (%)'],
            ['pengaturan_key' => 'shu_dana_pendidikan',       'pengaturan_value' => '5.5',                       'keterangan' => 'Persentase Pembagian SHU untuk Dana Pendidikan (%)'],
            // Level 2 SHU Pengurus Internal (Total = 100%)
            ['pengaturan_key' => 'shu_pengurus_ketua',        'pengaturan_value' => '34',                        'keterangan' => 'Porsi SHU Pengurus - Ketua (%)'],
            ['pengaturan_key' => 'shu_pengurus_bendahara',    'pengaturan_value' => '33',                        'keterangan' => 'Porsi SHU Pengurus - Bendahara (%)'],
            ['pengaturan_key' => 'shu_pengurus_sekretaris',   'pengaturan_value' => '33',                        'keterangan' => 'Porsi SHU Pengurus - Sekretaris (%)'],
            // WA Gateway
            ['pengaturan_key' => 'wa_token',                  'pengaturan_value' => '',                          'keterangan' => 'Token API WhatsApp Gateway (Fonnte)'],
            ['pengaturan_key' => 'wa_simpanan_aktif',         'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Simpanan (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_simpanan',      'pengaturan_value' => 'Halo {Nama}, Setoran Simpanan {Jenis} Anda sebesar Rp {Nominal} pada tanggal {Tanggal} telah berhasil kami terima. Salam, Koperasi.', 'keterangan' => 'Template Pesan Simpanan'],
            ['pengaturan_key' => 'wa_pinjaman_aktif',         'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Pencairan Pinjaman (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_pinjaman',      'pengaturan_value' => 'Halo {Nama}, Pinjaman Anda sebesar Rp {Nominal} telah dicairkan pada tanggal {Tanggal}.', 'keterangan' => 'Template Pesan Pinjaman'],
            ['pengaturan_key' => 'wa_angsuran_aktif',         'pengaturan_value' => '0',                         'keterangan' => 'Notifikasi Pembayaran Angsuran (1=Aktif, 0=Nonaktif)'],
            ['pengaturan_key' => 'wa_template_angsuran',      'pengaturan_value' => 'Halo {Nama}, Pembayaran Angsuran ke-{CicilanKe} sebesar Rp {Nominal} telah berhasil diterima pada tanggal {Tanggal}. Sisa tagihan Anda: Rp {Sisa}.', 'keterangan' => 'Template Pesan Angsuran'],
            ['pengaturan_key' => 'wa_chatbot_aktif',         'pengaturan_value' => '0',                         'keterangan' => 'Fitur WA Chatbot Inbound (Anggota cek saldo/pinjaman via WA) (1=Aktif, 0=Nonaktif)'],
        ];

        foreach ($defaultPengaturan as $dp) {
            $exists = $this->db->table('pengaturan')->where('pengaturan_key', $dp['pengaturan_key'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('pengaturan')->insert($dp);
            }
        }

        // Hapus duplikat shu_dana_cadangan jika ada
        $this->db->table('pengaturan')->where('pengaturan_key', 'shu_dana_cadangan')->delete();
    }
}
