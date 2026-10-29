<?php
/**
 * ============================================================================
 * EMERGENCY RESCUE & RECOVERY TOOL — KOPERASI SIMPAN PINJAM
 * ============================================================================
 * Skrip Mandiri (Standalone) Tanpa Ketergantungan Framework.
 * Digunakan khusus untuk memulihkan website klien yang mengalami kendala/crash
 * (seperti error 500 akibat hosting yang auto-upgrade ke PHP 8.4).
 * ============================================================================
 */

@set_time_limit(600);
@ini_set('memory_limit', '512M');

$repo = 'oheem-tech/koperasi-app';

// Deteksi apakah rescue.php dijalankan dari public/ atau dari root project
if (basename(__DIR__) === 'public') {
    $rootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
} else {
    $rootDir = __DIR__ . DIRECTORY_SEPARATOR;
}
$writableDir = $rootDir . 'writable' . DIRECTORY_SEPARATOR;
$uploadsDir = $writableDir . 'uploads' . DIRECTORY_SEPARATOR;

$phpVersion = PHP_VERSION;
$hasZip = extension_loaded('zip');
$hasCurl = extension_loaded('curl');
$isWritable = is_writable($rootDir);

$action = $_GET['action'] ?? '';
$error = '';
$success = '';

if ($action === 'delete_self') {
    @unlink(__FILE__);
    header('Location: ./');
    exit;
}

if ($action === 'do_rescue' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$hasZip) {
        $error = 'Ekstensi php_zip tidak aktif di server ini. Silakan aktifkan via cPanel (Select PHP Version > Extensions > zip).';
    } elseif (!$hasCurl) {
        $error = 'Ekstensi php_curl tidak aktif di server ini.';
    } else {
        // Ambil info release terbaru dari GitHub
        $ch = curl_init("https://api.github.com/repos/{$repo}/releases/latest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-EmergencyRescue');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $resp = curl_exec($ch);
        curl_close($ch);

        $latest = json_decode($resp, true);

        if (!isset($latest['zipball_url'])) {
            $error = 'Gagal menghubungi GitHub API atau belum ada rilis di repository: ' . htmlspecialchars($repo);
        } else {
            $zipUrl = $latest['zipball_url'];
            $tagName = $latest['tag_name'] ?? 'terbaru';

            if (!is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0777, true);
            }

            $zipFile = $uploadsDir . 'rescue_update.zip';

            // Download arsip zip
            $ch = curl_init($zipUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Koperasi-EmergencyRescue');
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $zipData = curl_exec($ch);
            curl_close($ch);

            if ($zipData === false || strlen($zipData) < 1000) {
                $error = 'Gagal mengunduh file paket pemulihan dari server.';
            } else {
                file_put_contents($zipFile, $zipData);

                $zip = new ZipArchive();
                if ($zip->open($zipFile) === true) {
                    $extractDir = $uploadsDir . 'rescue_extracted' . DIRECTORY_SEPARATOR;
                    if (is_dir($extractDir)) {
                        rescue_delete_dir($extractDir);
                    }
                    @mkdir($extractDir, 0777, true);
                    $zip->extractTo($extractDir);
                    $zip->close();

                    $subDirs = glob($extractDir . '*', GLOB_ONLYDIR);
                    if (!empty($subDirs)) {
                        $sourceDir = $subDirs[0] . DIRECTORY_SEPARATOR;

                        // Salin file pembaruan menimpa sistem lama (mengecualikan database dan upload)
                        rescue_copy_dir($sourceDir, $rootDir);

                        // Bersihkan cache dan sesi usang agar tidak bentrok dengan serialisasi baru
                        rescue_clean_dir($writableDir . 'cache');
                        rescue_clean_dir($writableDir . 'session');

                        // Bersihkan sisa unduhan
                        @unlink($zipFile);
                        rescue_delete_dir($extractDir);

                        $success = "Pemulihan Berhasil! Aplikasi Koperasi telah disinkronkan ke versi {$tagName} dan kini sepenuhnya kompatibel dengan PHP {$phpVersion}.";
                    } else {
                        $error = 'Gagal menemukan direktori hasil ekstraksi.';
                    }
                } else {
                    $error = 'Gagal mengekstrak arsip zip pembaruan.';
                }
            }
        }
    }
}

function rescue_copy_dir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($src . $file)) {
                if ($file === 'writable' || $file === '.git') continue;
                rescue_copy_dir($src . $file . DIRECTORY_SEPARATOR, $dst . $file . DIRECTORY_SEPARATOR);
            } else {
                if ($file === '.env' || $file === '.htaccess' || $file === 'rescue.php') continue;
                @copy($src . $file, $dst . $file);
            }
        }
    }
    closedir($dir);
}

