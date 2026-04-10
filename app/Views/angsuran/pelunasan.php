<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Konfirmasi Pelunasan Pinjaman</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white"><i class="fas fa-file-invoice-dollar"></i> Rincian Pinjaman</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Anggota</th><td><?= $anggota['nama_lengkap'] ?> (<?= $anggota['no_anggota'] ?>)</td></tr>
                    <tr><th>Jenis Pinjaman</th><td><?= $pinjaman['jenis_pinjaman'] ?></td></tr>
                    <tr><th>Jumlah Pinjaman</th><td>Rp <?= number_format($pinjaman['jumlah_pinjaman'], 0, ',', '.') ?></td></tr>
                    <tr><th>Tenor</th><td><?= $pinjaman['lama_tenor'] ?> Bulan</td></tr>
                    <tr><th>Bunga</th><td><?= $pinjaman['bunga_persen'] ?>% / Bulan</td></tr>
                    <tr><th>Sudah Diangsur</th><td><?= $sudah_dibayar ?> kali</td></tr>
                </table>
            </div>
        </div>

        <?php if($kebijakan_aktif): ?>
        <div class="alert alert-<?= $kena_jasa_full ? 'warning' : 'success' ?> shadow-sm">
            <?php if($kena_jasa_full): ?>
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Kebijakan Pelunasan Berlaku!</strong><br>
                Anggota baru diangsur <strong><?= $sudah_dibayar ?> kali</strong> dari minimal <strong><?= $batas_cicilan ?> kali</strong> (<?= $min_persen ?>% dari <?= $pinjaman['lama_tenor'] ?> Bulan tenor).
                Oleh karena itu, <strong>sisa jasa/bunga penuh tetap dibebankan</strong>.
            <?php else: ?>
                <i class="fas fa-check-circle"></i>
                Anggota sudah memenuhi syarat minimum cicilan.
                <?php if($jasa_bebas_persen == 0): ?>
                    <strong>Jasa/Bunga Nihil (Gratis)</strong> sesuai kebijakan koperasi.
                <?php elseif($jasa_bebas_persen == 100): ?>
                    <strong>Jasa dihitung 1 bulan berjalan penuh</strong> (100%).
                <?php else: ?>
                    <strong>Jasa dibebankan <?= $jasa_bebas_persen ?>% dari 1 bulan berjalan</strong> sesuai kebijakan koperasi.
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white"><i class="fas fa-money-bill-wave"></i> Kalkulasi Pelunasan</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Sisa Pokok Pinjaman</th>
                        <td class="text-end">Rp <?= number_format($sisa_pokok, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Biaya Jasa / Bunga</th>
                        <td class="text-end">Rp <?= number_format($sisa_jasa, 0, ',', '.') ?></td>
                    </tr>
                    <tr class="table-danger fw-bold">
                        <th>Total Pelunasan</th>
                        <td class="text-end fs-5">Rp <?= number_format($total_pelunasan, 0, ',', '.') ?></td>
                    </tr>
                </table>

                <form action="<?= base_url('angsuran/prosespelunasan/'.$pinjaman['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="total_pelunasan" value="<?= $total_pelunasan ?>">
                    <input type="hidden" name="sisa_pokok" value="<?= $sisa_pokok ?>">
                    <input type="hidden" name="sisa_jasa" value="<?= $sisa_jasa ?>">
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Konfirmasi pelunasan sebesar Rp <?= number_format($total_pelunasan, 0, ',', '.') ?>. Pinjaman akan ditandai LUNAS.')">
                            <i class="fas fa-check-double"></i> Proses Pelunasan
                        </button>
                        <a href="<?= base_url('pinjaman') ?>" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
