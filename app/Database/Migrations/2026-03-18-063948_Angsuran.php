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
            // Kolom jumlah_pokok & jumlah_jasa sudah include langsung (mencegah addColumn error di hosting)
            'jumlah_pokok'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_jasa'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'denda'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'cicilan_ke'    => ['type' => 'INT', 'constraint' => 11],
            // kas_id untuk referensi ke kas_koperasi
            'kas_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
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
