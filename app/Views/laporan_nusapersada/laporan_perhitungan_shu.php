<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>

<style>
    table.table-shu { width: 100%; border-collapse: collapse; margin-bottom: 0; font-size: 11pt; }
    table.table-shu th, table.table-shu td { border: 1px solid black !important; padding: 4px 8px; vertical-align: middle; color:#000 !important; }
    .title-row { text-align: center; font-weight: bold; background-color: #fff; }
    .font-bold { font-weight: bold; }
    .indent-1 { padding-left: 20px !important; }
    .indent-2 { padding-left: 40px !important; }
    .currency { display: flex; justify-content: space-between; }
    .curr-rp { float: left; }
    .curr-val { float: right; }
    .clear { clear: both; }
    .text-center { text-align: center; }
</style>

<table class="table-shu">
    <!-- SECTION 1 -->
    <tr>
        <td colspan="4" class="title-row">
            PERHITUNGAN SISA HASIL USAHA SIMPAN PINJAM<br>
            PER 31 DESEMBER <?= $tahun ?? date('Y') ?>
        </td>
    </tr>
    <tr>
        <td style="width: 55%;" class="font-bold">A. PENDAPATAN</td>
        <td style="width: 20%;"></td>
        <td style="width: 20%;"></td>
        <td style="width: 5%;"></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">1. Unit Usaha Simpan pinjam</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="indent-2">a. Total Pendapatan Tercatat</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($pendapatan, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">Total Pendapatan</td>
        <td></td>
        <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($pendapatan, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
    </tr>
    
    <tr>
        <td class="font-bold">B. BIAYA</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">1. Biaya Operasional & Administrasi</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="indent-2">a. Total Pengeluaran Tercatat</td>
        <td style="border-bottom: 1px solid #000;"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($biaya, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td class="text-center font-bold">+</td>
    </tr>
    <tr>
        <td class="font-bold indent-1">Total Biaya</td>
        <td></td>
        <td style="border-bottom: 1px solid #000;" class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($biaya, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td class="text-center font-bold">-</td>
    </tr>
    <tr>
        <td class="font-bold indent-1">Sisa hasil Usaha (SHU)</td>
        <td></td>
        <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($shu_bersih, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
    </tr>
    
    <!-- SECTION 2 -->
    <tr>
        <td colspan="4" class="title-row" style="border-top: 2px solid #000;">
            PEMBAGIAN HASIL USAHA SIMPAN PINJAM<br>
            PER 31 DESEMBER <?= $tahun ?? date('Y') ?>
        </td>
    </tr>
    <tr>
        <td class="font-bold">A. SISA HASIL USAHA</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold text-center">Jumlah</td>
        <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($shu_bersih, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold">B. PEMBAGIAN SISA HASIL USAHA</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">1. Cadangan <?= $p_cadangan ?>%</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_cadangan, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">2. Anggota <?= $p_total_anggota ?>%</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_anggota, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">3. Pengurus <?= $p_pengurus ?>%</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_pengurus, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <?php 
    $char = 'a';
    foreach ($pengurus_internal as $pi): 
    ?>
    <tr>
        <td class="indent-2"><?= $char++ ?>. <?= esc($pi['label']) ?> <?= $pi['persen'] ?>%</td>
        <td><span class="curr-rp"></span><span class="curr-val"><?= number_format($pi['nominal'], 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <?php endforeach; ?>

    <!-- SECTION 3 -->
    <tr>
        <td colspan="4" class="title-row" style="border-top: 2px solid #000;">
            PEMBAGIAN HASIL USAHA UNTUK ANGGOTA<br>
            PER 31 DESEMBER <?= $tahun ?? date('Y') ?>
        </td>
    </tr>
    <tr>
        <td class="font-bold">A. SISA HASIL USAHA UNTUK ANGGOTA</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold text-center">Jumlah</td>
        <td class="font-bold"><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_anggota, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold">B. PEMBAGIAN SISA HASIL USAHA</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">1. Simpanan (<?= $p_modal ?>% dari Total SHU)</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_simpanan, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="font-bold indent-1">2. Pinjaman (<?= $p_anggota ?>% dari Total SHU)</td>
        <td><span class="curr-rp">Rp</span><span class="curr-val"><?= number_format($val_pinjaman, 2, ',', '.') ?></span><div class="clear"></div></td>
        <td></td>
        <td></td>
    </tr>
</table>

<?= $this->endSection() ?>
