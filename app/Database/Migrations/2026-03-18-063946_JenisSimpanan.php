<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class JenisSimpanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_simpanan'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'minimal_setoran' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_simpanan', true);
    }

    public function down()
    {
        $this->forge->dropTable('jenis_simpanan', true);
    }
}
