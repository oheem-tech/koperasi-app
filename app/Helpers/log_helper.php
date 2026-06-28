<?php

if (!function_exists('catat_log')) {
    /**
     * Catat aktivitas ke tabel log_aktivitas
     * 
     * @param string $aktivitas Nama aktivitas (contoh: 'Login', 'Tambah Anggota')
     * @param string $keterangan Rincian aktivitas
     */
    function catat_log($aktivitas, $keterangan = '')
    {
        $logModel = new \App\Models\LogAktivitasModel();
        
        $request = \Config\Services::request();
        $ip_address = $request->getIPAddress();
        
        $user_id = session()->get('user_id') ?? 0;

        $logModel->insert([
            'user_id'    => $user_id,
            'aktivitas'  => $aktivitas,
            'keterangan' => $keterangan,
            'ip_address' => $ip_address,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
