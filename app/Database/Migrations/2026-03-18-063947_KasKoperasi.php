<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KasKoperasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tanggal'     => ['type' => 'DATE'],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'jenis'       => ['type' => 'ENUM', 'constraint' => ['masuk', 'keluar']],
            'nominal'     => ['type' => 'INT', 'constraint' => 11],
            'saldo_akhir' => ['type' => 'INT', 'constraint' => 11],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kas_koperasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('kas_koperasi', true);
    }
}
