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
                
                // Redirect agar CI4 me-restart dan membaca .env terbaru
                return redirect()->to(current_url());
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
            echo "<button type='submit' style='margin-top: 10px; padding: 12px 20px; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; font-size:16px;'>Simpan Konfigurasi & Install Database</button>";
            echo "</form>";
            echo "</div>";
            return;
        }

        // Jika file .env sudah ada, lanjutkan ke tampilan migrasi normal
        echo "<div style='font-family: sans-serif; padding: 20px; text-align: center; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>";
        echo "<h2>🚀 Web Installer Koperasi</h2>";
        
        try {
            // 1. Eksekusi Migrasi (Membuat semua tabel system)
            echo "<p style='text-align: left;'>Mengeksekusi tabel database...</p>";
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            echo "<div style='text-align: left; color: green;'>✓ Tabel berhasil dibuat (Migration Complete)!</div>";

            // 2. Eksekusi Seeder (Data Awal)
            echo "<p style='text-align: left; margin-top: 20px;'>Mengisi Data Master & Akun Admin...</p>";
            $seeder = \Config\Database::seeder();
            
            // Cek apakah Admin sudah ada jangan dised lagi (cegah dobel seed)
            $db = \Config\Database::connect();
            $user_count = $db->table('users')->countAllResults();
            
            if ($user_count === 0) {
                $seeder->call('AdminSeeder'); // Pastikan seeder Name sesuai dgn yg Anda miliki
                echo "<div style='text-align: left; color: green;'>✓ Data Admin berhasil disuntikkan!</div>";
            } else {
                echo "<div style='text-align: left; color: orange;'>✓ Melewati Seeder (Data sudah ada).</div>";
            }

            echo "<br>";
            echo "<div style='padding: 15px; background: #e6f7ff; border-radius: 6px;'>";
            echo "<h3 style='margin:0 0 10px 0;'>🎉 INSTALASI BERHASIL!</h3>";
            echo "Anda dapat menyuruh End User Anda menggunakan perintah ini.<br><br>";
            echo "<a href='" . base_url() . "' style='display:inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>Masuk ke Aplikasi</a>";
            echo "</div>";

        } catch (\Throwable $e) {
            echo "<br><br><div style='padding: 15px; background: #ffebee; color: #c62828; border-radius: 6px; text-align: left;'>";
            echo "<strong>💥 Terjadi Kesalahan Instalasi:</strong><br><br>";
            echo $e->getMessage();
            echo "<br><br><small>Hapus file <strong>.env</strong> di root aplikasi jika Anda ingin mengulangi pengisian form koneksi database.</small>";
            echo "</div>";
        }
        
        echo "</div>";
    }
}
