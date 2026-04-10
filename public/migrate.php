<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3307;dbname=koperasi_db', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE TABLE IF NOT EXISTS `master_kelompok` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `nama_kelompok` varchar(100) NOT NULL,
        `keterangan` text,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `nama_kelompok` (`nama_kelompok`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "Table master_kelompok created successfully.\n";

    // Check if empty before seeding
    $stmt = $db->query("SELECT COUNT(*) FROM master_kelompok");
    if ($stmt->fetchColumn() == 0) {
        $now = date('Y-m-d H:i:s');
        $seeds = [
            ['nama_kelompok' => 'Umum', 'keterangan' => 'Kelompok standar untuk anggota umum'],
            ['nama_kelompok' => 'ASN/PNS', 'keterangan' => 'Pegawai Negeri Sipil & ASN'],
            ['nama_kelompok' => 'PPPK / P3K', 'keterangan' => 'Pegawai Pemerintah dengan Perjanjian Kerja'],
            ['nama_kelompok' => 'Honorer', 'keterangan' => 'Tenaga Honorer / Kontrak'],
            ['nama_kelompok' => 'Pensiunan', 'keterangan' => 'Anggota yang telah pensiun'],
        ];
        $insert = $db->prepare("INSERT INTO master_kelompok (nama_kelompok, keterangan, created_at) VALUES (?, ?, ?)");
        foreach($seeds as $s) {
            $insert->execute([$s['nama_kelompok'], $s['keterangan'], $now]);
        }
        echo "Data seeded.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
