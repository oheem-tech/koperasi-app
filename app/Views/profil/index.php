<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="page-header mb-4">
    <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Info User -->
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center py-5">
                <div class="mx-auto mb-4" style="width:80px;height:80px;background:linear-gradient(135deg,#3b82f6,#06b6d4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.2rem;color:#fff;font-weight:700;box-shadow:0 8px 24px rgba(59,130,246,.4);">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
                <h5 class="fw-700 mb-1"><?= esc($username) ?></h5>
                <span class="badge <?= $role == 'admin' ? 'bg-primary' : 'bg-success' ?> mb-3">
                    <i class="fas fa-circle me-1" style="font-size:6px;"></i>
                    <?= $role == 'admin' ? 'Administrator' : 'Anggota' ?>
                </span>
                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Gunakan form di samping untuk mengubah password Anda.</p>
            </div>
        </div>
    </div>

    <!-- Form Ganti Password -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-600">
                <i class="fas fa-lock text-primary me-2"></i> Ganti Password
            </div>
            <div class="card-body">
                <form action="<?= base_url('profil/update-password') ?>" method="post" id="formGantiPassword">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-500">Password Lama <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" class="form-control" name="password_lama" id="passwordLama" required placeholder="Masukkan password lama Anda">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passwordLama', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label fw-500">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" class="form-control" name="password_baru" id="passwordBaru" required placeholder="Minimal 6 karakter" minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passwordBaru', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password minimal 6 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-double text-muted"></i></span>
                            <input type="password" class="form-control" name="konfirmasi_password" id="konfirmasiPassword" required placeholder="Ulangi password baru">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('konfirmasiPassword', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="matchHint" class="form-text"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4" id="btnSimpan">
                            <i class="fas fa-save me-1"></i> Simpan Password Baru
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Real-time konfirmasi match check
const passwordBaru = document.getElementById('passwordBaru');
const konfirmasiPassword = document.getElementById('konfirmasiPassword');
const matchHint = document.getElementById('matchHint');
const btnSimpan = document.getElementById('btnSimpan');

function checkMatch() {
    if (konfirmasiPassword.value === '') {
        matchHint.textContent = '';
        matchHint.className = 'form-text';
        return;
    }
    if (passwordBaru.value === konfirmasiPassword.value) {
        matchHint.textContent = '✓ Password cocok';
        matchHint.className = 'form-text text-success fw-500';
    } else {
        matchHint.textContent = '✗ Password tidak cocok';
        matchHint.className = 'form-text text-danger fw-500';
    }
}

passwordBaru.addEventListener('input', checkMatch);
konfirmasiPassword.addEventListener('input', checkMatch);

// Prevent submit jika password tidak cocok
document.getElementById('formGantiPassword').addEventListener('submit', function(e) {
    if (passwordBaru.value !== konfirmasiPassword.value) {
        e.preventDefault();
        alert('Password baru dan konfirmasi tidak cocok!');
    }
});
</script>
<?= $this->endSection() ?>
