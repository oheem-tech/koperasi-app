<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'permissions'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Helper list of all available permissions in the system
     */
    public static function getAvailablePermissions()
    {
        return [
            'manage_anggota'    => 'Kelola Data Anggota',
            'manage_simpanan'   => 'Kelola Simpanan Anggota',
            'manage_pinjaman'   => 'Pengajuan & Verifikasi Pinjaman',
            'manage_angsuran'   => 'Pembayaran Angsuran',
            'manage_kas'        => 'Buku Kas Umum (Manajemen Uang Masuk/Keluar)',
            'view_laporan'      => 'Akses & Cetak Laporan Keuangan (SHU, Neraca, Arus Kas)',
            'manage_pengaturan' => 'Pengaturan Global (Bunga, Tenor, Koperasi)',
            'manage_backup'     => 'Backup & Restore Database',
            'manage_roles'      => 'Hak Akses & Role Management (Super Admin)'
        ];
    }
}
