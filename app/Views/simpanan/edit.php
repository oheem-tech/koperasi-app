<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Transaksi Simpanan</h2>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('simpanan/update/'.$simpanan['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal_transaksi" value="<?= $simpanan['tanggal_transaksi'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Anggota</label>
                <select class="form-select" name="anggota_id" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php foreach($anggota as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $a['id'] == $simpanan['anggota_id'] ? 'selected' : '' ?>><?= $a['no_anggota'] ?> - <?= $a['nama_lengkap'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Simpanan</label>
                <select class="form-select" name="jenis_simpanan_id" required>
                    <option value="">-- Pilih Jenis Simpanan --</option>
                    <?php foreach($jenis_simpanan as $js): ?>
                        <option value="<?= $js['id'] ?>" <?= $js['id'] == $simpanan['jenis_simpanan_id'] ? 'selected' : '' ?>><?= $js['nama_simpanan'] ?> (Min: Rp <?= number_format($js['minimal_setoran'], 0, ',', '.') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Transaksi</label>
                <div class="d-flex gap-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis_transaksi" value="setor" id="transSetor" <?= $simpanan['jenis_transaksi'] == 'setor' ? 'checked' : '' ?> required>
                        <label class="form-check-label text-success fw-bold" for="transSetor">
                            <i class="fas fa-plus-circle"></i> Setor (Masuk)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="jenis_transaksi" value="tarik" id="transTarik" <?= $simpanan['jenis_transaksi'] == 'tarik' ? 'checked' : '' ?> required>
                        <label class="form-check-label text-danger fw-bold" for="transTarik">
                            <i class="fas fa-minus-circle"></i> Tarik (Keluar)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control" name="jumlah" value="<?= $simpanan['jumlah'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan Tambahan <small class="text-muted">(Opsional)</small></label>
                <input type="text" class="form-control" name="keterangan" value="<?= $simpanan['keterangan'] ?>">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= base_url('simpanan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
