<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersRoleToVarchar extends Migration
{
    public function up()
    {
        // Change the 'role' column from ENUM('admin','anggota') to VARCHAR(50)
        // to support dynamic RBAC roles
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'anggota',
                'null'       => false,
            ],
        ];
        
        $this->forge->modifyColumn('users', $fields);

        // Auto-fix any roles that became empty strings due to the previous ENUM strictness
        $db = \Config\Database::connect();
        $db->table('users')->where('role', '')->update(['role' => 'anggota']); // Safe default
        
        // specifically for the 'bendahara' user that was just created:
        $db->table('users')->where('username', 'bendahara')->update(['role' => 'bendahara']);
    }

    public function down()
    {
        // Revert back if needed
        $fields = [
            'role' => [
                'name'       => 'role',
                'type'       => 'ENUM',
                'constraint' => ['admin', 'anggota'],
                'default'    => 'anggota',
                'null'       => false,
            ],
        ];
        
        $this->forge->modifyColumn('users', $fields);
    }
}
