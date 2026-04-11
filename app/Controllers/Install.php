<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Install extends Controller
{
    public function index()
    {
        // Fitur instalasi web ini bisa Anda berikan batas aman 
        // misalnya mengecek apakah file .env sudah ada dan database terkoneksi.
        // Jika tabel sudah ada, kita skip migration.
        
        echo "<div style='font-family: sans-serif; padding: 20px; text-align: center; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 8px;'>";
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
                // Cek jika master kelompok kosong, kita panggil juga kalo ada 
                // Atau seed AdminSeeder sudah meng-handle semuanya.
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
            echo "</div>";
        }
        
        echo "</div>";
    }
}
