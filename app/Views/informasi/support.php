<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-3">
    <div class="col-lg-10">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-gradient bg-primary text-white p-4 pb-5 position-relative" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                <div class="text-center position-relative z-1">
                    <div class="mb-3">
                        <span class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 2rem; box-shadow: 0 8px 20px rgba(0,0,0,0.15);">
                            <i class="fas fa-handshake"></i>
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Layanan Kustomisasi & Dukungan Teknis</h2>
                    <p class="text-white-50 mb-0" style="font-size: 1rem;">Perlu fitur tambahan atau modifikasi khusus untuk Koperasi Anda?</p>
                </div>
                <!-- Dekoratif awan/gelombang bawah header -->
                <svg class="position-absolute bottom-0 start-0 w-100" style="height: 40px; color: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                  <path fill="currentColor" fill-opacity="1" d="M0,224L60,213.3C120,203,240,181,360,186.7C480,192,600,224,720,240C840,256,960,256,1080,240C1200,224,1320,192,1380,176L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
                </svg>
            </div>
            
            <div class="card-body p-5 pt-4">
                <div class="text-center mb-5">
                    <p class="lead text-muted" style="max-width: 600px; margin: 0 auto; font-size: 0.95rem; line-height: 1.7;">
                        Terima kasih telah menggunakan <strong>Aplikasi Koperasi CirebonTech</strong> versi Standar secara gratis. Kami menyadari bahwa setiap Koperasi memiliki Standar Operasional (SOP) dan algoritma perhitungan yang unik.
                    </p>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Featured PRO License Banner -->
                    <div class="col-12">
                        <div class="p-4 bg-white rounded-4 border shadow-sm position-relative overflow-hidden" style="border-color: #ffd700 !important;">
                            <div class="position-absolute top-0 end-0 p-3 opacity-25 d-none d-md-block">
                                <i class="fas fa-crown text-warning" style="font-size: 8rem;"></i>
                            </div>
                            <div class="row align-items-center position-relative z-1">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <img src="<?= base_url('assets/images/coffee_pro.png') ?>" alt="Premium Coffee" class="img-fluid rounded-circle" style="max-width: 130px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                </div>
                                <div class="col-md-9">
                                    <span class="badge bg-warning text-dark mb-2 py-1 px-2 fw-bold" style="font-size: 0.8rem;"><i class="fas fa-star text-danger"></i> TERBATAS</span>
                                    <h4 class="fw-bold d-flex align-items-center mb-2">
                                        Lisensi PRO (White-label)
                                    </h4>
                                    <p class="text-muted small mb-2">
                                        Ubah aplikasi menjadi milik Anda sepenuhnya. Hapus watermark sistem di seluruh halaman dan nota print, buka fitur eksklusif <strong>Input Massal</strong>, dan aktifkan fitur <strong>Kebijakan Pelunasan</strong>.
                                    </p>
                                    <div class="fw-bold text-success mb-0" style="font-size: 1.05rem;">
                                        <i class="fas fa-coffee"></i> <em>Upgrade sekarang juga!<br>Nikmati fitur premium dengan harga super hemat—bahkan nggak lebih mahal dari secangkir kopi favoritmu</em>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border border-light-subtle shadow-sm transition-hover">
                            <h6 class="fw-bold mb-3 d-flex align-items-center text-primary">
                                <i class="fas fa-code me-2"></i> Kustomisasi Ekstra
                            </h6>
                            <p class="text-muted small mb-0">
                                Ingin mengubah rumus SHU atau format Nota Print khusus? Kami siap memodifikasi source-code sesuai standar spesifik Anda.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border border-light-subtle shadow-sm transition-hover">
                            <h6 class="fw-bold mb-3 d-flex align-items-center text-success">
                                <i class="fas fa-database me-2"></i> Migrasi Data Excel
                            </h6>
                            <p class="text-muted small mb-0">
                                Punya <em>database</em> anggota/cicilan lama dari mesin kasir atau buku cetak? Kami bantu migrasi semua datanya ke aplikasi baru.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border border-light-subtle shadow-sm transition-hover">
                            <h6 class="fw-bold mb-3 d-flex align-items-center text-danger">
                                <i class="fas fa-server me-2"></i> Setup Hosting
                            </h6>
                            <p class="text-muted small mb-0">
                                Ingin aplikasi online agar bisa diakses anggota via smartphone? Kami siapkan penyewaan server (Hosting) & Domain.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center bg-primary bg-opacity-10 rounded-4 p-4 mb-4 border border-primary border-opacity-25">
                    <h5 class="fw-bold text-dark mb-3">Tertarik Meningkatkan Performa Koperasi Anda?</h5>
                    <p class="text-muted mb-4 small">
                        Konsultasikan segala kebutuhan IT Koperasi Anda bersama kami secara langsung. Kami siap membantu dengan biaya yang sangat fleksibel dan kompetitif.
                    </p>
                    <?php 
                        $waText = urlencode("Halo Tim CirebonTech,\n\nSaya pengguna Aplikasi Koperasi versi Gratis. Saya tertarik untuk menanyakan mengenai Lisensi PRO / Jasa Kustomisasi untuk koperasi saya...\n\nMohon informasi lebih lanjut.");
                    ?>
                    <a href="https://wa.me/6282240629862?text=<?= $waText ?>" target="_blank" class="btn btn-success btn-lg rounded-pill px-4 py-2 fw-bold shadow-sm" style="font-size: 0.95rem;">
                        <i class="fab fa-whatsapp me-2" style="font-size: 1.2rem;"></i> Hubungi via WhatsApp
                    </a>
                    <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                        0822-4062-9862 (CirebonTech)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}
</style>
<?= $this->endSection() ?>
