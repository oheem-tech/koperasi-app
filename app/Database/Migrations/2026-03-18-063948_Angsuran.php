<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Angsuran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pinjaman_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_bayar' => ['type' => 'DATE'],
            'jumlah_bayar'  => ['type' => 'INT', 'constraint' => 11],
            'denda'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'cicilan_ke'    => ['type' => 'INT', 'constraint' => 11],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pinjaman_id', 'pinjaman', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('angsuran', true);
    }

    public function down()
    {
        $this->forge->dropTable('angsuran', true);
    }
}
