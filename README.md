# 🏦 Aplikasi Manajemen Koperasi

Aplikasi web berbasis **CodeIgniter 4** untuk manajemen koperasi simpan pinjam. Cocok untuk koperasi skala kecil hingga menengah.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| 👥 Anggota | CRUD data anggota + akun login otomatis |
| 💰 Simpanan | Setor & tarik simpanan multi-jenis, terintegrasi kas |
| 📋 Pinjaman | Pengajuan, approval, dan manajemen pinjaman |
| 💳 Angsuran | Cicilan bulanan + pelunasan awal (early payoff) |
| 📊 Laporan | Neraca, SHU, Arus Kas, & laporan per anggota |
| 🗄️ Buku Kas | Kas umum otomatis + entri manual |
| 💾 Backup | Backup & restore database via antarmuka web |
| ⚙️ Pengaturan | Identitas koperasi, parameter keuangan & lisensi |
| 🌟 PRO: Kelompok | Master Kelompok & pemindahan massal anggota |
| 🌟 PRO: Massal | Input simpanan + angsuran massal per kelompok |

---

## 🖥️ Kebutuhan Sistem

- **PHP** 8.1 atau lebih tinggi
- **MySQL** 5.7 / MariaDB 10.4 atau lebih tinggi
- **Web Server**: Apache (XAMPP/Laragon) atau Nginx
- **Composer** 2.x *(hanya untuk metode developer/Git)*

---

## 🚀 Cara Instalasi

### Metode 1 — Web Installer (Direkomendasikan untuk End User)

Cara termudah tanpa baris perintah apapun.

1. **Download** paket ZIP dari developer / halaman rilis GitHub.
2. **Ekstrak** isi ZIP ke folder web server Anda.
   - XAMPP: `C:\xampp\htdocs\koperasi\`
   - cPanel Hosting: Folder `public_html/` atau subdirektori.
3. **Buat database kosong** di phpMyAdmin (misal: `koperasi_db`).
4. **Buka browser** dan akses URL aplikasi Anda:
   - Lokal: `http://localhost/koperasi/`
   - Subdirektori: `http://localhost:8088/nama-folder/`
5. Jika file `.env` belum ada, sistem otomatis menampilkan **halaman Web Installer**.
6. **Isi form installer**:
   - `Base App URL` → URL lengkap aplikasi Anda *(tanpa `/public`)*
   - Informasi koneksi database (host, nama DB, username, password, port)
   - Pilih mode database: **Bersih** (production) atau **Isi Data Demo** (percobaan)
7. Klik **"Simpan & Install Sekarang"** — sistem otomatis migrasi database dan redirect ke halaman login.

> ✅ Selesai! Login dengan `admin` / `admin123` dan segera ganti password.

---

### Metode 2 — Via Git / Command Line (Untuk Developer)

```bash
# 1. Clone repository
git clone https://github.com/USERNAME/koperasi.git
cd koperasi

# 2. Install dependencies PHP
composer install

# 3. Salin & edit file environment
cp .env.example .env
```

Edit `.env` sesuai server Anda:

```ini
CI_ENVIRONMENT = development
app.baseURL    = 'http://localhost/koperasi/'
app.indexPage  = ''

database.default.hostname = localhost
database.default.database = koperasi_db
database.default.username = root
database.default.password = ''
database.default.port     = 3306
```

```bash
# 4. Jalankan migrasi
php spark migrate

# 5a. Hanya akun admin (Production)
php spark db:seed AdminSeeder

# 5b. Data demo lengkap (Percobaan)
php spark db:seed AdminSeeder
php spark db:seed DemoSeeder
```

---

## 🔐 Login Default

| Field | Nilai |
|---|---|
| Username | `admin` |
| Password | `admin123` |

> ⚠️ **Segera ganti password** setelah login pertama kali.

---

## 🌟 Lisensi Premium

### Versi Gratis
- ✅ Semua fitur inti (Anggota, Simpanan, Pinjaman, Angsuran, Laporan, Kas, Backup)
- ✅ Web Installer otomatis
- ✅ Multi-role & manajemen akses

### Versi PRO
- 🌟 Semua fitur gratis +
- 🌟 **Master Kelompok** — buat & kelola klasifikasi anggota
- 🌟 **Input Massal** — potong gaji seluruh kelompok dalam satu klik
- 🌟 **Kebijakan Pelunasan** — atur minimum tenor & biaya jasa pelunasan awal

Untuk mendapatkan lisensi PRO, hubungi developer. Masukkan kode lisensi di menu **Pengaturan Master → kolom Kode Lisensi**.

---

## 📁 Struktur Folder

```
koperasi/
├── app/
│   ├── Config/              → Konfigurasi aplikasi (App.php, Routes.php, dll)
│   ├── Controllers/         → Logika bisnis & routing halaman
│   ├── Models/              → Model database
│   ├── Views/               → Tampilan (HTML/PHP)
│   │   ├── layout/          → Template utama (sidebar, topbar)
│   │   ├── install/         → View Web Installer
│   │   └── informasi/       → Halaman panduan, fitur & support
│   ├── Database/
│   │   ├── Migrations/      → Skema tabel database
│   │   └── Seeds/           → Data awal (AdminSeeder, DemoSeeder)
│   └── Helpers/             → Fungsi bantu global (is_premium, get_pengaturan)
├── public/                  → Entry point & aset statis
│   └── assets/images/       → Aset gambar (coffee_pro.png, dll)
├── .htaccess                → Redirect root ke /public secara otomatis
├── .env.example             → Template konfigurasi
├── composer.json            → Daftar dependencies
└── README.md
```

---

## 🛠️ Perintah Berguna (Developer)

```bash
# Jalankan server development
php spark serve

# Migrasi database
php spark migrate

# Reset database (hati-hati!)
php spark migrate:rollback

# Jalankan seeder
php spark db:seed AdminSeeder
php spark db:seed DemoSeeder
```

---

## 📞 Kontak & Dukungan

Untuk pertanyaan, bug report, atau pembelian lisensi PRO:

- 📧 Email: cirebontech@gmail.com
- 💬 WhatsApp: +62 822-4062-9862

---

## 📄 Lisensi

Aplikasi ini dirilis sebagai **freeware** — bebas digunakan untuk keperluan non-komersial.
Dilarang menjual ulang tanpa izin dari developer.
