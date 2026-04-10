<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Anggota</h2>
    <a href="<?= base_url('anggota/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Anggota</a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>No. Telp</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($anggota as $row): ?>
                <tr>
                    <td><?= $row['no_anggota'] ?></td>
                    <td><?= $row['nama_lengkap'] ?></td>
                    <td><?= $row['no_telp'] ?></td>
                    <td>
                        <?php if($row['status'] == 'aktif'): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('anggota/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="<?= base_url('anggota/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus anggota ini? Data transaksi simpan pinjam juga akan hilang!')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($anggota)): ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada data anggota.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
