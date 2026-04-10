<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>

<div class="laporan-title">LAPORAN ARUS KAS<br>Periode: <?= $bulan === 'all' ? 'Semua Waktu' : date('F Y', strtotime($bulan . '-01')) ?></div>

<table class="table">
    <thead>
        <tr>
            <th>URAIAN TRANSAKSI</th>
            <th width="30%">PENERIMAAN (Rp)</th>
            <th width="30%">PENGELUARAN (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <!-- Arus Kas Masuk -->
        <tr><td colspan="3"><strong>A. ARUS KAS MASUK</strong></td></tr>
        <?php foreach($simpananMasuk as $sm): ?>
        <tr><td>Setoran Simpanan (<?= htmlspecialchars($sm['nama_lengkap']) ?> - <?= htmlspecialchars($sm['nama_simpanan']) ?>)</td><td style="text-align:right"><?= number_format($sm['jumlah'], 0, ',', '.') ?></td><td></td></tr>
        <?php endforeach; ?>
        <?php foreach($angsuranMasuk as $am): ?>
        <tr><td>Pelunasan Angsuran (<?= htmlspecialchars($am['nama_lengkap']) ?>)</td><td style="text-align:right"><?= number_format($am['jumlah_bayar'], 0, ',', '.') ?></td><td></td></tr>
        <?php endforeach; ?>
        <?php foreach($manualMasuk as $mm): ?>
        <tr><td><?= htmlspecialchars($mm['keterangan']) ?></td><td style="text-align:right"><?= number_format($mm['nominal'], 0, ',', '.') ?></td><td></td></tr>
        <?php endforeach; ?>
        <tr style="background:#f1f5f9;font-weight:bold;"><td>TOTAL PENERIMAAN KAS</td><td style="text-align:right"><?= number_format($totalMasuk, 0, ',', '.') ?></td><td></td></tr>

        <!-- Arus Kas Keluar -->
        <tr><td colspan="3"><strong>B. ARUS KAS KELUAR</strong></td></tr>
        <?php foreach($simpananKeluar as $sk): ?>
        <tr><td>Penarikan Simpanan (<?= htmlspecialchars($sk['nama_lengkap']) ?>)</td><td></td><td style="text-align:right"><?= number_format($sk['jumlah'], 0, ',', '.') ?></td></tr>
        <?php endforeach; ?>
        <?php foreach($pinjamanCair as $pc): ?>
        <tr><td>Pencairan Pinjaman (<?= htmlspecialchars($pc['nama_lengkap']) ?>)</td><td></td><td style="text-align:right"><?= number_format($pc['jumlah_pinjaman'], 0, ',', '.') ?></td></tr>
        <?php endforeach; ?>
        <?php foreach($manualKeluar as $mk): ?>
        <tr><td><?= htmlspecialchars($mk['keterangan']) ?></td><td></td><td style="text-align:right"><?= number_format($mk['nominal'], 0, ',', '.') ?></td></tr>
        <?php endforeach; ?>
        <tr style="background:#f1f5f9;font-weight:bold;"><td>TOTAL PENGELUARAN KAS</td><td></td><td style="text-align:right"><?= number_format($totalKeluar, 0, ',', '.') ?></td></tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align:right; font-weight:bold;">KENAIKAN / (PENURUNAN) KAS BERSIH</td>
            <td colspan="2" style="text-align:center; font-weight:bold; font-size:14px;">Rp <?= number_format($saldoBersih, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
