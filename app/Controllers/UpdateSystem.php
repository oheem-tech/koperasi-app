<?php

namespace App\Controllers;

use Config\Updater;

class UpdateSystem extends BaseController
{
    public function index()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $updaterConfig = new Updater();
        $currentVersion = $updaterConfig->currentVersion;
        $repo = $updaterConfig->githubRepo;

        // Cek update ke GitHub
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/{$repo}/releases/latest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-AutoUpdater');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        $latestRelease = json_decode($response, true);

        $data = [
            'title'          => 'Pembaruan Sistem (Auto-Updater)',
            'currentVersion' => $currentVersion,
            'latestRelease'  => $latestRelease,
            'hasUpdate'      => false,
            'phpVersion'     => PHP_VERSION,
            'isPhpSupported' => version_compare(PHP_VERSION, '8.1.0', '>=')
        ];

        if (isset($latestRelease['tag_name'])) {
            $latestVersion = preg_replace('/^[^\d]+/', '', trim($latestRelease['tag_name']));
            $currVer = preg_replace('/^[^\d]+/', '', trim($currentVersion));
            if (version_compare($latestVersion, $currVer, '>')) {
                $data['hasUpdate'] = true;
            }
        }

        return view('updater/index', $data);
    }

    public function process()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        // Pastikan ekstensi yang dibutuhkan aktif
        if (!extension_loaded('zip')) {
            return redirect()->back()->with('error', 'Gagal memulai pembaruan: Ekstensi php_zip tidak aktif di hosting ini.');
        }

        // Peringatan jika PHP belum memenuhi syarat CI 4.6 (minimal 8.1)
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            return redirect()->back()->with('error', 'Versi PHP hosting Anda saat ini adalah ' . PHP_VERSION . '. Versi pembaruan ini memerlukan minimal PHP 8.1 (direkomendasikan PHP 8.2 atau 8.4). Harap ubah versi PHP di cPanel hosting terlebih dahulu.');
        }

        // Beri waktu dan memori yang cukup untuk download & ekstrak di hosting
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $updaterConfig = new Updater();
        $repo = $updaterConfig->githubRepo;

        // Ambil info rilis terbaru
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/{$repo}/releases/latest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-AutoUpdater');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        $latestRelease = json_decode($response, true);
        if (!isset($latestRelease['zipball_url'])) {
            return redirect()->back()->with('error', 'Gagal mendapatkan tautan unduhan dari GitHub.');
        }

        $zipUrl = $latestRelease['zipball_url'];
        $zipFile = WRITEPATH . 'uploads/update.zip';

        // Pastikan folder uploads ada
        if (!is_dir(WRITEPATH . 'uploads')) {
            mkdir(WRITEPATH . 'uploads', 0777, true);
        }

        // 1. Download ZIP
        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-AutoUpdater');
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $fileData = curl_exec($ch);
        curl_close($ch);
        
        if ($fileData === false || strlen($fileData) < 1000) {
            return redirect()->back()->with('error', 'Gagal mengunduh file pembaruan dari repositori.');
        }
        file_put_contents($zipFile, $fileData);

        // 2. Ekstrak ZIP
        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $extractPath = WRITEPATH . 'uploads/update_extracted/';
            if (is_dir($extractPath)) {
                $this->deleteDir($extractPath);
            }
            mkdir($extractPath, 0777, true);
            $zip->extractTo($extractPath);
            $zip->close();

            // Folder hasil ekstrak dari GitHub biasanya 'username-repo-hash'
            $subDirs = glob($extractPath . '*', GLOB_ONLYDIR);
            if (!empty($subDirs)) {
                $sourceDir = $subDirs[0] . '/';
                $targetDir = defined('ROOTPATH') ? ROOTPATH : (FCPATH . '../');

                // Copy file dari hasil ekstrak ke root, overwrite yang ada
                $this->copyDir($sourceDir, $targetDir);

                // Jalankan migrasi resmi CodeIgniter 4 jika ada skema database baru
                try {
                    $migrate = \Config\Services::migrations();
                    $migrate->latest();
                } catch (\Throwable $e) {
                    log_message('error', 'Auto-updater database migration failed: ' . $e->getMessage());
                }

                // Bersihkan cache dan sesi usang agar tidak bentrok dengan serialisasi class baru
                $this->cleanDir(WRITEPATH . 'cache');
                $this->cleanDir(WRITEPATH . 'session');

                // Bersihkan file skrip database_update.php legacy jika terbawa dalam arsip
                $dbUpdateScript = $targetDir . 'database_update.php';
                if (file_exists($dbUpdateScript)) {
                    @unlink($dbUpdateScript);
                }

                // Update versi di file konfigurasi lokal
                $newVersion = preg_replace('/^[^\d]+/', '', trim($latestRelease['tag_name']));
                $configPath = APPPATH . 'Config/Updater.php';
                if (file_exists($configPath)) {
                    $configContent = file_get_contents($configPath);
                    $configContent = preg_replace("/public \\\$currentVersion = '.*?';/", "public \$currentVersion = '{$newVersion}';", $configContent);
                    file_put_contents($configPath, $configContent);
                }

                // Bersihkan file sementara
                @unlink($zipFile);
                $this->deleteDir($extractPath);

                return redirect()->back()->with('success', 'Sistem berhasil diperbarui ke versi ' . $newVersion . ' (Kompatibel PHP 8.4)');
            }
        }

        return redirect()->back()->with('error', 'Gagal mengekstrak file pembaruan. Pastikan ekstensi php_zip aktif di hosting.');
    }

    private function copyDir($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    // Jangan overwrite folder writable (log, uploads, session) dan .git
                    if ($file == 'writable' || $file == '.git') continue;
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    // Jangan overwrite file konfigurasi database lokal & environment production
                    if ($file == '.env' || $file == '.htaccess') continue;
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function deleteDir($dirPath) {
        if (!is_dir($dirPath)) return;
        $files = array_diff(scandir($dirPath), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dirPath/$file")) ? $this->deleteDir("$dirPath/$file") : unlink("$dirPath/$file");
        }
        rmdir($dirPath);
    }

    private function cleanDir($dirPath) {
        if (!is_dir($dirPath)) return;
        $files = array_diff(scandir($dirPath), array('.', '..', '.gitkeep', 'index.html'));
        foreach ($files as $file) {
            $fullPath = rtrim($dirPath, '/\\') . DIRECTORY_SEPARATOR . $file;
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
}
