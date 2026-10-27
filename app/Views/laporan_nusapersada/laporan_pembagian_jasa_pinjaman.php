<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>
<style>
    @page { size: A4 landscape; margin: 15mm; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
    th, td { border: 1px solid black !important; padding: 4px; color: #000 !important; }
    th { text-align: center; font-weight: bold; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    h4 { text-align: center; font-weight: bold; margin-bottom: 20px; }
</style>
<h4>
    PEMBAGIAN JASA PINJAMAN ANGGOTA TAHUN <?= $tahun ?? date('Y') ?>
</h4>
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th>
            <th colspan="12">Jasa Pinjaman Per Bulan (Rp)</th>
            <th rowspan="2">Jml Jasa Pinjaman (Rp)</th>
            <th rowspan="2">Jasa Untuk Anggota (Rp)</th>
        </tr>
        <tr>
            <?php 
            $months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agst", "Sept", "Okt", "Nov", "Des"];
            foreach ($months as $m) echo "<th>$m</th>"; 
            ?>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data2 as $row): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= $row[0] ?></td>
            <?php for($i=1; $i<=12; $i++): ?>
            <td class="text-right"><?= $row[$i] ?></td>
            <?php endfor; ?>
            <td class="text-right font-bold"><?= $row[13] ?></td>
            <td class="text-right font-bold"><?= $row[14] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data2)): ?>
        <tr><td colspan="16" class="text-center">Tidak ada data transaksi angsuran di tahun ini.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <?php foreach ($totals as $tot): ?>
            <th class="text-right"><?= $tot ?></th>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>
<?= $this->endSection() ?>
