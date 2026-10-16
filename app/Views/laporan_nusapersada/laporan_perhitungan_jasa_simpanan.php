<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>
<style>
    @page { size: A4 portrait; margin: 15mm; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11pt; }
    th, td { border: 1px solid black !important; padding: 5px; color: #000 !important; }
    th { text-align: center; font-weight: bold; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    h4 { text-align: center; font-weight: bold; margin-bottom: 20px; }
</style>
<h4>
    PERHITUNGAN JASA SIMPANAN TAHUN <?= date('Y') ?>
</h4>
<table>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama</th>
            <th colspan="3">Jenis Simpanan (Rp)</th>
            <th rowspan="2">Jumlah (Rp)</th>
            <th rowspan="2">Jasa Diterima (Rp)</th>
        </tr>
        <tr>
            <th>Pokok</th>
            <th>Wajib</th>
            <th>Sukarela</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <td class="text-center"><?= $row[0] ?></td>
            <td><?= $row[1] ?></td>
            <td class="text-right"><?= $row[2] ?></td>
            <td class="text-right"><?= $row[3] ?></td>
            <td class="text-right"><?= $row[4] ?></td>
            <td class="text-right font-bold"><?= $row[5] ?></td>
            <td class="text-right font-bold text-success"><?= $row[6] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($data)): ?>
        <tr><td colspan="7" class="text-center">Belum ada data simpanan.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th class="text-right"><?= $totals[0] ?></th>
            <th class="text-right"><?= $totals[1] ?></th>
            <th class="text-right"><?= $totals[2] ?></th>
            <th class="text-right font-bold"><?= $totals[3] ?></th>
            <th class="text-right font-bold"><?= $totals[4] ?></th>
        </tr>
    </tfoot>
</table>
<?= $this->endSection() ?>
