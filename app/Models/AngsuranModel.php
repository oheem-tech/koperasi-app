<?php

namespace App\Models;

use CodeIgniter\Model;

class AngsuranModel extends Model
{
    protected $table            = 'angsuran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pinjaman_id', 'tanggal_bayar', 'jumlah_bayar', 'jumlah_pokok', 'jumlah_jasa', 'denda', 'cicilan_ke', 'kas_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
