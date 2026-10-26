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

        // 2. Master Kelompok
        $kelompokList = [
            ['nama_kelompok' => 'Umum',      'keterangan' => 'Kelompok standar untuk anggota umum',        'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'ASN/PNS',   'keterangan' => 'Pegawai Negeri Sipil & ASN',                 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'PPPK',      'keterangan' => 'Pegawai Pemerintah dengan Perjanjian Kerja', 'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Honorer',   'keterangan' => 'Tenaga Honorer / Kontrak',                   'created_at' => date('Y-m-d H:i:s')],
            ['nama_kelompok' => 'Pensiunan', 'keterangan' => 'Anggota yang telah pensiun',                 'created_at' => date('Y-m-d H:i:s')],
        ];
        foreach ($kelompokList as $k) {
            if ($this->db->table('master_kelompok')->where('nama_kelompok', $k['nama_kelompok'])->countAllResults() == 0) {
                $this->db->table('master_kelompok')->insert($k);
            }
        }

        // 3. User Akun Demo (Admin, Ketua, Bendahara, Pengurus/Pengawas, & Anggota)
        $usersData = [
            ['username' => 'ketua',      'password' => password_hash('ketua123', PASSWORD_DEFAULT),      'role' => 'ketua'],
            ['username' => 'bendahara',  'password' => password_hash('bendahara123', PASSWORD_DEFAULT),  'role' => 'bendahara'],
            ['username' => 'sekretaris',  'password' => password_hash('sekretaris123', PASSWORD_DEFAULT),  'role' => 'anggota'],
            ['username' => 'pengawas',    'password' => password_hash('pengawas123', PASSWORD_DEFAULT),    'role' => 'anggota'],
            ['username' => 'pembina',     'password' => password_hash('pembina123', PASSWORD_DEFAULT),     'role' => 'anggota'],
            ['username' => 'budi123',    'password' => password_hash('budi123', PASSWORD_DEFAULT),     'role' => 'anggota'],
            ['username' => 'citra456',   'password' => password_hash('citra456', PASSWORD_DEFAULT),    'role' => 'anggota'],
            ['username' => 'dewi789',    'password' => password_hash('dewi789', PASSWORD_DEFAULT),     'role' => 'anggota'],
        ];

        $userIdMap = [];
        foreach ($usersData as $u) {
            $existing = $this->db->table('users')->where('username', $u['username'])->get()->getRow();
            if (!$existing) {
                $this->db->table('users')->insert([
                    'username'   => $u['username'],
                    'password'   => $u['password'],
                    'role'       => $u['role'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $userIdMap[$u['username']] = $this->db->insertID();
            } else {
                $userIdMap[$u['username']] = $existing->id;
            }
        }

        // 4. Data Anggota Demo (Lengkap dengan Jabatan Level 2, Telegram Chat ID & Kelompok)
        $anggotaList = [
            [
                'username'          => 'ketua',
                'no_anggota'        => 'A-0001',
                'nama_lengkap'      => 'Ali Sahid Wahyono, S.Pd',
                'alamat'            => 'Jl. Pahlawan No. 10',
                'no_telp'           => '081234567801',
                'telegram_chat_id'  => '548291029',
                'tanggal_bergabung' => date('Y-m-d', strtotime('-12 months')),
                'status'            => 'aktif',
                'jabatan'           => 'pengurus_ketua',
                'kelompok'          => 'ASN/PNS',
            ],
            [
                'username'          => 'bendahara',
                'no_anggota'        => 'A-0002',
                'nama_lengkap'      => 'Iis Sholiha, S.Pd.',
                'alamat'            => 'Jl. Diponegoro No. 25',
                'no_telp'           => '081234567802',
                'telegram_chat_id'  => '619284011',
                'tanggal_bergabung' => date('Y-m-d', strtotime('-10 months')),
                'status'            => 'aktif',
                'jabatan'           => 'pengurus_bendahara',
                'kelompok'          => 'ASN/PNS',
            ],
            [
                'username'          => 'sekretaris',
                'no_anggota'        => 'A-0003',
                'nama_lengkap'      => 'Komala Sari, S.Pd.',
                'alamat'            => 'Jl. Kartini No. 15',
                'no_telp'           => '081234567803',
                'telegram_chat_id'  => null,
                'tanggal_bergabung' => date('Y-m-d', strtotime('-10 months')),
                'status'            => 'aktif',
                'jabatan'           => 'pengurus_sekretaris',
                'kelompok'          => 'Umum',
            ],
            [
                'username'          => 'pengawas',
                'no_anggota'        => 'A-0004',
                'nama_lengkap'      => 'Rusnadi, S.Pd.',
                'alamat'            => 'Jl. Sudirman No. 88',
                'no_telp'           => '081234567804',
                'telegram_chat_id'  => null,
                'tanggal_bergabung' => date('Y-m-d', strtotime('-11 months')),
                'status'            => 'aktif',
                'jabatan'           => 'pengawas',
                'kelompok'          => 'ASN/PNS',
            ],
            [
                'username'          => 'pembina',
                'no_anggota'        => 'A-0005',
                'nama_lengkap'      => 'Drs. H. Asep Saepudin, M.S.I.',
                'alamat'            => 'Jl. Veteran No. 50',
                'no_telp'           => '081234567805',
                'telegram_chat_id'  => null,
                'tanggal_bergabung' => date('Y-m-d', strtotime('-12 months')),
                'status'            => 'aktif',
                'jabatan'           => 'pembina',
                'kelompok'          => 'Pensiunan',
            ],
            [
                'username'          => 'budi123',
                'no_anggota'        => 'A-0006',
                'nama_lengkap'      => 'Budi Santoso',
                'alamat'            => 'Jl. Mawar No. 12',
                'no_telp'           => '081234567890',
                'telegram_chat_id'  => '123456789',
                'tanggal_bergabung' => date('Y-m-d', strtotime('-6 months')),
                'status'            => 'aktif',
                'jabatan'           => 'anggota',
                'kelompok'          => 'Umum',
            ],
            [
                'username'          => 'citra456',
                'no_anggota'        => 'A-0007',
                'nama_lengkap'      => 'Citra Lestari',
                'alamat'            => 'Jl. Melati No. 45',
                'no_telp'           => '089876543210',
                'telegram_chat_id'  => null,
                'tanggal_bergabung' => date('Y-m-d', strtotime('-4 months')),
                'status'            => 'aktif',
                'jabatan'           => 'anggota',
                'kelompok'          => 'PPPK',
            ],
            [
                'username'          => 'dewi789',
                'no_anggota'        => 'A-0008',
                'nama_lengkap'      => 'Dewi Kirana',
                'alamat'            => 'Jl. Anggrek No. 8',
                'no_telp'           => '087766554433',
                'telegram_chat_id'  => null,
                'tanggal_bergabung' => date('Y-m-d', strtotime('-2 months')),
                'status'            => 'aktif',
                'jabatan'           => 'anggota',
                'kelompok'          => 'Honorer',
            ],
        ];

        $anggotaIdMap = [];
        foreach ($anggotaList as $item) {
            $uName = $item['username'];
            unset($item['username']);
            $item['user_id'] = $userIdMap[$uName];

            $existing = $this->db->table('anggota')->where('no_anggota', $item['no_anggota'])->get()->getRow();
            if (!$existing) {
                $item['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('anggota')->insert($item);
                $anggotaIdMap[$item['no_anggota']] = $this->db->insertID();
            } else {
                $this->db->table('anggota')->where('no_anggota', $item['no_anggota'])->update([
                    'nama_lengkap'     => $item['nama_lengkap'],
                    'jabatan'          => $item['jabatan'],
                    'telegram_chat_id' => $item['telegram_chat_id'],
                    'kelompok'         => $item['kelompok'],
                ]);
                $anggotaIdMap[$item['no_anggota']] = $existing->id;
            }
        }

        // 5. Kas Awal (Modal)
        if ($this->db->table('kas_koperasi')->countAllResults() == 0) {
            $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Modal Awal Koperasi', 'masuk', 50000000, 'modal_awal');
        }

        // Ambil ID jenis simpanan
        $spRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Pokok')->get()->getRow();
        $swRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Wajib')->get()->getRow();
        $ssRow = $this->db->table('jenis_simpanan')->where('nama_simpanan', 'Simpanan Sukarela')->get()->getRow();
        $sp_id = $spRow ? $spRow->id : 1;
        $sw_id = $swRow ? $swRow->id : 2;
        $ss_id = $ssRow ? $ssRow->id : 3;

        // 6. Data Simpanan Anggota Demo
        foreach ($anggotaList as $item) {
            $noAnggota = $item['no_anggota'];
            $aId = $anggotaIdMap[$noAnggota] ?? null;
            if (!$aId) continue;

            $hasSimpanan = $this->db->table('simpanan')->where('anggota_id', $aId)->countAllResults() > 0;
            if (!$hasSimpanan) {
                $kasIdSp = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Simpanan Pokok - ' . $item['nama_lengkap'], 'masuk', 100000, 'simpanan');
                $kasIdSw = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-6 months')), 'Simpanan Wajib - ' . $item['nama_lengkap'], 'masuk', 50000, 'simpanan');

                $this->db->table('simpanan')->insertBatch([
                    [
                        'anggota_id'        => $aId,
                        'jenis_simpanan_id' => $sp_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')),
                        'jumlah'            => 100000,
                        'jenis_transaksi'   => 'setor',
                        'keterangan'        => 'Setoran Awal Simpanan Pokok',
                        'kas_id'            => $kasIdSp,
                        'created_at'        => date('Y-m-d H:i:s'),
                    ],
                    [
                        'anggota_id'        => $aId,
                        'jenis_simpanan_id' => $sw_id,
                        'tanggal_transaksi' => date('Y-m-d', strtotime('-6 months')),
                        'jumlah'            => 50000,
                        'jenis_transaksi'   => 'setor',
                        'keterangan'        => 'Setoran Simpanan Wajib Bulan 1',
                        'kas_id'            => $kasIdSw,
                        'created_at'        => date('Y-m-d H:i:s'),
                    ],
                ]);
            }
        }

        // Simpanan Sukarela untuk Budi Santoso
        $budiId = $anggotaIdMap['A-0006'] ?? null;
        if ($budiId && $this->db->table('simpanan')->where(['anggota_id' => $budiId, 'jenis_simpanan_id' => $ss_id])->countAllResults() == 0) {
            $kasIdSs = $kasModel->catatTransaksi(date('Y-m-d', strtotime('-2 months')), 'Simpanan Sukarela - Budi Santoso', 'masuk', 500000, 'simpanan');
            $this->db->table('simpanan')->insert([
                'anggota_id'        => $budiId,
                'jenis_simpanan_id' => $ss_id,
                'tanggal_transaksi' => date('Y-m-d', strtotime('-2 months')),
                'jumlah'            => 500000,
                'jenis_transaksi'   => 'setor',
                'keterangan'        => 'Setoran Simpanan Sukarela',
                'kas_id'            => $kasIdSs,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // 7. Pinjaman Berjalan (Status: Disetujui) - Budi Santoso
        if ($budiId && $this->db->table('pinjaman')->where('anggota_id', $budiId)->countAllResults() == 0) {
            $this->db->table('pinjaman')->insert([
                'anggota_id'          => $budiId,
                'jenis_pinjaman'      => 'Uang',
                'tanggal_pengajuan'   => date('Y-m-d', strtotime('-1 months')),
                'jumlah_pinjaman'     => 5000000,
                'lama_tenor'          => 10,
                'bunga_persen'        => 1.5,
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+9 months')),
                'status'              => 'disetujui',
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
            $pinjamanBudiId = $this->db->insertID();

            // Kas keluar pencairan
            $kasModel->catatTransaksi(date('Y-m-d', strtotime('-1 months')), 'Pencairan Pinjaman Uang - Budi Santoso', 'keluar', 5000000, 'pinjaman');

            // Angsuran ke-1 sudah dibayar
            $kasIdAngsuran = $kasModel->catatTransaksi(date('Y-m-d'), 'Angsuran Pembayaran ke-1 Budi Santoso', 'masuk', 575000, 'angsuran');
            $this->db->table('angsuran')->insert([
                'pinjaman_id'   => $pinjamanBudiId,
                'tanggal_bayar' => date('Y-m-d'),
                'jumlah_bayar'  => 575000,
                'jumlah_pokok'  => 500000,
                'jumlah_jasa'   => 75000,
                'denda'         => 0,
                'cicilan_ke'    => 1,
                'kas_id'        => $kasIdAngsuran,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // 8. Pinjaman Selesai (Status: Lunas) - Citra Lestari
        $citraId = $anggotaIdMap['A-0007'] ?? null;
        if ($citraId && $this->db->table('pinjaman')->where('anggota_id', $citraId)->countAllResults() == 0) {
            $this->db->table('pinjaman')->insert([
                'anggota_id'          => $citraId,
                'jenis_pinjaman'      => 'Barang',
                'tanggal_pengajuan'   => date('Y-m-d', strtotime('-4 months')),
                'jumlah_pinjaman'     => 1500000,
                'lama_tenor'          => 3,
                'bunga_persen'        => 1.5,
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('-1 months')),
                'status'              => 'lunas',
                'created_at'          => date('Y-m-d H:i:s', strtotime('-4 months')),
            ]);
            $pinjamanCitraId = $this->db->insertID();

            // Kas keluar pencairan pinjaman barang
            $kasModel->catatTransaksi(date('Y-m-d', strtotime('-4 months')), 'Pencairan Pinjaman Barang - Citra Lestari', 'keluar', 1500000, 'pinjaman');

            // 3 Kali angsuran lunas
            for ($c = 1; $c <= 3; $c++) {
                $tglBayar = date('Y-m-d', strtotime("-" . (4 - $c) . " months"));
                $kasIdC = $kasModel->catatTransaksi($tglBayar, "Angsuran ke-$c (Lunas) - Citra Lestari", 'masuk', 522500, 'angsuran');
                $this->db->table('angsuran')->insert([
                    'pinjaman_id'   => $pinjamanCitraId,
                    'tanggal_bayar' => $tglBayar,
                    'jumlah_bayar'  => 522500,
                    'jumlah_pokok'  => 500000,
                    'jumlah_jasa'   => 22500,
                    'denda'         => 0,
                    'cicilan_ke'    => $c,
                    'kas_id'        => $kasIdC,
                    'created_at'    => date('Y-m-d H:i:s', strtotime("-" . (4 - $c) . " months")),
                ]);
            }
        }

        // 9. Pinjaman Baru Menunggu Persetujuan (Status: Pending) - Dewi Kirana
        $dewiId = $anggotaIdMap['A-0008'] ?? null;
        if ($dewiId && $this->db->table('pinjaman')->where('anggota_id', $dewiId)->countAllResults() == 0) {
            $this->db->table('pinjaman')->insert([
                'anggota_id'          => $dewiId,
                'jenis_pinjaman'      => 'Uang',
                'tanggal_pengajuan'   => date('Y-m-d'),
                'jumlah_pinjaman'     => 3000000,
                'lama_tenor'          => 6,
                'bunga_persen'        => 1.5,
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+6 months')),
                'status'              => 'pending',
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
        }

        // 10. Pengaturan Default Sistem Terbaru (Porsi Jabatan Pengurus & Telegram)
        $defaultSettings = [
            'kebijakan_pelunasan_aktif'  => ['value' => '0',  'ket' => 'Aktifkan Kebijakan Min. Tenor Pelunasan? (1=Ya, 0=Tidak)'],
            'shu_pengurus_ketua'         => ['value' => '34', 'ket' => 'Porsi SHU Pengurus - Ketua (%)'],
            'shu_pengurus_bendahara'     => ['value' => '33', 'ket' => 'Porsi SHU Pengurus - Bendahara (%)'],
            'shu_pengurus_sekretaris'    => ['value' => '33', 'ket' => 'Porsi SHU Pengurus - Sekretaris (%)'],
            'telegram_token'             => ['value' => '',   'ket' => 'Token Bot Telegram dari @BotFather'],
        ];

        foreach ($defaultSettings as $key => $conf) {
            $exists = $this->db->table('pengaturan')->where('pengaturan_key', $key)->countAllResults();
            if ($exists == 0) {
                $this->db->table('pengaturan')->insert([
                    'pengaturan_key'   => $key,
                    'pengaturan_value' => $conf['value'],
                    'keterangan'       => $conf['ket'],
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "✅ Data Demo berhasil di-seed sesuai arsitektur sistem terbaru (Jabatan Level 2, Telegram, & Status Pinjaman)\n";
    }
}
