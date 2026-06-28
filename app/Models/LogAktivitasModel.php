<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'aktivitas', 'keterangan', 'ip_address', 'created_at'];

    protected $useTimestamps = false; // We manage created_at manually or let DB handle if needed, but let's enable it so it auto-sets
    
    // Actually, setting $useTimestamps = false and handling it on insert is easier if we only need created_at.
    // But let's just let the model handle it if we rename created_at or use datetime.
    // Let's keep it false and just set date('Y-m-d H:i:s') on insert.

    public function getLogsWithUser($limit = 50, $offset = 0)
    {
        return $this->select('log_aktivitas.*, users.username, users.role')
                    ->join('users', 'users.id = log_aktivitas.user_id', 'left')
                    ->orderBy('log_aktivitas.id', 'DESC')
                    ->findAll($limit, $offset);
    }
}
