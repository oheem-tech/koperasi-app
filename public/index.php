<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// ── Installer Guard ──────────────────────────────────────────────────────────
// Jika file .env belum ada dan database belum dikonfigurasi, redirect ke installer
$rootPath = dirname(FCPATH) . DIRECTORY_SEPARATOR;
$hasEnv = file_exists($rootPath . '.env')
       || file_exists(FCPATH . '.env')
       || (isset($paths->appDirectory) && file_exists(rtrim($paths->appDirectory, '\\/ ') . '/../.env'));

// Periksa apakah database sudah dikonfigurasi langsung di Config\Database
$hasDbConfig = false;
if (file_exists($paths->appDirectory . '/Config/Database.php')) {
    require_once $paths->appDirectory . '/Config/Database.php';
    if (class_exists('Config\Database')) {
        $dbCfg = new Config\Database();
        $hasDbConfig = !empty($dbCfg->default['database']);
    }
}

if (!$hasEnv && !$hasDbConfig) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    // Normalisasi: ambil path tanpa query string
    $requestPath = strtok($requestUri, '?');
    // Izinkan akses ke /install saja, blokir semua path lain
    if (!preg_match('#/install#i', $requestPath)) {
        // Deteksi base path otomatis (misal: /kopercob atau /demo-koperasi)
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $installUrl = $scriptDir . '/install';
        header('Location: ' . $installUrl, true, 302);
        exit;
    }
}
// ────────────────────────────────────────────────────────────────────────────

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
