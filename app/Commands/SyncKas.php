<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\KasKoperasiModel;

class SyncKas extends BaseCommand
{
    protected $group       = 'Koperasi';
    protected $name        = 'kas:sync';
    protected $description = 'Sinkronisasi riwayat transaksi lama ke Buku Kas';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Bersihkan tabel kas
        $db->table('kas_koperasi')->truncate();

        $events = [];

        // 1. Simpanan
        $simpanan = $db->table('simpanan')
            ->select('simpanan.*, anggota.nama_lengkap')
            ->join('anggota', 'anggota.id = simpanan.anggota_id')
            ->get()->getResultArray();
        
        foreach ($simpanan as $s) {
            $jenisKas = ($s['jenis_transaksi'] == 'setor') ? 'masuk' : 'keluar';
            $kat      = ($s['jenis_transaksi'] == 'setor') ? 'Setoran' : 'Penarikan';
            $events[] = [
                'tanggal'    => $s['tanggal_transaksi'],
                'keterangan' => $kat . ' Simpanan - ' . $s['nama_lengkap'],
                'jenis'      => $jenisKas,
                'nominal'    => $s['jumlah'],
                'created_at' => $s['created_at']
            ];
        }

        // 2. Angsuran
        $angsuran = $db->table('angsuran')
            ->select('angsuran.*, pinjaman.jenis_pinjaman, anggota.nama_lengkap')
            ->join('pinjaman', 'pinjaman.id = angsuran.pinjaman_id')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->get()->getResultArray();

        foreach ($angsuran as $a) {
            $kodePinjaman = 'PJ-' . str_pad($a['pinjaman_id'], 4, '0', STR_PAD_LEFT);
            $events[] = [
                'tanggal'    => $a['tanggal_bayar'],
                'keterangan' => 'Setoran Angsuran ' . $a['jenis_pinjaman'] . ' - ' . $a['nama_lengkap'] . ' (Cicilan ke-' . $a['cicilan_ke'] . ') [' . $kodePinjaman . ']',
                'jenis'      => 'masuk',
                'nominal'    => $a['jumlah_bayar'],
                'created_at' => $a['created_at']
            ];
        }

        // 3. Pinjaman (Cair)
        $pinjaman = $db->table('pinjaman')
            ->select('pinjaman.*, anggota.nama_lengkap')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->whereIn('pinjaman.status', ['disetujui', 'lunas'])
            ->get()->getResultArray();
        
        foreach ($pinjaman as $p) {
            $tgl = $p['tanggal_pengajuan'] ? $p['tanggal_pengajuan'] : date('Y-m-d', strtotime($p['created_at']));
            $kodePinjaman = 'PJ-' . str_pad($p['id'], 4, '0', STR_PAD_LEFT);
            $events[] = [
                'tanggal'    => $tgl,
                'keterangan' => 'Pencairan Pinjaman - ' . $p['nama_lengkap'] . ' [' . $kodePinjaman . ']',
                'jenis'      => 'keluar',
                'nominal'    => $p['jumlah_pinjaman'],
                'created_at' => $p['created_at']
            ];
        }

        // Urutkan tanggal secara kronologis (dari awal)
        usort($events, function($a, $b) {
            if ($a['tanggal'] === $b['tanggal']) {
                return $a['created_at'] <=> $b['created_at'];
            }
            return $a['tanggal'] <=> $b['tanggal'];
        });

        $kasModel = new KasKoperasiModel();
        foreach ($events as $e) {
            $kasModel->catatTransaksi($e['tanggal'], $e['keterangan'], $e['jenis'], $e['nominal']);
            // Opsional: set ulang created_at jika diperlukan historikal update
            $last_id = $kasModel->insertID();
            $db->table('kas_koperasi')->where('id', $last_id)->update(['created_at' => $e['created_at']]);
        }

        CLI::write("Berhasil merekap " . count($events) . " transaksi historis ke Buku Kas Umum!", "green");
    }
}
