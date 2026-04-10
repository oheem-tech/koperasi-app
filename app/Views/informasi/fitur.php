<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h4 class="mb-0 fw-bold" style="color: var(--primary-dark);"><i class="fas fa-star text-warning me-2"></i> Detail Fitur Koperasi</h4>
                <p class="text-muted mt-2 mb-0">Rangkuman seluruh fungsionalitas dan fitur yang telah diimplementasikan pada aplikasi.</p>
            </div>
            <div class="card-body p-4">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--primary);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-users text-primary me-2"></i> Manajemen Keanggotaan & Kelompok</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li>Pendataan anggota baru dengan informasi lengkap.</li>
                                <li><b>Master Data Kelompok</b>: Sistem manajemen klasifikasi (PNS, Honorer, dll) dengan kemampuan pemindahan posisi massal (Bulk Assign).</li>
                                <li>Tiap anggota otomatis mendapatkan akses login (akun tersendiri).</li>
                                <li>Dashboard khusus anggota untuk melihat saldo dan tagihan.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--success);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-piggy-bank text-success me-2"></i> Sistem Simpanan Terintegrasi</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li><b>Multi-Jenis Simpanan:</b> Mendukung Simpanan Pokok, Wajib, Sukarela, dll yang bisa ditambah dinamis.</li>
                                <li>Fitur Setor dan Tarik yang terintegrasi otomatis dengan <b>Buku Kas Umum</b>.</li>
                                <li>Fitur <b>Input Massal Canggih</b>: Potong gaji massal per kelompok dengan hitungan real-time dan konfirmasi simpanan+angsuran serentak.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--warning);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-hand-holding-usd text-warning me-2"></i> Manajemen Pinjaman & Angsuran</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li>Sistem validasi cerdas (anggota tidak bisa meminjam jenis pinjaman yang sama jika masih aktif).</li>
                                <li>Otomatisasi pencairan <i>(Approval)</i> yang terhubung ke pengurangan buku kas utama.</li>
                                <li>Fitur pelunasan angsuran bulanan, dan <b>Pelunasan Akhir (Early Payoff)</b> menggunakan panel <i>toggle switch</i> instan tanpa denda.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--danger);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-book-open text-danger me-2"></i> Buku Kas Otomatis & Dinamis</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li>Menerapkan sistem <b>Dynamic Running Balance</b> (Saldo Berjalan Dinamis).</li>
                                <li>Transaksi manual (Pemasukan/Pengeluaran kas) bisa dikelola namun tetap terlindungi.</li>
                                <li>Update otomatis saldo akhir tanpa perlu migrasi berulang.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid #8b5cf6;">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-chart-pie me-2" style="color:#8b5cf6;"></i> Laporan Keuangan & Cetak Modern</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li><b>Device-Aware Print:</b> Tampilan laporan/kwitansi (SHU, Neraca, Kas) akan menyesuaikan perangkat. Super lega di PC, bisa <i>Share</i> instan bila dibuka dari HP.</li>
                                <li>Menyajikan pos riil berupa Kas, Piutang Anggota, dan Total Modal Simpanan secara *real-time*.</li>
                                <li>Laporan SHU otomatis terhitung sesuai persentase pengaturan konfigurasi.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--accent);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-cogs text-info me-2"></i> Akses & Keamanan Sistem</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.6;">
                                <li>Dukungan <b>Sistem Freemium (Lisensi Bertingkat)</b> dengan <i>watermark brand</i> elegan untuk versi tidak berbayar.</li>
                                <li>Pembuatan <b>Role yang Dinamis</b> (Admin, Bendahara, dsb) untuk mengontrol izin *Permission*.</li>
                                <li>UI/UX Premium berbasis Web dengan responsivitas tinggi serta penanganan *Dark Mode* di elemen cetak.</li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
