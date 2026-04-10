<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Edit Jenis Simpanan</h2>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form action="<?= base_url('jenis-simpanan/update/'.$simpanan['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Simpanan</label>
                <input type="text" class="form-control" name="nama_simpanan" value="<?= $simpanan['nama_simpanan'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Minimal Setoran (Rp)</label>
                <input type="number" class="form-control" name="minimal_setoran" value="<?= $simpanan['minimal_setoran'] ?>" required>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= base_url('jenis-simpanan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
