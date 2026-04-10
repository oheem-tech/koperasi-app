<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-layer-group"></i> Master Data Kelompok</h2>
        <p class="text-muted">Kelola penamaan dan rincian kelompok anggota koperasi untuk keperluan input massal maupun pelaporan.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('kelompok/bulk') ?>" class="btn btn-warning shadow-sm"><i class="fas fa-users-cog"></i> Pindah Massal Anggota</a>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Kelompok
        </button>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0" style="border-radius:12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc; color:#475569;">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th width="30%">Nama Kelompok</th>
                        <th width="30%">Keterangan</th>
                        <th width="15%" class="text-center">Jumlah Anggota</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($kelompok as $k): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="fw-bold" style="color:#1e293b;"><i class="fas fa-tags text-primary me-2"></i><?= esc($k['nama_kelompok']) ?></td>
                        <td class="text-muted" style="font-size:0.85rem;"><?= esc($k['keterangan'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if($k['jumlah_anggota'] > 0): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2"><?= $k['jumlah_anggota'] ?> Orang</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted rounded-pill px-3 border py-2">Kosong</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $k['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                            <a href="<?= base_url('kelompok/delete/'.$k['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Yakin ingin menghapus kelompok ini? Semua anggota di dalamnya harus dikosongkan terlebih dahulu.')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    
                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $k['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content" style="border-radius:12px; border:none;">
                                <form action="<?= base_url('kelompok/update/'.$k['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header border-0 bg-primary bg-opacity-10">
                                        <h5 class="modal-title text-primary fw-bold"><i class="fas fa-edit me-2"></i>Edit Kelompok</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning p-2" style="font-size:0.8rem;">
                                            <i class="fas fa-info-circle"></i> Jika Anda mengubah nama kelompok di sini, sistem akan **otomatis** merubah label kelompok pada seluruh data (<?= $k['jumlah_anggota'] ?>) anggota yang terhubung!
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Kelompok</label>
                                            <input type="text" class="form-control" name="nama_kelompok" value="<?= esc($k['nama_kelompok']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Keterangan Singkat</label>
                                            <textarea name="keterangan" class="form-control" rows="2"><?= esc($k['keterangan']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if(empty($kelompok)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open mb-2" style="font-size:2rem; opacity:.5;"></i>
                            <p class="mb-0">Belum ada data kelompok.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; border:none;">
            <form action="<?= base_url('kelompok/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 bg-primary bg-opacity-10">
                    <h5 class="modal-title text-primary fw-bold"><i class="fas fa-plus me-2"></i>Tambah Kelompok Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelompok <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kelompok" required placeholder="Cth: Dosen, Staf, dsb.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan Singkat</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
