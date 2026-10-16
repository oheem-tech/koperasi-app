<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'koperasi_db', 3307);
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

$data = [
    ['shu_pengurus_ketua', '35', 'Persentase SHU Pengurus - Ketua (%)'],
    ['shu_pengurus_bendahara', '25', 'Persentase SHU Pengurus - Bendahara (%)'],
    ['shu_pengurus_sekretaris', '15', 'Persentase SHU Pengurus - Sekretaris (%)'],
    ['shu_pengurus_bp1', '12.5', 'Persentase SHU Pengurus - BP 1 (%)'],
    ['shu_pengurus_bp2', '12.5', 'Persentase SHU Pengurus - BP 2 (%)']
];

foreach ($data as $d) {
    // Check if exists
    $result = $mysqli->query("SELECT id FROM pengaturan WHERE pengaturan_key = '{$d[0]}'");
    if ($result->num_rows == 0) {
        $stmt = $mysqli->prepare('INSERT INTO pengaturan (pengaturan_key, pengaturan_value, keterangan) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $d[0], $d[1], $d[2]);
        $stmt->execute();
    }
}
echo 'Berhasil insert data pengaturan master';
