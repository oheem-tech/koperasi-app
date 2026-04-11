<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKasIdToSimpananAngsuran extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add kas_id to simpanan
        if (!$db->fieldExists('kas_id', 'simpanan')) {
            $this->forge->addColumn('simpanan', [
                'kas_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'keterangan',
                ],
            ]);
        }

        // Add kas_id to angsuran
        if (!$db->fieldExists('kas_id', 'angsuran')) {
            $this->forge->addColumn('angsuran', [
                'kas_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'cicilan_ke',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('simpanan', 'kas_id');
        $this->forge->dropColumn('angsuran', 'kas_id');
    }
}
