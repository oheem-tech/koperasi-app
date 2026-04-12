<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersRoleToVarchar extends Migration
{
    public function up()
    {
        // No-op: Kolom 'role' sudah VARCHAR(50) sejak migration awal (Users.php).
        // File ini dipertahankan agar urutan batch migration tetap konsisten.
    }

    public function down()
    {
        // No-op
    }
}
