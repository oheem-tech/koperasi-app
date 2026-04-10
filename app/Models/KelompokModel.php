<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokModel extends Model
{
    protected $table            = 'master_kelompok';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kelompok', 'keterangan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_kelompok' => 'required|min_length[2]|is_unique[master_kelompok.nama_kelompok,id,{id}]'
    ];
    protected $validationMessages   = [
        'nama_kelompok' => [
            'is_unique' => 'Nama Kelompok ini sudah ada. Silakan gunakan nama lain.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
