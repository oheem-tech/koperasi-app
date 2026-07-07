<?php

namespace App\Controllers;

class Diag extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $kasKeluar = $db->table('kas_koperasi')
            ->where('jenis', 'keluar')
            ->where('YEAR(tanggal)', 2026)
            ->where('MONTH(tanggal) <=', 6)
            ->get()->getResultArray();
            
        $simpananKeluar = $db->table('simpanan')
            ->where('jenis_transaksi', 'tarik')
            ->where('YEAR(tanggal_transaksi)', 2026)
            ->where('MONTH(tanggal_transaksi) <=', 6)
            ->get()->getResultArray();
            
        $pinjamanCair = $db->table('pinjaman')
            ->whereIn('status', ['disetujui', 'lunas'])
            ->where('YEAR(tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH)', 2026)
            ->where('MONTH(tanggal_jatuh_tempo - INTERVAL lama_tenor MONTH) <=', 6)
            ->get()->getResultArray();

        $manualKeluar = $db->table('kas_koperasi')
            ->where('jenis', 'keluar')
            ->whereNotIn('kategori', ['simpanan', 'angsuran', 'pinjaman'])
            ->where('YEAR(tanggal)', 2026)
            ->where('MONTH(tanggal) <=', 6)
            ->get()->getResultArray();

        $str = "<pre>";
        $str .= "Total Kas Keluar (KasKoperasi): " . array_sum(array_column($kasKeluar, 'nominal')) . "\n";
        $str .= "Total Simpanan Keluar: " . array_sum(array_column($simpananKeluar, 'jumlah')) . "\n";
        $str .= "Total Pinjaman Cair: " . array_sum(array_column($pinjamanCair, 'jumlah_pinjaman')) . "\n";
        $str .= "Total Manual Keluar: " . array_sum(array_column($manualKeluar, 'nominal')) . "\n";
        $str .= "Total Arus Kas Keluar: " . (array_sum(array_column($simpananKeluar, 'jumlah')) + array_sum(array_column($pinjamanCair, 'jumlah_pinjaman')) + array_sum(array_column($manualKeluar, 'nominal'))) . "\n\n";

        $str .= "Missing from Arus Kas:\n";
        foreach ($kasKeluar as $k) {
            if ($k['kategori'] == 'simpanan') {
                $found = false;
                foreach ($simpananKeluar as $s) {
                    if ($s['jumlah'] == $k['nominal'] && $s['tanggal_transaksi'] == $k['tanggal']) $found = true;
                }
                if (!$found) $str .= "Missing Simpanan: " . print_r($k, true) . "\n";
            } else if ($k['kategori'] == 'pinjaman') {
                $found = false;
                foreach ($pinjamanCair as $p) {
                    if ($p['jumlah_pinjaman'] == $k['nominal']) $found = true;
                }
                if (!$found) $str .= "Missing Pinjaman: " . print_r($k, true) . "\n";
            }
        }
        
        $str .= "\nPinjaman Cair List:\n";
        foreach ($pinjamanCair as $p) {
            $str .= $p['id'] . " | " . $p['jumlah_pinjaman'] . " | Tenor: " . $p['lama_tenor'] . " | Jatuh Tempo: " . $p['tanggal_jatuh_tempo'] . "\n";
        }
        
        return $str;
    }
}
