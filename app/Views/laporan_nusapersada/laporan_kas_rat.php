<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>
<?php
function renderCell($val) {
    if (!$val || $val == 0) return '<span style="float:right;">-</span>';
    return '<span style="float:left;">Rp</span><span style="float:right;">' . number_format($val, 0, ',', '.') . '</span><div style="clear:both;"></div>';
}

$months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agst", "Sept", "Okt", "Nov", "Des"];

// Variables from controller: $tahun, $p_pokok, $p_wajib, $p_sukarela, $p_angsuran, $p_jasa, $p_pelunasan, $k_pinjaman, $k_keluar

$saldo_awal = array_fill(1, 12, 0);
$pemasukan_jumlah = array_fill(1, 12, 0);
$pengeluaran_jumlah = array_fill(1, 12, 0);
$saldo_akhir = array_fill(1, 12, 0);

// Basic loop to calculate rolling balance
$current_balance = 0; // Seharusnya query saldo kas tahun lalu, untuk sekarang kita mulai dari 0 atau dummy agar tidak error logikanya
for ($i = 1; $i <= 12; $i++) {
    $saldo_awal[$i] = $current_balance;
    $pemasukan_jumlah[$i] = $saldo_awal[$i] + $p_pokok[$i] + $p_wajib[$i] + $p_sukarela[$i] + $p_angsuran[$i] + $p_jasa[$i] + $p_pelunasan[$i];
    $pengeluaran_jumlah[$i] = $k_pinjaman[$i] + $k_keluar[$i];
    
    // Ending balance is total in minus out
    $saldo_akhir[$i] = $pemasukan_jumlah[$i] - $pengeluaran_jumlah[$i];
    $current_balance = $saldo_akhir[$i];
}
?>

<style>
    @page { size: A4 landscape; margin: 10mm; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 8pt; }
    th, td { border: 1px solid black !important; padding: 3px; color: #000 !important; }
    th { text-align: center; font-weight: bold; background-color: #f8f9fa !important; }
    .col-no { text-align: center; width: 25px; font-weight: bold; }
    .col-rincian { width: 150px; }
    .font-bold { font-weight: bold; }
    h4 { text-align: center; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
</style>

<h4>KAS SIMPAN PINJAM PER 31 DESEMBER <?= $tahun ?></h4>

<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>RINCIAN</th>
            <?php foreach ($months as $m): ?>
            <th><?= $m ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <!-- PEMASUKAN -->
        <tr>
            <td class="font-bold">A</td>
            <td colspan="13" class="font-bold">PEMASUKAN</td>
        </tr>
        <tr>
            <td class="col-no">1</td><td class="col-rincian font-bold">Saldo awal bulan</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($saldo_awal[$i])."</td>"; ?>
        </tr>
        <tr>
            <td class="col-no">2</td><td class="col-rincian font-bold">Simpanan</td><td colspan="12"></td>
        </tr>
        <tr>
            <td></td><td class="col-rincian">a. Simpanan Pokok</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_pokok[$i])."</td>"; ?>
        </tr>
        <tr>
            <td></td><td class="col-rincian">b. Simpanan Wajib</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_wajib[$i])."</td>"; ?>
        </tr>
        <tr>
            <td></td><td class="col-rincian">c. Simpanan Sukarela</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_sukarela[$i])."</td>"; ?>
        </tr>
        <tr>
            <td class="col-no">3</td><td class="col-rincian font-bold">Angsuran</td><td colspan="12"></td>
        </tr>
        <tr>
            <td></td><td class="col-rincian">a. Angsuran Pokok Pinjaman</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_angsuran[$i])."</td>"; ?>
        </tr>
        <tr>
            <td class="col-no">4</td><td class="col-rincian font-bold">Jasa</td><td colspan="12"></td>
        </tr>
        <tr>
            <td></td><td class="col-rincian">a. Jasa Pinjaman</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_jasa[$i])."</td>"; ?>
        </tr>
        <tr>
            <td class="col-no">5</td><td class="col-rincian font-bold">Pelunasan</td><td colspan="12"></td>
        </tr>
        <tr>
            <td></td><td class="col-rincian">a. Pelunasan Pinjaman</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($p_pelunasan[$i])."</td>"; ?>
        </tr>
        <tr>
            <th colspan="2">JUMLAH PEMASUKAN</th>
            <?php for($i=1; $i<=12; $i++) echo "<th>".renderCell($pemasukan_jumlah[$i])."</th>"; ?>
        </tr>

        <!-- PENGELUARAN -->
        <tr>
            <td class="font-bold">B</td>
            <td colspan="13" class="font-bold">PENGELUARAN</td>
        </tr>
        <tr>
            <td class="col-no">1</td><td class="col-rincian font-bold">Pinjaman</td><td colspan="12"></td>
        </tr>
        <tr>
            <td></td><td class="col-rincian">a. Pinjaman Dicairkan</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($k_pinjaman[$i])."</td>"; ?>
        </tr>
        <tr>
            <td class="col-no">2</td><td class="col-rincian font-bold">Anggota Keluar / Tarik Tunai</td>
            <?php for($i=1; $i<=12; $i++) echo "<td>".renderCell($k_keluar[$i])."</td>"; ?>
        </tr>
        <tr>
            <th colspan="2">JUMLAH PENGELUARAN</th>
            <?php for($i=1; $i<=12; $i++) echo "<th>".renderCell($pengeluaran_jumlah[$i])."</th>"; ?>
        </tr>
        <tr>
            <td class="col-no">3</td><td class="col-rincian font-bold" style="background:#e9ecef;">SALDO TUNAI AKHIR</td>
            <?php for($i=1; $i<=12; $i++) echo "<th style='background:#e9ecef;'>".renderCell($saldo_akhir[$i])."</th>"; ?>
        </tr>
    </tbody>
</table>
<?= $this->endSection() ?>
