<?php

namespace App\Models;

use CodeIgniter\Model;

class KasKoperasiModel extends Model
{
    protected $table            = 'kas_koperasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tanggal', 'keterangan', 'jenis', 'nominal', 'kategori', 'saldo_akhir'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Helper method to insert a transaction and automatically compute current saldo_akhir
     */
    public function catatTransaksi($tanggal, $keterangan, $jenis, $nominal, $kategori = 'sistem_lainnya')
    {
        // Saldo akhir tidak lagi dilacak secara hard-coded di database (Dynamic Running Balance)
        $this->insert([
            'tanggal'     => $tanggal,
            'keterangan'  => $keterangan,
            'jenis'       => $jenis,
            'nominal'     => $nominal,
            'kategori'    => $kategori,
            'saldo_akhir' => 0
        ]);
        
        return $this->getInsertID();
    }
}
