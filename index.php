<?php
/**
 * Root Entry Point - Koperasi Simpan Pinjam
 * Memudahkan akses aplikasi langsung melalui http://localhost/nama-folder/
 * tanpa mewajibkan pengguna mengetik /public/ secara manual di browser.
 */
$publicIndex = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

if (file_exists($publicIndex)) {
    require $publicIndex;
} else {
    header('Location: public/');
    exit;
}
