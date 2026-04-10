<?php

if (!function_exists('get_pengaturan')) {
    function get_pengaturan($key, $default = '')
    {
        $db = \Config\Database::connect();
        $row = $db->table('pengaturan')->where('pengaturan_key', $key)->get()->getRowArray();
        
        if ($row) {
            return $row['pengaturan_value'];
        }
        
        return $default;
    }
}

if (!function_exists('get_koperasi_logo')) {
    function get_koperasi_logo()
    {
        $logo = get_pengaturan('koperasi_logo');
        if (!empty($logo) && file_exists(FCPATH . 'uploads/logo/' . $logo)) {
            return base_url('uploads/logo/' . $logo);
        }
        
        return null; 
    }
}

if (!function_exists('is_premium')) {
    /**
     * Memeriksa apakah lisensi premium aktif.
     *
     * Validasi menggunakan SHA-256 + salt (obfuskasi di source code).
     * Jika kode lisensi kosong → return false (user gratis, tidak error).
     * Jika kode lisensi diisi tapi salah → return false.
     * Hanya return true jika hash(input + salt) cocok dengan hash referensi.
     */
    function is_premium(): bool
    {
        $lisensi = get_pengaturan('kode_lisensi');

        // Jika kosong → user gratis, tidak ada masalah, app tetap jalan
        if (empty(trim((string)$lisensi))) {
            return false;
        }

        // Salt diobfuskasi — bukan plaintext, bukan dari .env
        $s = "\x4b\x30\x70\x33\x72\x34\x73\x31\x5f\x53\x34\x4c\x54\x5f" .
             "\x73\x33\x63\x72\x33\x74\x5f\x32\x30\x32\x36\x5f\x21\x78" .
             "\x51\x7a\x23\x39\x6d\x50\x77";

        // Hash referensi (hasil sha256 dari key premium + salt)
        $ref = "\x35\x35\x63\x33\x30\x33\x31\x35\x30\x37\x39\x66\x33\x35\x36" .
               "\x30\x31\x39\x61\x37\x37\x35\x65\x61\x62\x64\x34\x61\x31\x32" .
               "\x30\x63\x39\x35\x62\x66\x63\x62\x64\x31\x38\x36\x34\x61\x36" .
               "\x62\x36\x64\x66\x63\x65\x39\x39\x30\x66\x33\x30\x61\x35\x34" .
               "\x34\x66\x38\x30";

        // Bandingkan secara timing-safe
        return hash_equals($ref, hash('sha256', trim($lisensi) . $s));
    }
}
