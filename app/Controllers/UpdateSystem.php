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
        $response = curl_exec($ch);
        curl_close($ch);

        $latestRelease = json_decode($response, true);

        $data = [
            'title' => 'Pembaruan Sistem (Auto-Updater)',
            'currentVersion' => $currentVersion,
            'latestRelease' => $latestRelease,
            'hasUpdate' => false
        ];

        if (isset($latestRelease['tag_name'])) {
            $latestVersion = str_replace('v', '', $latestRelease['tag_name']);
            $currVer = str_replace('v', '', $currentVersion);
            if (version_compare($latestVersion, $currVer, '>')) {
                $data['hasUpdate'] = true;
            }
        }

        return view('updater/index', $data);
    }

    public function process()
    {
        if (!has_permission('manage_pengaturan')) return redirect()->to('/dashboard');

        $updaterConfig = new Updater();
        $repo = $updaterConfig->githubRepo;

        // Ambil info rilis terbaru lagi
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/{$repo}/releases/latest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-AutoUpdater');
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
        $fileData = curl_exec($ch);
        curl_close($ch);
        
        if ($fileData === false) {
            return redirect()->back()->with('error', 'Gagal mengunduh file pembaruan.');
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
                $targetDir = FCPATH . '../'; // Root direktori aplikasi

                // Copy file dari hasil ekstrak ke root, overwrite yang ada
                $this->copyDir($sourceDir, $targetDir);

                // Eksekusi skrip migrasi/update database jika ada
                $dbUpdateScript = $targetDir . 'database_update.php';
                if (file_exists($dbUpdateScript)) {
                    include $dbUpdateScript;
                    @unlink($dbUpdateScript);
                }

                // Update versi di file konfigurasi lokal
                $newVersion = str_replace('v', '', $latestRelease['tag_name']);
                $configPath = APPPATH . 'Config/Updater.php';
                if (file_exists($configPath)) {
                    $configContent = file_get_contents($configPath);
                    $configContent = preg_replace("/public \\\$currentVersion = '.*?';/", "public \$currentVersion = '{$newVersion}';", $configContent);
                    file_put_contents($configPath, $configContent);
                }

                // Bersihkan file sementara
                @unlink($zipFile);
                $this->deleteDir($extractPath);

                return redirect()->back()->with('success', 'Sistem berhasil diperbarui ke versi ' . $newVersion);
            }
        }

        return redirect()->back()->with('error', 'Gagal mengekstrak file pembaruan. Pastikan ekstensi php_zip aktif.');
    }

    private function copyDir($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    // Jangan overwrite folder writable (log, uploads, session)
                    if ($file == 'writable' || $file == '.git') continue;
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
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
}
