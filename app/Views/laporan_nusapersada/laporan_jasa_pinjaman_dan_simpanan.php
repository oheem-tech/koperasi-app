<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>
<style>
    @page { size: A4 landscape; margin: 15mm; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11pt; }
    th, td { border: 1px solid black !important; padding: 5px; color: #000 !important; }
    th { text-align: center; font-weight: bold; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    h4 { text-align: center; font-weight: bold; margin-bottom: 20px; }
</style>
<h4>
    DAFTAR JASA PINJAMAN, SIMPANAN DAN SHU TOKO<br>
    YANG DITERIMAKAN KEPADA ANGGOTA TUTUP BUKU TAHUN <?= $tahun ?? date('Y') ?>
</h4>
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th>
            <th colspan="3">Jasa</th>
            <th rowspan="2">Total Diterimakan (Rp)</th>
        </tr>
        <tr>
            <th>Simpanan (Rp)</th>
            <th>Pinjaman (Rp)</th>
            <th>SHU Toko (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data as $row): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= $row[0] ?></td>
            <td class="text-right"><?= $row[1] ?></td>
            <td class="text-right"><?= $row[2] ?></td>
            <td class="text-right"><?= $row[3] ?></td>
            <td class="text-right font-bold"><?= $row[4] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data)): ?>
        <tr><td colspan="6" class="text-center">Tidak ada data transaksi.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <th class="text-right"><?= $totals[0] ?></th>
            <th class="text-right"><?= $totals[1] ?></th>
            <th class="text-right"><?= $totals[2] ?></th>
            <th class="text-right"><?= $totals[3] ?></th>
        </tr>
    </tfoot>
</table>
<?= $this->endSection() ?>
