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
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Versi</h6>
                </div>
                <div class="card-body">
                    <p>Versi Aplikasi Saat Ini: <strong>v<?= $currentVersion; ?></strong></p>
                    <hr>
                    
                    <?php if (isset($latestRelease['tag_name'])) : ?>
                        <p>Versi Terbaru di GitHub: <strong class="<?= $hasUpdate ? 'text-success' : 'text-secondary'; ?>"><?= $latestRelease['tag_name']; ?></strong></p>
                        
                        <?php if ($hasUpdate) : ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Terdapat versi pembaruan baru yang siap diunduh!
                            </div>
                            
                            <h6 class="font-weight-bold mt-4">Catatan Rilis (Release Notes):</h6>
                            <div class="bg-light p-3 rounded mb-4" style="max-height: 200px; overflow-y: auto;">
                                <?= nl2br(htmlspecialchars($latestRelease['body'])); ?>
                            </div>

                            <form action="<?= base_url('updatesystem/process'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-primary btn-icon-split" onclick="return confirm('Apakah Anda yakin ingin memulai proses pembaruan? Jangan tutup halaman selama proses berlangsung.');">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    <span class="text">Download & Instal Update Sekarang</span>
                                </button>
                            </form>
                            <small class="d-block mt-2 text-muted">*Proses ini mungkin memakan waktu beberapa menit tergantung koneksi internet server.</small>
                        <?php else : ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Sistem Anda sudah menggunakan versi terbaru!
                            </div>
                        <?php endif; ?>
                        
                    <?php else : ?>
                        <div class="alert alert-danger">
                            Gagal terhubung ke GitHub atau belum ada rilis (Release) yang dibuat di repositori.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
