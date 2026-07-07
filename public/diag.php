<?php
$db = new mysqli('localhost', 'root', '', 'koperasi');

// 1. All kas keluar in Semester 1 2026
$kasKeluar = $db->query("SELECT * FROM kas_koperasi WHERE jenis = 'keluar' AND YEAR(tanggal) = 2026 AND MONTH(tanggal) <= 6")->fetch_all(MYSQLI_ASSOC);

// 2. All simpanan tarik
$simpananKeluar = $db->query("SELECT * FROM simpanan WHERE jenis_transaksi = 'tarik' AND YEAR(tanggal_transaksi) = 2026 AND MONTH(tanggal_transaksi) <= 6")->fetch_all(MYSQLI_ASSOC);

// 3. All pinjaman cair
$pinjamanCair = $db->query("SELECT * FROM pinjaman WHERE status IN ('disetujui', 'lunas') AND YEAR(tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH) = 2026 AND MONTH(tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH) <= 6")->fetch_all(MYSQLI_ASSOC);

// 4. All manual keluar
$manualKeluar = $db->query("SELECT * FROM kas_koperasi WHERE jenis = 'keluar' AND kategori NOT IN ('simpanan', 'angsuran', 'pinjaman') AND YEAR(tanggal) = 2026 AND MONTH(tanggal) <= 6")->fetch_all(MYSQLI_ASSOC);

$sumKas = 0;
foreach($kasKeluar as $k) $sumKas += $k['nominal'];

$sumSimp = 0;
foreach($simpananKeluar as $s) $sumSimp += $s['jumlah'];

$sumPinj = 0;
foreach($pinjamanCair as $p) $sumPinj += $p['jumlah_pinjaman'];

$sumMan = 0;
foreach($manualKeluar as $m) $sumMan += $m['nominal'];

echo "Total Kas Keluar: $sumKas\n";
echo "Total Simpanan Keluar: $sumSimp\n";
echo "Total Pinjaman Cair: $sumPinj\n";
echo "Total Manual Keluar: $sumMan\n";
echo "Arus Kas Total: " . ($sumSimp + $sumPinj + $sumMan) . "\n";
echo "Diff: " . ($sumKas - ($sumSimp + $sumPinj + $sumMan)) . "\n";

// Let's find which ones are missing
echo "\nChecking missing in Kas Koperasi vs Arus Kas components:\n";

foreach($kasKeluar as $k) {
    if ($k['kategori'] == 'simpanan') {
        $found = false;
        foreach($simpananKeluar as $s) {
            if ($s['jumlah'] == $k['nominal'] && $s['tanggal_transaksi'] == $k['tanggal']) {
                $found = true; break;
            }
        }
        if (!$found) echo "Missing Simpanan: " . json_encode($k) . "\n";
    } else if ($k['kategori'] == 'pinjaman') {
        $found = false;
        foreach($pinjamanCair as $p) {
            if ($p['jumlah_pinjaman'] == $k['nominal']) {
                $found = true; break;
            }
        }
        if (!$found) echo "Missing Pinjaman: " . json_encode($k) . "\n";
    }
}

