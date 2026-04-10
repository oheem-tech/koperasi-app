<?= $this->extend('layout/print') ?>
<?= $this->section('content') ?>

<div class="laporan-title">BUKU REKAPITULASI KEUANGAN ANGGOTA<br>Status per Tanggal: <?= date('d F Y') ?></div>

<table class="table">
    <thead>
        <tr>
            <th rowspan="2" style="text-align:center; vertical-align:middle; width:40px;">No</th>
            <th rowspan="2" style="text-align:center; vertical-align:middle;">No. Anggota</th>
            <th rowspan="2" style="vertical-align:middle;">Nama Lengkap</th>
            <th colspan="3" style="text-align:center;">Saldo Simpanan Individual</th>
            <th rowspan="2" style="text-align:center; vertical-align:middle; color:#d00;">Total Sisa<br>Piutang / Hutang</th>
        </tr>
        <tr>
            <th style="text-align:center;">Pokok</th>
            <th style="text-align:center;">Wajib</th>
            <th style="text-align:center;">Sukarela / Lainnya</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rekapAnggota)): ?>
            <tr><td colspan="7" style="text-align:center;">Belum ada data anggota.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach ($rekapAnggota as $r): ?>
                <tr>
                    <td style="text-align:center;"><?= $no++ ?></td>
                    <td style="text-align:center;"><?= esc($r['no_anggota']) ?></td>
                    <td>
                        <?= esc($r['nama_lengkap']) ?>
                    </td>
                    <td style="text-align:right;"><?= number_format($r['pokok'], 0, ',', '.') ?></td>
                    <td style="text-align:right;"><?= number_format($r['wajib'], 0, ',', '.') ?></td>
                    <td style="text-align:right;"><?= number_format($r['sukarela'], 0, ',', '.') ?></td>
                    <td style="text-align:right; color:#d00; font-weight:bold;">
                        <?php if (empty($r['list_pinjaman'])): ?>
                            0
                        <?php else: ?>
                            <?php foreach($r['list_pinjaman'] as $lp): ?>
                                <div style="font-size:10px; margin-bottom:2px;">
                                    PJ-<?= str_pad($lp['pinjaman_id'], 4, '0', STR_PAD_LEFT) ?> : <?= number_format($lp['sisa'], 0, ',', '.') ?>
                                </div>
                            <?php endforeach; ?>
                            <div style="margin-top:3px; border-top:1px dashed #d00; padding-top:2px;">
                                <?= number_format($r['sisa_pinjaman'], 0, ',', '.') ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background:#f1f5f9;">
            <td colspan="3" style="text-align:right;">TOTAL KESELURUHAN DANA :</td>
            <td style="text-align:right;">Rp <?= number_format($total_pokok, 0, ',', '.') ?></td>
            <td style="text-align:right;">Rp <?= number_format($total_wajib, 0, ',', '.') ?></td>
            <td style="text-align:right;">Rp <?= number_format($total_sukarela, 0, ',', '.') ?></td>
            <td style="text-align:right; color:#d00;">Rp <?= number_format($total_sisa_pinjaman, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
