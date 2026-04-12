<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Install extends Controller
{
    public function index()
    {
        $envPath = ROOTPATH . '.env';
        $envExamplePath = ROOTPATH . '.env.example';

        // Jika form setup .env di-submit
        if ($this->request->getPost('setup_env')) {
            $dbHost = $this->request->getPost('db_host');
            $dbPort = $this->request->getPost('db_port');
            $dbUser = $this->request->getPost('db_user');
            $dbPass = $this->request->getPost('db_pass');
            $dbName = $this->request->getPost('db_name');
            $baseURL = $this->request->getPost('base_url');

            if (file_exists($envExamplePath)) {
                $envContent = file_get_contents($envExamplePath);
                
                // Replace configurasi database dan baseURL
                $envContent = preg_replace('/^database\.default\.hostname\s*=\s*.*/m', "database.default.hostname = $dbHost", $envContent);
                $envContent = preg_replace('/^database\.default\.port\s*=\s*.*/m', "database.default.port = $dbPort", $envContent);
                $envContent = preg_replace('/^database\.default\.database\s*=\s*.*/m', "database.default.database = $dbName", $envContent);
                $envContent = preg_replace('/^database\.default\.username\s*=\s*.*/m', "database.default.username = $dbUser", $envContent);
                $envContent = preg_replace('/^database\.default\.password\s*=\s*.*/m', "database.default.password = '$dbPass'", $envContent);
                if ($baseURL) {
                    $envContent = preg_replace('/^app\.baseURL\s*=\s*.*/m', "app.baseURL = '$baseURL'", $envContent);
                }

                // Ubah CI_ENVIRONMENT jadi production sesuai environment end-user
                $envContent = preg_replace('/^CI_ENVIRONMENT\s*=\s*.*/m', "CI_ENVIRONMENT = production", $envContent);

                file_put_contents($envPath, $envContent);
                $dummy = $this->request->getPost('install_dummy') == '1' ? '?dummy=1' : '';

                // Gunakan javascript redirect agar browser yang menentukan absolute URL-nya
                echo "<script>window.location.href = 'install" . htmlspecialchars($dummy) . "';</script>";
                exit;
            } else {
                echo "<div style='color:red; text-align:center;'>File .env.example tidak ditemukan. Proses dibatalkan.</div>";
                return;
            }
        }

        // Cek apakah file .env belum ada
        if (!file_exists($envPath)) {
            // Prediksi Base URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $script = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
            // Hapus '/public/' di akhir jika ada, karena kita pakai .htaccess di root
            $script = preg_replace('/\/public\/?$/', '/', $script);
            $guessedBaseUrl = rtrim($protocol . "://" . $host . $script, '/') . '/';

            echo "<div style='font-family: sans-serif; padding: 20px; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>";
            echo "<h2 style='text-align: center;'>⚙️ Setup Konfigurasi Koperasi</h2>";
            echo "<p style='text-align: center; color: #555;'>File konfigurasi database (<strong>.env</strong>) belum terdeteksi. Silakan lengkapi form di bawah ini.</p>";
            echo "<form method='post' action='' style='display:flex; flex-direction:column; gap: 15px; margin-top:20px;'>";
            echo csrf_field();
            echo "<input type='hidden' name='setup_env' value='1'>";
            echo "<div><label style='font-weight:bold; font-size:14px;'>Base URL Aplikasi:</label><br><input type='text' name='base_url' value='{$guessedBaseUrl}' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;' required></div>";
            echo "<div style='display:flex; gap:10px;'>";
            echo "<div style='flex:3;'><label style='font-weight:bold; font-size:14px;'>Database Host:</label><br><input type='text' name='db_host' value='localhost' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;' required></div>";
            echo "<div style='flex:1;'><label style='font-weight:bold; font-size:14px;'>Port:</label><br><input type='number' name='db_port' value='3306' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;' required></div>";
            echo "</div>";
            echo "<div><label style='font-weight:bold; font-size:14px;'>Nama Database:</label><br><input type='text' name='db_name' placeholder='Misal: koperasi_db' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;' required></div>";
            echo "<div><label style='font-weight:bold; font-size:14px;'>Username Database:</label><br><input type='text' name='db_user' value='root' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;' required></div>";
            echo "<div><label style='font-weight:bold; font-size:14px;'>Password Database:</label><br><input type='text' name='db_pass' placeholder='(Kosongkan jika di XAMPP default)' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;'></div>";
            echo "<div><label style='font-weight:bold; font-size:14px;'>Jenis Instalasi (Opsional):</label><br>
                 <select name='install_dummy' style='width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;'>
                    <option value='0'>Database Kosong (Hanya Akun Admin)</option>
                    <option value='1'>Isi dengan Data Dummy (Demo)</option>
                 </select></div>";
            echo "<button type='submit' style='margin-top: 10px; padding: 12px 20px; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; font-size:16px;'>Simpan Konfigurasi & Install Database</button>";
            echo "</form>";
            echo "</div>";
            return;
        }

        // Jika file .env sudah ada, lanjutkan ke tampilan migrasi normal
        $loginUrl = base_url('auth');
        $isDummy  = $this->request->getGet('dummy') == '1';

        echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Web Installer — Koperasi</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 560px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #1d4ed8, #0ea5e9);
            padding: 36px 30px 30px;
            text-align: center;
            position: relative;
        }
        .card-header .badge-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            font-size: 30px;
        }
        .card-header h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.3px;
        }
        .card-header p {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            margin-top: 6px;
        }
        .card-body { padding: 28px 30px; }
        .log-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 22px;
        }
        .log-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .log-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.825rem;
            color: #374151;
        }
        .log-item:last-child { border-bottom: none; }
        .log-item .dot {
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            font-weight: 700;
        }
        .dot-ok  { background: #d1fae5; color: #059669; }
        .dot-skip { background: #fef3c7; color: #d97706; }
        .dot-info { background: #dbeafe; color: #2563eb; }
        .success-box {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 22px;
        }
        .success-box .emoji { font-size: 2.2rem; margin-bottom: 8px; }
        .success-box h2 { font-size: 1.15rem; font-weight: 800; color: #065f46; margin-bottom: 4px; }
        .success-box p  { font-size: 0.82rem; color: #047857; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 22px; }
        .info-cell {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px 14px;
        }
        .info-cell .lbl { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #0369a1; margin-bottom: 3px; }
        .info-cell .val { font-size: 0.85rem; font-weight: 600; color: #0c4a6e; font-family: monospace; }
        .btn-login {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1d4ed8, #0ea5e9);
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-align: center;
            transition: opacity .2s;
            box-shadow: 0 4px 15px rgba(14,165,233,0.4);
        }
        .btn-login:hover { opacity: 0.9; }
        .note { text-align: center; font-size: 0.75rem; color: #94a3b8; margin-top: 14px; }
        .note a { color: #64748b; }
    </style>
</head>
<body>
<div class='card'>
    <div class='card-header'>
        <div class='badge-icon'>🚀</div>
        <h1>Web Installer Koperasi</h1>
        <p>Proses instalasi sedang berjalan...</p>
    </div>
    <div class='card-body'>";

        $logItems = [];
        $hasError = false;

        try {
            // 1. Migrasi
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            $logItems[] = ['ok', 'Struktur tabel database berhasil dibuat'];

            // 2. Seeder
            $seeder = \Config\Database::seeder();
            $db = \Config\Database::connect();
            $user_count = $db->table('users')->countAllResults();

            if ($user_count === 0) {
                $seeder->call('AdminSeeder');
                $logItems[] = ['ok', 'Akun Admin default berhasil dibuat'];

                if ($isDummy) {
                    $seeder->call('DemoSeeder');
                    $logItems[] = ['ok', 'Data demo (3 anggota & transaksi) berhasil disuntikkan'];
                } else {
                    $logItems[] = ['info', 'Melewati data demo – database kosong siap digunakan'];
                }
            } else {
                $logItems[] = ['skip', 'Data admin sudah ada, proses seeder dilewati'];
            }

        } catch (\Throwable $e) {
            $hasError = true;
            $logItems[] = ['err', 'ERROR: ' . $e->getMessage()];
        }

        // Render log box
        echo "<div class='log-box'>";
        echo "<div class='log-title'>📋 Log Proses Instalasi</div>";
        foreach ($logItems as $item) {
            $dotClass = $item[0] === 'ok' ? 'dot-ok' : ($item[0] === 'skip' ? 'dot-skip' : ($item[0] === 'info' ? 'dot-info' : 'dot-err'));
            $icon     = $item[0] === 'ok' ? '✓' : ($item[0] === 'skip' ? '~' : ($item[0] === 'info' ? 'i' : '✗'));
            echo "<div class='log-item'><div class='dot {$dotClass}'>{$icon}</div><span>" . htmlspecialchars($item[1]) . "</span></div>";
        }
        echo "</div>";

        if (!$hasError) {
            // Success banner
            echo "<div class='success-box'>
                <div class='emoji'>🎉</div>
                <h2>Instalasi Berhasil!</h2>
                <p>Aplikasi Koperasi Anda sudah siap digunakan.</p>
            </div>";

            // Credential info box
            echo "<div class='info-grid'>
                <div class='info-cell'>
                    <div class='lbl'>Username Admin</div>
                    <div class='val'>admin</div>
                </div>
                <div class='info-cell'>
                    <div class='lbl'>Password Admin</div>
                    <div class='val'>admin123</div>
                </div>
            </div>";

            // Login button — pointing to correct login page
            echo "<a href='{$loginUrl}' class='btn-login'>🔑 Masuk ke Halaman Login</a>";
            echo "<p class='note'>Segera ubah password setelah login pertama kali.</p>";
        } else {
            echo "<div style='background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;color:#991b1b;font-size:0.85rem;'>
                <strong>⚠️ Instalasi gagal.</strong> Periksa log di atas.<br><br>
                <small>Hapus file <code>.env</code> di direktori root aplikasi, lalu akses halaman ini kembali untuk mencoba ulang.</small>
            </div>";
        }

        echo "   </div>
</div>
</body>
</html>";
    }
}
