<?= $this->extend('layout/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pembaruan Sistem (Auto-Updater)</h1>
    </div>

    <?php if(session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Status Sistem & Versi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td style="width: 200px;">Versi Aplikasi Saat Ini</td>
                            <td>: <strong class="badge badge-info p-2" style="font-size: 14px;">v<?= esc($currentVersion); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Versi PHP Server Hosting</td>
                            <td>: 
                                <?php if ($isPhpSupported) : ?>
                                    <span class="badge badge-success p-2"><i class="fas fa-check-circle"></i> PHP <?= esc($phpVersion); ?> (Didukung Penuh)</span>
                                <?php else : ?>
                                    <span class="badge badge-warning p-2"><i class="fas fa-exclamation-triangle"></i> PHP <?= esc($phpVersion); ?> (Perlu Diubah di cPanel ke PHP 8.2/8.4)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    
                    <?php if (isset($latestRelease['tag_name'])) : ?>
                        <p class="mb-2">Versi Terbaru di Server Pembaruan: <strong class="<?= $hasUpdate ? 'text-primary font-weight-bold' : 'text-secondary'; ?>"><?= esc($latestRelease['tag_name']); ?></strong></p>
                        
                        <?php if ($hasUpdate) : ?>
                            <?php if (!$isPhpSupported) : ?>
                                <div class="alert alert-warning">
                                    <h6 class="font-weight-bold"><i class="fas fa-exclamation-circle"></i> Perhatian Versi PHP:</h6>
                                    Versi pembaruan baru memerlukan minimal <strong>PHP 8.1 / 8.2 / 8.4</strong>. Saat ini server hosting Anda masih menjalankan PHP <?= esc($phpVersion); ?>. Silakan ubah versi PHP di cPanel hosting Anda terlebih dahulu sebelum menekan tombol pembaruan di bawah ini agar aplikasi tidak mengalami kendala.
                                </div>
                            <?php else : ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-arrow-alt-circle-up"></i> Versi baru siap diunduh! Pembaruan ini mendukung penuh PHP 8.4 dan peningkatan performa sistem.
                                </div>
                            <?php endif; ?>
                            
                            <h6 class="font-weight-bold mt-4">Catatan Rilis (Release Notes):</h6>
                            <div class="bg-light p-3 rounded mb-4" style="max-height: 200px; overflow-y: auto;">
                                <?= nl2br(htmlspecialchars($latestRelease['body'] ?? 'Pembaruan stabilitas dan peningkatan kompatibilitas sistem.')); ?>
                            </div>

                            <form action="<?= base_url('updatesystem/process'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-primary btn-icon-split" onclick="return confirm('Apakah Anda yakin ingin memulai proses pembaruan? File core framework akan diperbarui secara otomatis. Mohon jangan tutup halaman selama proses berlangsung.');">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    <span class="text">Download & Instal Update Sekarang</span>
                                </button>
                            </form>
                            <small class="d-block mt-2 text-muted">*Proses ini membutuhkan waktu beberapa saat tergantung kecepatan unduh server.</small>
                        <?php else : ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Selamat! Sistem Koperasi Anda sudah menggunakan versi paling mutakhir.
                            </div>
                        <?php endif; ?>
                        
                    <?php else : ?>
                        <div class="alert alert-danger">
                            Gagal terhubung ke server pembaruan atau belum ada rilis baru yang dipublikasikan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-shield-alt"></i> Perlindungan Data Klien</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Fitur Pembaruan Sistem dirancang aman dengan perlindungan:
                    </p>
                    <ul class="small text-muted pl-3">
                        <li><strong>Data Database (.env)</strong>: Aman dan tidak akan tertimpa.</li>
                        <li><strong>File Unggahan & Logo</strong>: Folder <code>writable/uploads/</code> diproteksi dan tetap utuh.</li>
                        <li><strong>Migrasi Otomatis</strong>: Jika ada penambahan kolom database, sistem akan menjalankan migrasi secara otomatis.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
