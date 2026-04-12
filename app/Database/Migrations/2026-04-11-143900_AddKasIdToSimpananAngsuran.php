<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKasIdToSimpananAngsuran extends Migration
{
    public function up()
    {
        // No-op: kas_id sudah masuk ke Simpanan.php dan Angsuran.php (migration awal).
        // File ini dipertahankan agar urutan batch migration tetap konsisten.
    }

    public function down()
    {
        // No-op
    }
}
