# 🏦 Aplikasi Manajemen Koperasi

Aplikasi web berbasis **CodeIgniter 4** untuk manajemen koperasi simpan pinjam. Cocok untuk koperasi skala kecil hingga menengah.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| 👥 Anggota | CRUD data anggota koperasi |
| 💰 Simpanan | Pencatatan simpanan pokok, wajib, dan sukarela |
| 📋 Pinjaman | Pengajuan dan pengelolaan pinjaman |
| 💳 Angsuran | Pencatatan cicilan & pelunasan otomatis |
| 📊 Laporan | Neraca, SHU, laporan anggota & kas |
| ⚙️ Pengaturan | Identitas koperasi & parameter keuangan |
| 🌟 Fitur PRO | Master Kelompok & Input Massal (lisensi premium) |

---

## 🖥️ Kebutuhan Sistem

- **PHP** 8.1 atau lebih tinggi
- **MySQL** 5.7 / MariaDB 10.4 atau lebih tinggi
- **Composer** 2.x
- **Web Server**: Apache (XAMPP/Laragon) atau Nginx

---

## 🚀 Cara Instalasi

### Metode 1 — Via Git (Direkomendasikan)

```bash
# 1. Clone repository
git clone https://github.com/USERNAME/koperasi.git
cd koperasi

# 2. Install dependencies PHP
composer install

# 3. Salin & konfigurasi file environment
cp .env.example .env
```

Edit file `.env` sesuai konfigurasi server kamu:

```ini
CI_ENVIRONMENT = development
app.baseURL    = 'http://localhost/koperasi/public/'

database.default.hostname = localhost
database.default.database = koperasi_db   # ← nama database kamu
database.default.username = root           # ← username MySQL
database.default.password = ''             # ← password MySQL
database.default.port     = 3306
```

```bash
# 4. Buat database di MySQL (nama sesuai .env)
# Contoh: CREATE DATABASE koperasi_db;

# 5. Jalankan migrasi via command line (terminal)
php spark migrate
```

### ⚙️ Pilihan Database (Pilih Salah Satu)

Setelah migrasi selesai, Anda harus memasukkan data awal. Pilih salah satu dari dua opsi berikut:

**Opsi A: Database Murni (Direkomendasikan untuk Production)**
Hanya membuat akun `admin` (password: `admin123`) tanpa data transaksi dummy.
```bash
php spark db:seed AdminSeeder
```

**Opsi B: Database Demo (Direkomendasikan untuk Coba-Coba)**
Memasukkan data sampel: 2 akun anggota lengkap dengan transaksi kas awal, simpanan, pengajuan pinjaman, dan angsuran berjalan.
```bash
php spark db:seed AdminSeeder
php spark db:seed DemoSeeder
```

---

Setelah itu, jalankan server dan buka aplikasi:
```bash
php spark serve
# Buka di browser: http://localhost:8080/
```

### Metode 2 — Download ZIP

1. Download ZIP dari halaman GitHub → **Code → Download ZIP**
2. Ekstrak ke folder web server (misal: `htdocs/koperasi/`)
3. Rename `.env.example` → `.env`, lalu isi konfigurasi database
4. Buka `http://localhost/koperasi/public/migrate.php`
5. Aplikasi siap digunakan

---

## 🔐 Login Default

Setelah migrasi, gunakan akun berikut:

| Field | Nilai |
|---|---|
| Username | `admin` |
| Password | `admin123` |

> ⚠️ **Segera ganti password** setelah login pertama kali.

---

## 🌟 Lisensi Premium

Aplikasi ini tersedia dalam dua versi:

### Versi Gratis
- ✅ Semua fitur dasar (Anggota, Simpanan, Pinjaman, Angsuran, Laporan)
- ✅ Tidak perlu konfigurasi lisensi apapun
- ✅ Langsung bisa digunakan setelah instalasi

### Versi Premium
- 🌟 Semua fitur gratis +
- 🌟 **Master Kelompok** — pengelompokan anggota
- 🌟 **Input Massal** — input transaksi banyak anggota sekaligus

Untuk mendapatkan lisensi premium, hubungi developer dan masukkan kode lisensi di menu **Pengaturan → Kode Lisensi**.

---

## 📁 Struktur Folder

```
koperasi/
├── app/
│   ├── Config/          → Konfigurasi aplikasi
│   ├── Controllers/     → Controller (logika bisnis)
│   ├── Models/          → Model database
│   ├── Views/           → Tampilan (HTML/PHP)
│   ├── Database/
│   │   ├── Migrations/  → Skema tabel database
│   │   └── Seeds/       → Data awal
│   └── Helpers/         → Fungsi bantu global
├── public/              → Entry point (document root)
├── .env.example         → Template konfigurasi
├── composer.json        → Daftar dependencies
└── README.md
```

---

## 🛠️ Perintah Berguna

```bash
# Menjalankan server development
php spark serve

# Menjalankan migrasi database
php spark migrate

# Reset database (hati-hati!)
php spark migrate:rollback
```

---

## 📞 Kontak & Dukungan

Untuk pertanyaan, bug report, atau pembelian lisensi premium:

- 📧 Email: developer@koperasi.app
- 💬 WhatsApp: +62 xxx-xxxx-xxxx

---

## 📄 Lisensi

Aplikasi ini dirilis sebagai **freeware** — bebas digunakan untuk keperluan non-komersial.
Dilarang menjual ulang tanpa izin dari developer.
