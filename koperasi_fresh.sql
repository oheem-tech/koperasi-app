-- ============================================================
-- Koperasi App - Fresh Database (Generated from Migrations)
-- Siap diimport ke Hostinger phpMyAdmin
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ============================================================
-- Tabel: migrations (wajib untuk CodeIgniter 4)
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version`   varchar(255) NOT NULL,
  `class`     varchar(255) NOT NULL,
  `group`     varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time`      int(11) NOT NULL,
  `batch`     int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
('2026-03-18-063945', 'App\\Database\\Migrations\\Users',                            'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063946', 'App\\Database\\Migrations\\Anggota',                         'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063946', 'App\\Database\\Migrations\\JenisSimpanan',                   'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063946', 'App\\Database\\Migrations\\Pinjaman',                        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063947', 'App\\Database\\Migrations\\KasKoperasi',                     'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063948', 'App\\Database\\Migrations\\Angsuran',                        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-063949', 'App\\Database\\Migrations\\Simpanan',                        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-071450', 'App\\Database\\Migrations\\AddPokokJasaToAngsuran',          'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-072355', 'App\\Database\\Migrations\\Pengaturan',                      'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-072839', 'App\\Database\\Migrations\\AddTenorOptionToPengaturan',      'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-073744', 'App\\Database\\Migrations\\AddJenisPinjaman',                'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-074621', 'App\\Database\\Migrations\\AddKebijakanPelunasanToPengaturan','default','App', UNIX_TIMESTAMP(), 1),
('2026-03-18-075859', 'App\\Database\\Migrations\\AddJasaPelunasanBebasToPengaturan','default','App', UNIX_TIMESTAMP(), 1),
('2026-03-18-081159', 'App\\Database\\Migrations\\AddDetailSHUToPengaturan',        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-18-081759', 'App\\Database\\Migrations\\AddJabatanToAnggota',             'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-03-31-044200', 'App\\Database\\Migrations\\UpdateUsersRoleToVarchar',        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-04-10-104500', 'App\\Database\\Migrations\\CreateMasterKelompok',            'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-04-11-141923', 'App\\Database\\Migrations\\AddKelompokToAnggota',            'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-04-11-143900', 'App\\Database\\Migrations\\AddKasIdToSimpananAngsuran',      'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-04-12-111000', 'App\\Database\\Migrations\\AddKategoriToKasKoperasi',        'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-04-12-120000', 'App\\Database\\Migrations\\CreateRoles',                       'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-06-28-131400', 'App\\Database\\Migrations\\LogAktivitas',                    'default', 'App', UNIX_TIMESTAMP(), 1);

-- ============================================================
-- Tabel: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   varchar(100) NOT NULL,
  `password`   varchar(255) NOT NULL,
  `role`       varchar(50) NOT NULL DEFAULT 'anggota',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: master_kelompok
-- ============================================================
CREATE TABLE IF NOT EXISTS `master_kelompok` (
  `id`             int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kelompok`  varchar(100) NOT NULL,
  `keterangan`     text DEFAULT NULL,
  `created_at`     datetime DEFAULT NULL,
  `updated_at`     datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: anggota
-- ============================================================
CREATE TABLE IF NOT EXISTS `anggota` (
  `id`                int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           int(11) UNSIGNED NOT NULL,
  `no_anggota`        varchar(50) NOT NULL,
  `nama_lengkap`      varchar(150) NOT NULL,
  `alamat`            text NOT NULL,
  `no_telp`           varchar(20) NOT NULL,
  `tanggal_bergabung` date NOT NULL,
  `status`            enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `jabatan`           enum('anggota','pengurus','pengawas','pembina') NOT NULL DEFAULT 'anggota',
  `kelompok`          varchar(100) NOT NULL DEFAULT 'Umum',
  `created_at`        datetime DEFAULT NULL,
  `updated_at`        datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_anggota` (`no_anggota`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `anggota_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: jenis_simpanan
-- ============================================================
CREATE TABLE IF NOT EXISTS `jenis_simpanan` (
  `id`              int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_simpanan`   varchar(100) NOT NULL,
  `minimal_setoran` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: pinjaman
-- ============================================================
CREATE TABLE IF NOT EXISTS `pinjaman` (
  `id`                  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `anggota_id`          int(11) UNSIGNED NOT NULL,
  `jenis_pinjaman`      varchar(100) NOT NULL DEFAULT 'Uang',
  `tanggal_pengajuan`   date NOT NULL,
  `jumlah_pinjaman`     int(11) NOT NULL,
  `lama_tenor`          int(11) NOT NULL COMMENT 'Bulan',
  `bunga_persen`        float(5,2) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status`              enum('pending','disetujui','ditolak','lunas') NOT NULL DEFAULT 'pending',
  `created_at`          datetime DEFAULT NULL,
  `updated_at`          datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anggota_id` (`anggota_id`),
  CONSTRAINT `pinjaman_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: kas_koperasi
-- ============================================================
CREATE TABLE IF NOT EXISTS `kas_koperasi` (
  `id`          int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tanggal`     date NOT NULL,
  `keterangan`  varchar(255) NOT NULL,
  `jenis`       enum('masuk','keluar') NOT NULL,
  `kategori`    varchar(100) DEFAULT 'operasional',
  `nominal`     int(11) NOT NULL,
  `saldo_akhir` int(11) NOT NULL,
  `created_at`  datetime DEFAULT NULL,
  `updated_at`  datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: angsuran
-- ============================================================
CREATE TABLE IF NOT EXISTS `angsuran` (
  `id`            int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pinjaman_id`   int(11) UNSIGNED NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar`  int(11) NOT NULL,
  `jumlah_pokok`  int(11) NOT NULL DEFAULT 0,
  `jumlah_jasa`   int(11) NOT NULL DEFAULT 0,
  `denda`         int(11) NOT NULL DEFAULT 0,
  `cicilan_ke`    int(11) NOT NULL,
  `kas_id`        int(11) UNSIGNED DEFAULT NULL,
  `created_at`    datetime DEFAULT NULL,
  `updated_at`    datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pinjaman_id` (`pinjaman_id`),
  CONSTRAINT `angsuran_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: simpanan
-- ============================================================
CREATE TABLE IF NOT EXISTS `simpanan` (
  `id`                int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `anggota_id`        int(11) UNSIGNED NOT NULL,
  `jenis_simpanan_id` int(11) UNSIGNED NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `jumlah`            int(11) NOT NULL,
  `jenis_transaksi`   enum('setor','tarik') NOT NULL,
  `keterangan`        varchar(255) DEFAULT NULL,
  `kas_id`            int(11) UNSIGNED DEFAULT NULL,
  `created_at`        datetime DEFAULT NULL,
  `updated_at`        datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anggota_id` (`anggota_id`),
  KEY `jenis_simpanan_id` (`jenis_simpanan_id`),
  CONSTRAINT `simpanan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `simpanan_jenis_simpanan_id_foreign` FOREIGN KEY (`jenis_simpanan_id`) REFERENCES `jenis_simpanan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: pengaturan
-- ============================================================
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id`                int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengaturan_key`    varchar(100) NOT NULL,
  `pengaturan_value`  text DEFAULT NULL,
  `keterangan`        varchar(255) DEFAULT NULL,
  `created_at`        datetime DEFAULT NULL,
  `updated_at`        datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Data Default: pengaturan
-- ============================================================
INSERT INTO `pengaturan` (`pengaturan_key`, `pengaturan_value`, `keterangan`) VALUES
('bunga_pinjaman',             '1.5',         'Persentase Bunga/Jasa Pinjaman per Bulan (%)'),
('denda_keterlambatan',        '10000',        'Nominal Denda Keterlambatan Angsuran (Rp/Bulan)'),
('shu_jasa_modal',             '20',           'Persentase Pembagian SHU - Jasa Modal/Simpanan (%)'),
('shu_jasa_anggota',           '25',           'Persentase Pembagian SHU - Jasa Anggota/Pinjaman (%)'),
('shu_cadangan',               '40',           'Persentase Pembagian SHU - Cadangan Koperasi (%)'),
('opsi_tenor_pinjaman',        '3,6,12,24',    'Pilihan Lama Tenor Pinjaman dalam Bulan (Pisahkan dengan koma)'),
('opsi_jenis_pinjaman',        'Uang,Barang,Emas', 'Pilihan Jenis Pinjaman (Pisahkan dengan koma)'),
('maks_pinjaman_aktif',        '1',            'Batas Maksimal Pinjaman Aktif untuk Jenis yang Sama'),
('pelunasan_min_tenor_persen', '50',           'Batas Minimum Tenor (%) sebelum pelunasan bebas biaya jasa'),
('kebijakan_pelunasan_aktif',  '1',            'Aktifkan Kebijakan Min. Tenor Pelunasan? (1=Ya, 0=Tidak)'),
('pelunasan_jasa_bebas_persen','100',          'Persentase Jasa yang Dibebankan Saat Pelunasan Setelah Memenuhi Min. Tenor'),
('shu_pengurus_anggota',       '10',           'SHU Pengurus Anggota (%)'),
('shu_pengawas',               '5',            'SHU Pengawas (%)'),
('shu_pembina',                '5',            'SHU Pembina (%)'),
('shu_dana_sosial',            '5',            'SHU Dana Sosial (%)'),
('shu_dana_cadangan',          '10',           'SHU Dana Cadangan (%)');

-- ============================================================
-- Data Default: master_kelompok
-- ============================================================
INSERT INTO `master_kelompok` (`nama_kelompok`, `keterangan`, `created_at`) VALUES
('Umum',      'Kelompok standar untuk anggota umum',         NOW()),
('ASN/PNS',   'Pegawai Negeri Sipil & ASN',                  NOW()),
('PPPK',      'Pegawai Pemerintah dengan Perjanjian Kerja',  NOW()),
('Honorer',   'Tenaga Honorer / Kontrak',                    NOW()),
('Pensiunan', 'Anggota yang telah pensiun',                  NOW());

-- ============================================================
-- Data Default: jenis_simpanan
-- ============================================================
INSERT INTO `jenis_simpanan` (`nama_simpanan`, `minimal_setoran`) VALUES
('Simpanan Pokok',   100000),
('Simpanan Wajib',   50000),
('Simpanan Sukarela',10000);

-- ============================================================
-- Data Default: users (Admin)
-- Password: admin123
-- ============================================================
INSERT INTO `users` (`username`, `password`, `role`, `created_at`, `updated_at`) VALUES
('admin', '$2y$10$vrMMPlgbA8TIdqRiE//HCOzvlwN1KdZlLO71lg9rrjQ1BJOR1S8XS', 'admin', NOW(), NOW());

-- ============================================================
-- Tabel: roles (Hak Akses & Permission)
-- ============================================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          int(11) NOT NULL AUTO_INCREMENT,
  `name`        varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  `created_at`  datetime DEFAULT NULL,
  `updated_at`  datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Data Default: roles
-- ============================================================
INSERT INTO `roles` (`name`, `description`, `permissions`, `created_at`, `updated_at`) VALUES
('admin',      'Super Administrator dengan hak akses penuh',            '["manage_anggota","manage_simpanan","manage_pinjaman","manage_angsuran","manage_kas","view_laporan","manage_pengaturan","manage_backup","manage_roles","view_log"]', NOW(), NOW()),
('anggota',    'Anggota Koperasi Biasa (Akses Terbatas)',                '["view_simpanan","view_pinjaman"]',                                                                                                                                   NOW(), NOW()),
('bendahara',  'Bendahara (Penyelaras Kas dan Pembukuan)',               '["manage_anggota","manage_simpanan","manage_pinjaman","manage_angsuran","manage_kas","view_laporan"]',                                                                NOW(), NOW()),
('ketua',      'Ketua Koperasi',                                         '["manage_anggota","manage_simpanan","manage_kas","view_laporan","manage_pengaturan","manage_backup"]',                                                                NOW(), NOW());

-- ============================================================
-- Tabel: log_aktivitas (Riwayat Aktivitas Pengguna)
-- ============================================================
CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id`          int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     int(11) UNSIGNED NOT NULL,
  `aktivitas`   varchar(100) NOT NULL,
  `keterangan`  text DEFAULT NULL,
  `ip_address`  varchar(45) DEFAULT NULL,
  `created_at`  datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

