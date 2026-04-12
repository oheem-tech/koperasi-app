<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToKasKoperasi extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        try {
            if (!$db->fieldExists('kategori', 'kas_koperasi')) {
                $this->forge->addColumn('kas_koperasi', [
                    'kategori' => [
                        'type'       => 'VARCHAR',
                        'constraint' => '100',
                        'null'       => true,
                        'default'    => 'operasional',
                        'after'      => 'jenis',
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // Abaikan jika kolom sudah ada atau duplicate
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('kategori', 'kas_koperasi')) {
            $this->forge->dropColumn('kas_koperasi', 'kategori');
        }
    }
}