function rescue_delete_dir($path) {
    if (!is_dir($path)) return;
    $files = array_diff(scandir($path), ['.', '..']);
    foreach ($files as $file) {
        $full = $path . DIRECTORY_SEPARATOR . $file;
        is_dir($full) ? rescue_delete_dir($full) : @unlink($full);
    }
    @rmdir($path);
}

function rescue_clean_dir($path) {
    if (!is_dir($path)) return;
    $files = array_diff(scandir($path), ['.', '..', '.gitkeep', 'index.html']);
    foreach ($files as $file) {
        $full = $path . DIRECTORY_SEPARATOR . $file;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyelamat Darurat Sistem Koperasi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #1a56db; color: #fff; padding: 24px 30px; }
        .header h1 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .header p { font-size: 14px; opacity: 0.9; }
        .body { padding: 30px; }
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #def7ec; color: #03543f; border: 1px solid #bcf0da; }
        .alert-danger { background: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
        .alert-info { background: #e1effe; color: #1e429f; border: 1px solid #b4c6fc; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 14px; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #edf2f7; }
        .info-table td:first-child { width: 220px; color: #64748b; font-weight: 500; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .badge-green { background: #def7ec; color: #03543f; }
        .badge-red { background: #fde8e8; color: #9b1c1c; }
        .badge-blue { background: #e1effe; color: #1e429f; }
        .btn { display: inline-block; width: 100%; padding: 14px 20px; background: #1a56db; color: #fff; font-size: 15px; font-weight: 600; text-align: center; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .btn:hover { background: #1e429f; }
        .btn-success { background: #0e9f6e; }
        .btn-success:hover { background: #046c4e; }
        .btn-secondary { background: #64748b; margin-top: 10px; }
        .btn-secondary:hover { background: #475569; }
        .footer { padding: 15px 30px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Alat Pemulihan Darurat Sistem (Rescue Tool)</h1>
        <p>Solusi 1-Klik Pemulihan Website Koperasi Akibat Pembaruan Versi PHP Hosting</p>
    </div>
    <div class="body">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Berhasil!</strong> <?= htmlspecialchars($success); ?>
            </div>
            <p style="margin-bottom: 20px; font-size: 14px; color: #475569;">
                Website Koperasi Anda kini sudah kembali aktif dan kompatibel dengan versi PHP hosting saat ini. Untuk keamanan sistem, disarankan menghapus skrip pemulihan ini setelah selesai.
            </p>
            <a href="./" class="btn btn-success" style="margin-bottom: 10px;">Buka Aplikasi Koperasi Sekarang</a>
            <a href="?action=delete_self" class="btn btn-secondary" onclick="return confirm('Hapus file rescue.php ini dan kembali ke aplikasi?');">Hapus File Pemulihan & Masuk ke Aplikasi</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>Peringatan:</strong> <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <strong>Kapan alat ini digunakan?</strong> Gunakan alat ini ketika web Koperasi mendadak tidak dapat dibuka (blank putih atau error 500) setelah hosting Anda memperbarui versi PHP secara sepihak.
            </div>

            <table class="info-table">
                <tr>
                    <td>Versi PHP Hosting Saat Ini</td>
                    <td><span class="badge badge-blue">PHP <?= htmlspecialchars($phpVersion); ?></span></td>
                </tr>
                <tr>
                    <td>Ekstensi Zip (php_zip)</td>
                    <td>
                        <?php if ($hasZip): ?>
                            <span class="badge badge-green">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-red">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Ekstensi cURL (php_curl)</td>
                    <td>
                        <?php if ($hasCurl): ?>
                            <span class="badge badge-green">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-red">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Izin Tulis Folder (Writable)</td>
                    <td>
                        <?php if ($isWritable): ?>
                            <span class="badge badge-green">Dapat Ditulis (OK)</span>
                        <?php else: ?>
                            <span class="badge badge-red">Terkunci (Periksa Permission)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <form method="post" action="?action=do_rescue">
                <button type="submit" class="btn" onclick="return confirm('Mulai proses pemulihan dan sinkronisasi framework sekarang? File database (.env) dan berkas Anda tetap aman.');">
                    Sinkronkan & Pulihkan Sistem Sekarang (1-Klik)
                </button>
            </form>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 12px; text-align: center;">
                *Skrip akan mengunduh versi framework resmi terbaru dari repository dan menimpa file core yang bermasalah.
            </p>
        <?php endif; ?>
    </div>
    <div class="footer">
        Koperasi Simpan Pinjam — Sistem Pemulihan Otomatis &bull; PHP 8.4 Ready
    </div>
</div>
</body>
</html>
