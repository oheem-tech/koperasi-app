<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanModel extends Model
{
    protected $table            = 'pinjaman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['anggota_id', 'jenis_pinjaman', 'tanggal_pengajuan', 'jumlah_pinjaman', 'lama_tenor', 'bunga_persen', 'tanggal_jatuh_tempo', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
