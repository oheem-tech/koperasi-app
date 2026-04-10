<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-shield text-dark me-2"></i> Pengaturan Role & Hak Akses</h2>
    <a href="<?= base_url('role/create') ?>" class="btn btn-primary d-inline-flex gap-2 align-items-center">
        <i class="fas fa-plus"></i> Tambah Role Baru
    </a>
</div>

<div class="row custom-container">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-start gap-3">
            <i class="fas fa-info-circle fa-lg mt-1"></i>
            <div>
                <strong>Panduan Manajemen Akses</strong>
                <p class="mb-0 mt-1" style="font-size: .88rem;">Anda dapat membuat jenis user baru (contoh: <em>Petugas Simpanan</em>, <em>Ketua</em>) dan menentukan menu mana saja yang boleh mereka akses. Klik <span class="badge bg-secondary">Edit</span> untuk mengubah rincian izin masing-masing.</p>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered custom-table">
                <thead class="table-dark">
                    <tr>
                        <th width="3%">No</th>
                        <th width="15%">Nama Role</th>
                        <th width="30%">Deskripsi</th>
                        <th width="40%">Daftar Izin (Permissions)</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    foreach($roles as $r): 
                        $perms = json_decode($r['permissions'], true) ?? [];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="badge rounded-pill bg-<?= $r['name']=='admin' ? 'danger' : ($r['name']=='anggota' ? 'secondary' : 'primary') ?>" style="font-size: 0.85rem">
                                <?= strtoupper($r['name']) ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size: .9rem;"><?= esc($r['description']) ?></td>
                        <td>
                            <?php if($r['name'] == 'admin'): ?>
                                <span class="badge bg-success mb-1 border" style="font-weight: 500;">ALL PERMISSIONS</span>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach($perms as $p): ?>
                                    <span class="badge bg-light text-dark border"><i class="fas fa-check text-success me-1"></i><?= $available_permissions[$p] ?? $p ?></span>
                                    <?php endforeach; ?>
                                    <?php if(empty($perms)): ?>
                                        <span class="text-muted fst-italic" style="font-size:.8rem;">- Tidak ada akses spesifik -</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('role/edit/'.$r['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit Akses">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <?php if(!in_array($r['name'], ['admin', 'anggota'])): ?>
                            <a href="<?= base_url('role/delete/'.$r['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus role ini? Semua anggota dengan role ini mungkin akan kesulitan login jika tidak diubah.')" title="Hapus Role">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
