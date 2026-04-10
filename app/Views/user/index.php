<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-users-cog text-dark me-2"></i> Manajemen Staf & User</h2>
    <a href="<?= base_url('user/create') ?>" class="btn btn-primary d-inline-flex gap-2 align-items-center shadow-sm">
        <i class="fas fa-plus"></i> Tambah Staf Baru
    </a>
</div>

<div class="row custom-container">
    <div class="col-12">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="alert alert-info d-flex align-items-start gap-3 border-0 shadow-sm">
            <i class="fas fa-info-circle fa-lg mt-1 text-info"></i>
            <div>
                <strong>Panduan Akun Sistem</strong>
                <p class="mb-0 mt-1" style="font-size: .88rem;">Halaman ini mengelola akun-akun yang murni digunakan staf/pengurus (Non-Anggota biasa). Anda dapat menetapkan Role spesifik (misal: Bendahara) agar akses mereka di aplikasi ini dibatasi. <em>Catatan: Jika user memiliki tanda "Akun Anggota", profil mereka terkunci dengan data keanggotaannya.</em></p>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 custom-table">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="20%">Username / Login</th>
                            <th width="25%">Role (Hak Akses)</th>
                            <th width="35%">Keterangan (Nama Anggota)</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        foreach($users as $u): 
                            // Flag if it's an anggota
                            $isAnggota = ($u['role'] == 'anggota' || !empty($u['nama_lengkap']));
                        ?>
                        <tr>
                            <td class="text-center align-middle"><?= $no++ ?></td>
                            <td class="align-middle">
                                <span class="fw-semibold" style="letter-spacing:-.2px;"><?= esc($u['username']) ?></span>
                            </td>
                            <td class="align-middle">
                                <?php if($u['role'] == 'admin'): ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-2"><i class="fas fa-crown me-1"></i> ADMIN</span>
                                <?php elseif($u['role'] == 'anggota'): ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="fas fa-user me-1"></i> ANGGOTA</span>
                                <?php else: ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-2"><i class="fas fa-user-tag me-1"></i> <?= strtoupper($u['role']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle">
                                <?php if(!empty($u['nama_lengkap'])): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:.7rem;font-weight:700;">
                                            <?= substr(strtoupper($u['nama_lengkap']),0,1) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size:.85rem;"><?= esc($u['nama_lengkap']) ?></div>
                                            <div class="text-muted" style="font-size:.75rem;">ID: <?= esc($u['no_anggota']) ?></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size: .85rem;">Staf Internal (Non-Anggota)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if(!$isAnggota || session()->get('user_id') == $u['id']): ?>
                                    <a href="<?= base_url('user/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline-primary shadow-sm" title="Edit Akun & Role">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php else: ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i> Opsi
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:.85rem;">
                                            <li><a class="dropdown-item" href="<?= base_url('user/edit/'.$u['id']) ?>"><i class="fas fa-key text-warning me-2"></i> Reset Password</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><span class="dropdown-item-text text-muted" style="font-size:.7rem;"><i class="fas fa-lock me-1"></i> Role Anggota Terkunci</span></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if($u['id'] != session()->get('user_id') && !$isAnggota): ?>
                                    <a href="<?= base_url('user/delete/'.$u['id']) ?>" class="btn btn-sm btn-outline-danger shadow-sm ms-1" onclick="return confirm('Hapus staf ini? Aksi ini tidak dapat dibatalkan.')" title="Hapus Staf">
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
</div>

<?= $this->endSection() ?>
