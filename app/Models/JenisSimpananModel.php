<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisSimpananModel extends Model
{
    protected $table            = 'jenis_simpanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_simpanan', 'minimal_setoran'];
    protected $useTimestamps    = false;
}
