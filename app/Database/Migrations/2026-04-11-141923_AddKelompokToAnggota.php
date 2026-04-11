<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelompokToAnggota extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (!$db->fieldExists('kelompok', 'anggota')) {
            $this->forge->addColumn('anggota', [
                'kelompok' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'default'    => 'Umum',
                    'after'      => 'jabatan',
                ]
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'kelompok');
    }
}
