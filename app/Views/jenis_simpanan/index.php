<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Jenis Simpanan</h2>
    <a href="<?= base_url('jenis-simpanan/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Jenis Simpanan</a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Nama Simpanan</th>
                    <th>Minimal Setoran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($simpanan as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_simpanan'] ?></td>
                    <td>Rp <?= number_format($row['minimal_setoran'], 0, ',', '.') ?></td>
                    <td>
                        <a href="<?= base_url('jenis-simpanan/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="<?= base_url('jenis-simpanan/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus jenis simpanan ini?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($simpanan)): ?>
                <tr>
                    <td colspan="4" class="text-center">Belum ada data jenis simpanan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
