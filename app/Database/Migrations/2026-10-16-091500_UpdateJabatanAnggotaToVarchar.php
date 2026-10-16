<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateJabatanAnggotaToVarchar extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('anggota', [
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'anggota',
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('anggota', [
            'jabatan' => [
                'type'       => 'ENUM',
                'constraint' => ['anggota', 'pengurus', 'pengawas', 'pembina'],
                'default'    => 'anggota',
                'null'       => true,
            ]
        ]);
    }
}
