<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-edit text-primary me-2"></i> Edit Akun Sistem / Staf</h2>
    <a href="<?= base_url('user') ?>" class="btn btn-secondary d-inline-flex gap-2 align-items-center shadow-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row custom-container">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i> Data Akses Sistem & Role</h6>
                <?php if($user['role'] == 'anggota'): ?>
                    <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i> AKUN ANGGOTA TERKUNCI</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/update/'.$user['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" required <?php echo ($user['role'] == 'anggota' ? 'readonly' : ''); ?> style="border-radius:8px;">
                        <?php if($user['role'] == 'anggota'): ?>
                        <small class="text-danger" style="font-size:.75rem;">Username terkunci oleh sistem keanggotaan.</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Hak Akses / Role Aplikasi</label>
                        <select name="role" class="form-select" required style="border-radius:8px;" <?php echo ($user['role'] == 'anggota' || ($user['role'] == 'admin' && session()->get('user_id') == $user['id']) ? 'style="pointer-events:none;background:#e9ecef"' : ''); ?>>
                            <?php foreach($roles as $r): ?>
                                <option value="<?= $r['name'] ?>" <?= $r['name'] == $user['role'] ? 'selected' : '' ?>><?= strtoupper($r['name']) ?> - <?= esc($r['description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($user['role'] == 'anggota'): ?>
                            <input type="hidden" name="role" value="anggota">
                            <small class="text-muted" style="font-size:.75rem;"><i class="fas fa-info-circle text-info"></i> Hanya pengguna Non-Anggota yang dapat diubah jabatannya secara bebas di sini.</small>
                        <?php elseif($user['role'] == 'admin' && session()->get('user_id') == $user['id']): ?>
                            <input type="hidden" name="role" value="admin">
                            <small class="text-danger" style="font-size:.70rem;"><i class="fas fa-exclamation-triangle"></i> Anda tidak bisa mendowngrade akun Admin Anda sendiri.</small>
                        <?php endif; ?>
                    </div>

                    <hr class="text-secondary opacity-25">

                    <h6 class="fw-bold mb-3 mt-4 text-dark"><i class="fas fa-key text-warning me-2"></i> Keamanan Sandi</h6>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary" style="font-size:.85rem;">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Isi hanya jika reset sandi" style="border-radius:8px;">
                        <small class="text-muted mt-1 d-block" style="font-size:.70rem;">Berguna jika staf atau anggota Anda lupa kata sandi.</small>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-3 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius:8px;">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
