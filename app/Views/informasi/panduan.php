<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<style>
.guide-step {
    display: flex;
    margin-bottom: 24px;
    align-items: flex-start;
}
.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
    margin-right: 16px;
    margin-top: 4px;
}
.step-content h6 {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
    font-size: 1rem;
}
.step-content p {
    color: #475569;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0;
}
.badge-pro {
    font-size: 0.6rem;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 700;
    background: linear-gradient(135deg, #059669, #0d9488);
    color: #fff;
    letter-spacing: .3px;
    vertical-align: middle;
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h4 class="mb-0 fw-bold" style="color: var(--primary-dark);"><i class="fas fa-book-open text-primary me-2"></i> Panduan Operasional Sistem</h4>
                <p class="text-muted mt-2 mb-0">Petunjuk langkah demi langkah mengoperasikan aplikasi Koperasi Simpan Pinjam.</p>
            </div>
            
            <div class="card-body p-4">
                <div class="accordion" id="accordionPanduan">

                    <!-- Panduan 0: Instalasi -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanInstall">
                                <i class="fas fa-download me-2 text-primary"></i> 1. Instalasi via Web Installer
                            </button>
                        </h2>
                        <div id="panduanInstall" class="accordion-collapse collapse show" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Ekstrak & Letakkan di Web Server</h6>
                                        <p>Unduh paket ZIP aplikasi dari developer, lalu ekstrak ke folder web server Anda (contoh: <code>htdocs/nama-folder/</code> di XAMPP, atau <code>public_html/</code> di hosting).</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Buat Database Kosong</h6>
                                        <p>Masuk ke phpMyAdmin atau panel hosting Anda, buat sebuah database baru yang kosong (misalnya: <code>koperasi_db</code>). Catat nama database, username, dan password MySQL.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Akses URL Aplikasi di Browser</h6>
                                        <p>Buka browser dan arahkan ke URL folder Anda (misal: <code>http://localhost/nama-folder/</code>). Jika file konfigurasi <code>.env</code> belum ada, sistem otomatis mengalihkan ke <b>halaman Web Installer</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h6>Isi Form Konfigurasi Installer</h6>
                                        <p>Masukkan <b>Base App URL</b> (URL lengkap aplikasi Anda, <u>tanpa</u> <code>/public</code>), lalu isi data koneksi database. Pilih mode: <b>"Database Bersih"</b> untuk produksi, atau <b>"Isi Data Demo"</b> untuk percobaan.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">5</div>
                                    <div class="step-content">
                                        <h6>Simpan & Instalasi Otomatis</h6>
                                        <p>Klik tombol <b>"Simpan & Install Sekarang"</b>. Sistem akan otomatis membuat konfigurasi, menjalankan migrasi database, dan mengisi data awal. Setelah selesai, Anda langsung diarahkan ke halaman login. Gunakan akun <code>admin</code> / <code>admin123</code>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Anggota -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan1">
                                <i class="fas fa-user-plus me-2 text-primary"></i> 2. Menambahkan Anggota Baru
                            </button>
                        </h2>
                        <div id="panduan1" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Buka Menu Data Anggota</h6>
                                        <p>Arahkan kursor ke menu sidebar kiri di bagian <b>"Master Data"</b> lalu klik <b>"Data Anggota"</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Klik Tombol "Tambah Anggota" & Isi Form</h6>
                                        <p>Isi Nama, No. HP, Alamat, dan Tanggal Bergabung. Kelompok anggota baru akan otomatis masuk ke <b>"Umum"</b>. Klik <b>"Generate Otomatis"</b> untuk membuat username + password secara acak, atau isi manual.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Akun Login Tersedia Langsung</h6>
                                        <p>Setelah disimpan, anggota bisa langsung login ke aplikasi menggunakan username & password yang dibuatkan, dan melihat saldo serta tagihan pribadinya di dashboard khusus anggota.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Master Kelompok (PRO) -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanKelompok">
                                <i class="fas fa-layer-group me-2 text-primary"></i> 3. Master Kelompok & Pemindahan Anggota
                                <span class="badge-pro ms-2"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </button>
                        </h2>
                        <div id="panduanKelompok" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="alert alert-warning py-2 px-3" style="font-size:0.82rem;"><i class="fas fa-crown me-1 text-warning"></i> Fitur ini memerlukan <b>Lisensi PRO</b>. Pengguna gratis bisa melihat tampilan, namun tombol proses hanya bisa digunakan setelah aktivasi.</div>
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Kelola Master Kelompok</h6>
                                        <p>Buka menu sidebar <b>"Master Kelompok"</b>. Tambah, edit, atau hapus kelompok. Jika nama kelompok diubah, sistem otomatis <b>memperbarui label</b> pada seluruh anggota yang terhubung (cascade update).</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Pemindahan Anggota Massal (Bulk Assign)</h6>
                                        <p>Klik tombol kuning <b>"Pindah Massal Anggota"</b>. Saring berdasarkan kelompok asal, centang nama anggota yang ingin dipindahkan, pilih kelompok tujuan, lalu klik <b>"Proses Pemindahan"</b>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Simpanan -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan2">
                                <i class="fas fa-piggy-bank me-2 text-success"></i> 4. Transaksi Simpanan (Setor dan Tarik)
                            </button>
                        </h2>
                        <div id="panduan2" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Buka Menu Simpanan → Input Transaksi</h6>
                                        <p>Pilih anggota menggunakan kolom <b>pencarian dinamis (live-search)</b>. Pilih Jenis Transaksi (<b>Setor</b> atau <b>Tarik</b>), pilih jenis simpanan, lalu masukkan nominal.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Otomatis Masuk Buku Kas & Laporan</h6>
                                        <p>Setelah disimpan, transaksi simpanan otomatis tercatat di <b>Buku Kas Umum</b> dan langsung memengaruhi semua laporan keuangan secara <i>real-time</i>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Cetak Kwitansi</h6>
                                        <p>Setiap transaksi simpanan memiliki tombol cetak kwitansi. Di PC terbuka otomatis untuk dicetak; di HP tersedia tombol Share untuk kirim via WhatsApp.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Input Massal (PRO) -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanMassal">
                                <i class="fas fa-layer-group me-2 text-success"></i> 5. Input Massal (Potong Gaji)
                                <span class="badge-pro ms-2"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </button>
                        </h2>
                        <div id="panduanMassal" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="alert alert-warning py-2 px-3" style="font-size:0.82rem;"><i class="fas fa-crown me-1 text-warning"></i> Fitur ini memerlukan <b>Lisensi PRO</b>. Pengguna gratis bisa melihat tampilan, namun tombol proses hanya bisa digunakan setelah aktivasi.</div>
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Setup Kelompok & Tanggal</h6>
                                        <p>Klik menu <b>"Input Massal"</b>. Pilih Kelompok anggota yang ingin diproses dan tentukan Tanggal Transaksi, lalu klik <b>"Buka Form Input"</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Centang Baris yang Diproses</h6>
                                        <p>Tabel menampilkan semua anggota beserta baris simpanan dan angsuran aktifnya. Gunakan <b>Centang Semua</b> atau centang satu per satu. Nilai pokok dan jasa bisa diedit langsung di tabel.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Panel Pelunasan & Konfirmasi</h6>
                                        <p>Aktifkan <b>toggle "Lunas?"</b> untuk anggota yang ingin melunasi pinjaman — nilai akan otomatis berubah ke total sisa pinjaman. Cek Preview Kas di bagian bawah, lalu klik <b>"Proses Pembayaran Massal"</b>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Pinjaman -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan3">
                                <i class="fas fa-hand-holding-usd me-2 text-warning"></i> 6. Pengajuan & Persetujuan Pinjaman
                            </button>
                        </h2>
                        <div id="panduan3" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Input Pengajuan Pinjaman</h6>
                                        <p>Buka menu <b>"Pinjaman"</b> → klik <b>"Ajukan Pinjaman"</b>. Pilih anggota, jenis pinjaman, lama tenor (bulan), dan nominal. Sistem otomatis menghitung angsuran per bulan.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Proses Approval</h6>
                                        <p>Pinjaman awalnya berstatus <b>"Pending"</b>. Admin/Bendahara klik tombol approval (✔ hijau) untuk mencairkan dana. Saat disetujui, kas koperasi otomatis berkurang sejumlah dana yang dicairkan.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Bayar Angsuran / Pelunasan Awal</h6>
                                        <p>Buka menu <b>"Angsuran"</b>. Klik "Bayar Angsuran" untuk cicilan bulanan biasa. Aktifkan <b>toggle "Lunas?"</b> untuk pelunasan seluruh sisa pinjaman sekaligus — nominal menyesuaikan otomatis.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Kas & Laporan -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduan4">
                                <i class="fas fa-chart-line me-2" style="color:#8b5cf6;"></i> 7. Buku Kas Umum & Laporan Keuangan
                            </button>
                        </h2>
                        <div id="panduan4" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Entri Manual Kas</h6>
                                        <p>Pergi ke <b>"Buku Kas Umum"</b> untuk menginput transaksi kas luar (sumbangan, pengeluaran ATK, dll). Transaksi dari simpanan dan pinjaman otomatis masuk dan hanya bisa diedit dari menu masing-masing.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Laporan Arus Kas, Neraca & SHU</h6>
                                        <p>Buka menu <b>Laporan</b> untuk melihat Arus Kas bulanan, Neraca Saldo (Aset vs Kewajiban), dan Laporan SHU yang terhitung otomatis sesuai pengaturan pembagian.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Backup & Restore Database</h6>
                                        <p>Buka menu <b>"Backup & Restore"</b> (visible untuk admin). Klik <b>"Buat Backup Sekarang"</b> untuk mengunduh file SQL. Untuk pemulihan, upload file SQL backup melalui tombol Restore.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan WhatsApp Gateway (PRO) -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanWaGateway">
                                <i class="fab fa-whatsapp me-2" style="color:#25D366;"></i> 8. Konfigurasi WhatsApp Gateway
                                <span class="badge-pro ms-2"><i class="fas fa-crown" style="color:#fbbf24; font-size:0.5rem;"></i> PRO</span>
                            </button>
                        </h2>
                        <div id="panduanWaGateway" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="alert alert-warning py-2 px-3" style="font-size:0.82rem;"><i class="fas fa-crown me-1 text-warning"></i> Fitur ini memerlukan <b>Lisensi PRO</b>.</div>
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Dapatkan Token API Fonnte</h6>
                                        <p>Daftar atau login ke <a href="https://fonnte.com" target="_blank" class="text-decoration-none">fonnte.com</a>. Hubungkan nomor WhatsApp koperasi (scan QR code), lalu salin <b>Token API</b> dari menu Device.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Masukkan Token di Pengaturan Aplikasi</h6>
                                        <p>Buka menu <b>"Pengaturan Master"</b> di aplikasi. Pada blok <b>WhatsApp Gateway</b>, tempelkan token yang disalin ke kolom <code>wa_token</code>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Aktifkan & Sesuaikan Template</h6>
                                        <p>Ubah status notifikasi menjadi <b>Aktif</b> pada modul yang diinginkan (Simpanan, Pinjaman, Angsuran). Anda bisa memodifikasi kalimat template sesuai kebutuhan. Jangan lupa klik <b>Simpan Semua Perubahan</b>. Sistem kini akan mengirimkan notifikasi WA otomatis saat transaksi dilakukan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Pengaturan Lisensi -->
                    <div class="accordion-item mb-3 border-0 bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded shadow-sm fw-bold" style="background:#f8fafc; color:#0f172a;" type="button" data-bs-toggle="collapse" data-bs-target="#panduanLisensi">
                                <i class="fas fa-key me-2 text-warning"></i> 9. Aktivasi Lisensi PRO
                            </button>
                        </h2>
                        <div id="panduanLisensi" class="accordion-collapse collapse" data-bs-parent="#accordionPanduan">
                            <div class="accordion-body px-4 pt-4 pb-2">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h6>Hubungi Developer untuk Dapatkan Kode</h6>
                                        <p>Kunjungi menu <b>"Kustomisasi & Support"</b> dan hubungi developer via WhatsApp untuk mendapatkan kode lisensi PRO.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h6>Masukkan Kode Lisensi di Pengaturan</h6>
                                        <p>Buka menu <b>"Pengaturan Master"</b> → Gulir ke bagian tabel <b>"Lainnya"</b> → Isi kolom <b>"Kode Lisensi"</b> yang tersedia → Klik <b>"Simpan Semua Perubahan"</b>.</p>
                                    </div>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h6>Verifikasi Aktivasi</h6>
                                        <p>Setelah disimpan, badge <b>"VERSI GRATIS"</b> pada sidebar akan berubah menjadi <b>"VERSI PRO"</b> (hijau teal). Tombol Input Massal dan aksi Master Kelompok akan langsung aktif.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
