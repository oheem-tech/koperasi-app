<?php
$dir = 'd:/xampp8/htdocs/koperasi';
@mkdir("$dir/app/Views/laporan_nusapersada", 0777, true);

$files = glob("$dir/public/custom_nusapersada/*.php");
foreach($files as $file) {
    $c = file_get_contents($file);
    if (strpos($file, 'laporan_jasa_pinjaman_dan_simpanan.php') !== false) {
        $c = preg_replace('/<\?php\s+\$data\s*=\s*\[.*?\];\s*\?>/s', '', $c);
    }
    if (strpos($file, 'laporan_pembagian_jasa_pinjaman.php') !== false) {
        $c = preg_replace('/<\?php\s+\$data2\s*=\s*\[.*?\];\s*\$totals\s*=\s*\[.*?\];\s*\$months\s*=\s*\[.*?\];\s*\?>/s', '<?php $months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agustus", "Sept", "Okt", "Nov", "Des"]; ?>', $c);
    }
    if (strpos($file, 'laporan_perhitungan_jasa_simpanan.php') !== false) {
        $c = preg_replace('/<\?php\s+\$data\s*=\s*\[.*?\];\s*\$totals\s*=\s*\[.*?\];\s*\?>/s', '', $c);
    }
    file_put_contents("$dir/app/Views/laporan_nusapersada/" . basename($file), $c);
}

$r = "$dir/app/Config/Routes.php";
$routes = file_get_contents($r);
$append = "
// Laporan Nusapersada (Dinamis DB)
\$routes->get('laporan-nusapersada/jasa-pinjaman', 'LaporanNusapersada::jasa_pinjaman_dan_simpanan');
\$routes->get('laporan-nusapersada/pembagian-jasa', 'LaporanNusapersada::pembagian_jasa_pinjaman');
\$routes->get('laporan-nusapersada/kas-rat', 'LaporanNusapersada::kas_rat');
\$routes->get('laporan-nusapersada/perhitungan-shu', 'LaporanNusapersada::perhitungan_shu');
\$routes->get('laporan-nusapersada/perhitungan-jasa', 'LaporanNusapersada::perhitungan_jasa_simpanan');
";
file_put_contents($r, $routes . $append);
echo "Berhasil!";
