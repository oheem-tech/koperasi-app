<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Tambah Anggota Baru</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('anggota/store') ?>" method="post">
            <?= csrf_field() ?>
            <h5 class="mb-3 text-secondary">Data Profil Anggota</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Anggota</label>
                    <input type="text" class="form-control bg-light" name="no_anggota" value="<?= esc($autoNoAnggota ?? '') ?>" readonly required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" name="no_telp" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" name="tanggal_bergabung" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label d-flex align-items-center gap-2">
                        Kelompok Anggota <small class="text-muted">(untuk input massal)</small>
                        <?php if(!is_premium()): ?>
                        <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px;"><i class="fas fa-crown" style="color:#fbbf24;font-size:0.55rem;"></i> PRO</span>
                        <?php endif; ?>
                    </label>
                    <?php if(is_premium()): ?>
                    <select class="form-select" name="kelompok">
                        <?php foreach($kelompok as $k): ?>
                        <option value="<?= esc($k['nama_kelompok']) ?>" <?= esc($k['nama_kelompok']) == 'Umum' ? 'selected' : '' ?>><?= esc($k['nama_kelompok']) ?></option>
                        <?php endforeach; ?>
                        <?php if(empty($kelompok)): ?>
                        <option value="Umum">Umum</option>
                        <?php endif; ?>
                    </select>
                    <?php else: ?>
                    <select class="form-select bg-light" disabled>
                        <option>Umum</option>
                    </select>
                    <input type="hidden" name="kelompok" value="Umum">
                    <small class="text-muted" style="font-size:0.75rem;"><i class="fas fa-lock me-1"></i>Pengelompokan anggota hanya tersedia di Versi PRO.</small>
                    <?php endif; ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="3" required></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h5 class="text-secondary mb-0">Data Akun (Login)</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAccount()">
                    <i class="fas fa-magic"></i> Generate Otomatis
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="inputUsername" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="inputPassword" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <script>
            function generateAccount() {
                let nama = document.querySelector('input[name="nama_lengkap"]').value;
                let username = "";
                
                if (nama.trim() !== '') {
                    // Ambil nama depan dan tambahkan angka acak
                    let namaDepan = nama.trim().split(' ')[0].toLowerCase().replace(/[^a-z0-9]/g, '');
                    let randNumbers = Math.floor(100 + Math.random() * 900);
                    username = namaDepan + randNumbers;
                } else {
                    username = 'user' + Math.floor(1000 + Math.random() * 9000);
                }
                
                const chars = 'abcdefghijkmnpqrstuvwxyz23456789'; // dihapus l, 1, o, 0 agar tidak pusing
                let password = '';
                for(let i=0; i<6; i++){
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                document.getElementById('inputUsername').value = username;
                let pwdInput = document.getElementById('inputPassword');
                pwdInput.value = password;
                
                // Otomatis tampilkan pasword saat di-generate
                if (pwdInput.type === 'password') {
                    togglePassword();
                }
            }

            function togglePassword() {
                let pwdInput = document.getElementById('inputPassword');
                let eyeIcon = document.getElementById('eyeIcon');
                if (pwdInput.type === 'password') {
                    pwdInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    pwdInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            }
            </script>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                <a href="<?= base_url('anggota') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
