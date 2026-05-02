<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h4 class="mb-0 fw-bold" style="color: var(--primary-dark);"><i class="fas fa-star text-warning me-2"></i> Detail Fitur Aplikasi Koperasi</h4>
                <p class="text-muted mt-2 mb-0">Rangkuman seluruh fungsionalitas yang telah diimplementasikan pada versi terkini.</p>
            </div>
            <div class="card-body p-4">
                
                <div class="row g-4">

                    <!-- Instalasi Mandiri -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--primary);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-download text-primary me-2"></i> Web Installer Otomatis</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Instalasi mandiri tanpa baris perintah — cukup buka browser.</li>
                                <li>Form konfigurasi database & Base URL yang intuitif.</li>
                                <li>Pilihan mode: <b>Database Bersih</b> (produksi) atau <b>Data Demo</b> (percobaan).</li>
                                <li>Redirect otomatis ke halaman login setelah instalasi sukses.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Manajemen Anggota -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--primary);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-users text-primary me-2"></i> Manajemen Anggota</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Pendataan anggota lengkap: nama, no. HP, alamat, dan tanggal bergabung.</li>
                                <li>Akun login otomatis dibuatkan tiap anggota baru, dengan generator username+password acak.</li>
                                <li>Filter & pencarian anggota <b>live-search</b> di seluruh form transaksi.</li>
                                <li>Dashboard khusus anggota untuk melihat saldo & tagihan pribadi.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Master Kelompok PRO -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f0fdf4; border-radius:12px; border-left:4px solid #059669;">
                            <h5 class="fw-bold fs-6 mb-3">
                                <i class="fas fa-layer-group me-2" style="color:#059669;"></i> Master Kelompok
                                <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px; vertical-align:middle;"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Buat & kelola klasifikasi kelompok anggota (ASN, Honorer, Umum, dll).</li>
                                <li><b>Cascade Update</b>: Ubah nama kelompok → seluruh anggota terhubung ikut diperbarui otomatis.</li>
                                <li><b>Pemindahan Massal (Bulk Assign)</b>: Pindahkan banyak anggota ke kelompok baru sekaligus.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Simpanan -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--success);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-piggy-bank text-success me-2"></i> Sistem Simpanan Terintegrasi</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li><b>Multi-Jenis Simpanan</b>: Pokok, Wajib, Sukarela — bisa ditambah dinamis dari pengaturan.</li>
                                <li>Transaksi Setor & Tarik otomatis masuk ke <b>Buku Kas Umum</b>.</li>
                                <li>Cetak kwitansi simpanan per transaksi (Device-Aware: Print di PC, Share di HP).</li>
                                <li>Filter transaksi per anggota dengan pencarian instan.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Input Massal PRO -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f0fdf4; border-radius:12px; border-left:4px solid #059669;">
                            <h5 class="fw-bold fs-6 mb-3">
                                <i class="fas fa-bolt me-2" style="color:#059669;"></i> Input Massal (Potong Gaji)
                                <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px; vertical-align:middle;"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Proses simpanan + angsuran seluruh anggota satu kelompok dalam satu sesi.</li>
                                <li>Nilai Pokok & Jasa bisa diedit langsung di tabel sebelum dikonfirmasi.</li>
                                <li>Toggle <b>"Lunas?"</b> per pinjaman untuk pelunasan otomatis total sisa pinjaman.</li>
                                <li>Preview kas real-time di bagian bawah layar sebelum proses dikonfirmasi.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pinjaman & Angsuran -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--warning);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-hand-holding-usd text-warning me-2"></i> Manajemen Pinjaman & Angsuran</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Validasi cerdas: anggota tidak bisa ajukan jenis pinjaman yang sama jika masih aktif.</li>
                                <li>Approval pinjaman oleh admin/bendahara — kas otomatis berkurang saat disetujui.</li>
                                <li><b>Pelunasan Awal (Early Payoff)</b> dengan toggle switch — sisa pinjaman terhitung otomatis.</li>
                                <li>Cetak kwitansi per angsuran.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Buku Kas -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--danger);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-book-open text-danger me-2"></i> Buku Kas Umum Otomatis</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li><b>Dynamic Running Balance</b>: Saldo berjalan dihitung ulang secara real-time.</li>
                                <li>Entri manual untuk pemasukan/pengeluaran di luar transaksi simpanan & pinjaman.</li>
                                <li>Kategorisasi sumber transaksi (simpanan, angsuran, kas manual) secara otomatis.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Laporan -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid #8b5cf6;">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-chart-pie me-2" style="color:#8b5cf6;"></i> Laporan Keuangan Lengkap</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li><b>Arus Kas</b>: Rekap pemasukan & pengeluaran per periode.</li>
                                <li><b>Neraca Saldo</b>: Aset (Kas + Piutang) vs Kewajiban & Ekuitas (Total Simpanan).</li>
                                <li><b>Laporan SHU</b>: Otomatis terhitung berdasarkan konfigurasi persentase pembagian.</li>
                                <li><b>Laporan per Anggota</b>: Riwayat keuangan individual lengkap.</li>
                                <li><b>Device-Aware Print</b>: Auto-print di PC / tombol Share di HP.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Backup & Restore -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--accent);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-database text-info me-2"></i> Backup & Restore</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Backup database langsung dari antarmuka web tanpa perlu akses server.</li>
                                <li>Unduh file SQL backup ke perangkat Anda.</li>
                                <li>Restore database dari file SQL backup yang sudah diunduh sebelumnya.</li>
                                <li>Cocok untuk lingkungan <i>shared hosting</i> tanpa akses terminal.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- WhatsApp Gateway PRO -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f0fdf4; border-radius:12px; border-left:4px solid #059669;">
                            <h5 class="fw-bold fs-6 mb-3">
                                <i class="fab fa-whatsapp me-2" style="color:#25D366;"></i> WhatsApp Gateway
                                <span style="font-size:0.6rem; padding:2px 7px; border-radius:20px; font-weight:700; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; letter-spacing:.3px; vertical-align:middle;"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li>Notifikasi real-time via WhatsApp ke anggota koperasi secara otomatis.</li>
                                <li>Tersedia untuk modul transaksi: <b>Simpanan</b>, <b>Pencairan Pinjaman</b>, dan <b>Angsuran</b>.</li>
                                <li>Pengaturan status ON/OFF per-modul melalui menu Pengaturan.</li>
                                <li>Template isi pesan dinamis yang bisa diubah-ubah menggunakan tag (seperti {Nama}, {Nominal}, dll).</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Akses & Keamanan -->
                    <div class="col-md-6">
                        <div class="p-3 h-100" style="background:#f8fafc; border-radius:12px; border-left:4px solid var(--accent);">
                            <h5 class="fw-bold fs-6 mb-3"><i class="fas fa-cogs text-info me-2"></i> Akses, Role & Sistem Lisensi</h5>
                            <ul class="text-muted small mb-0 ps-3" style="line-height:1.9;">
                                <li><b>Role Dinamis</b>: Buat role khusus (Admin, Bendahara, Kasir) dengan hak akses yang bisa dikonfigurasi.</li>
                                <li><b>Sistem Freemium</b>: Versi gratis penuh fitur inti; Versi PRO membuka Input Massal & Kelompok.</li>
                                <li>Aktivasi lisensi PRO cukup dengan memasukkan kode di halaman Pengaturan.</li>
                                <li>UI/UX Premium berbasis Bootstrap 5 dengan desain responsif untuk semua perangkat.</li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
