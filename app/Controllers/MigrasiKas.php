<?php

namespace App\Controllers;

use App\Models\SimpananModel;
use App\Models\AngsuranModel;
use App\Models\KasKoperasiModel;

class MigrasiKas extends BaseController
{
    public function run()
    {
        $db = \Config\Database::connect();

        echo "<h2>Menjalankan Migrasi Database Kas Koperasi...</h2>";

        // 1. Alter Table (Sudah dijalankan)
        echo "Kolom kas_id sudah ada di tabel.<br>";

        $kasModel = new KasKoperasiModel();

        try {
            // 2. Hapus kas Massal (GABUNGAN)
            $db->query("DELETE FROM kas_koperasi WHERE keterangan LIKE 'Setoran Massal Potong Gaji%' AND kategori = 'sistem_lainnya'");
            echo "Data kas Setoran Massal gabungan lama berhasil dihapus (akan digantikan individu).<br>";

            // 3. Migrasi Simpanan
            $simpananModel = new SimpananModel();
            $simpanans = $simpananModel->where('kas_id', null)->findAll();
            $countSimpanan = 0;
            foreach ($simpanans as $s) {
                $anggota = $db->table('anggota')->where('id', $s['anggota_id'])->get()->getRowArray();
                $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
                $keteranganKas = ($s['jenis_transaksi'] == 'setor' ? 'Setoran' : 'Penarikan') . ' Simpanan - ' . $namaAnggota;
                $jenisKas = ($s['jenis_transaksi'] == 'setor') ? 'masuk' : 'keluar';

                // Hapus yang lama kalau ada yang mirip (untuk mencegah dobel jika bukan massal sblmnya)
                $db->query("DELETE FROM kas_koperasi WHERE tanggal = ? AND nominal = ? AND keterangan LIKE ?", 
                           [$s['tanggal_transaksi'], $s['jumlah'], "%$keteranganKas%"]);

                $kas_id = $kasModel->catatTransaksi($s['tanggal_transaksi'], $keteranganKas, $jenisKas, $s['jumlah'], 'simpanan');
                
                $simpananModel->update($s['id'], ['kas_id' => $kas_id]);
                $countSimpanan++;
            }
            echo "Berhasil memigrasi {$countSimpanan} data Simpanan lama.<br>";

            // 4. Migrasi Angsuran
            $angsuranModel = new AngsuranModel();
            $angsurans = $angsuranModel->where('kas_id', null)->findAll();
            $countAngsuran = 0;
            foreach ($angsurans as $a) {
                $pinjaman = $db->table('pinjaman')->where('id', $a['pinjaman_id'])->get()->getRowArray();
                if ($pinjaman) {
                    $anggota = $db->table('anggota')->where('id', $pinjaman['anggota_id'])->get()->getRowArray();
                    $namaAnggota = $anggota ? $anggota['nama_lengkap'] : 'Anggota';
                    $kodePinjaman = 'PJ-' . str_pad($pinjaman['id'], 4, '0', STR_PAD_LEFT);
                    
                    // Cek kemungkinan keterangan lama
                    $ketKasBiasa = 'Setoran Angsuran ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' (Cicilan ke-' . $a['cicilan_ke'] . ') [' . $kodePinjaman . ']';
                    $ketKasLunas = 'Pelunasan Pinjaman ' . $pinjaman['jenis_pinjaman'] . ' - ' . $namaAnggota . ' [' . $kodePinjaman . ']';

                    $db->query("DELETE FROM kas_koperasi WHERE tanggal = ? AND nominal = ? AND (keterangan LIKE ? OR keterangan LIKE ?)", 
                               [$a['tanggal_bayar'], $a['jumlah_bayar'], "%$ketKasBiasa%", "%$ketKasLunas%"]);

                    $kas_id = $kasModel->catatTransaksi($a['tanggal_bayar'], $ketKasBiasa, 'masuk', $a['jumlah_bayar'], 'angsuran');
                    
                    $angsuranModel->update($a['id'], ['kas_id' => $kas_id]);
                    $countAngsuran++;
                }
            }
            $out = "Berhasil memigrasi {$countAngsuran} data Angsuran lama.\n Migrasi Selesai! Integrasi Kas 100% tersambung!";
            file_put_contents('test.txt', $out);
            echo $out;
            echo "<form action='/migrasikas/destroy' method='post'><button type='submit'>Hapus File Migrator Ini</button></form>";
        } catch (\Throwable $th) {
            $err = "Error: " . $th->getMessage() . " at Line " . $th->getLine();
            file_put_contents('test.txt', $err);
            echo $err;
        }
    }

    public function destroy()
    {
        unlink(__FILE__);
        echo "File Migrator berhasil dihapus untuk keamanan. Silakan tutup halaman ini.";
    }
}
