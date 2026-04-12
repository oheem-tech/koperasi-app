<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPokokJasaToAngsuran extends Migration
{
    public function up()
    {
        // No-op: Kolom jumlah_pokok & jumlah_jasa sudah masuk ke Angsuran.php (migration awal).
        // File ini dipertahankan agar urutan batch migration tetap konsisten.
    }

    public function down()
    {
        // No-op
    }
}
