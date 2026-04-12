<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelompokToAnggota extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        try {
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
        } catch (\Exception $e) {
            // Abaikan jika kolom sudah ada atau duplicate
        }
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'kelompok');
    }
}
