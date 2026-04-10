<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJabatanToAnggota extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'jabatan' => [
                'type'       => 'ENUM',
                'constraint' => ['anggota', 'pengurus', 'pengawas', 'pembina'],
                'default'    => 'anggota',
                'after'      => 'status',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'jabatan');
    }
}
