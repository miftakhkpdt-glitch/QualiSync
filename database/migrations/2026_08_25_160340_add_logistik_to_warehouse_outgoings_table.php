<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warehouse_outgoings', function (Blueprint $table) {
            // Menambahkan kolom logistik (semuanya dibuat nullable agar boleh dikosongkan jika belum ada data)
            $table->string('no_shipment', 50)->nullable()->after('approved_by');
            $table->string('fwd_agent', 100)->nullable()->after('no_shipment');
            $table->string('no_polisi', 20)->nullable()->after('fwd_agent');
            $table->string('nama_supir', 100)->nullable()->after('no_polisi');
            $table->text('keterangan')->nullable()->after('nama_supir');
        });
    }

    public function down()
    {
        Schema::table('warehouse_outgoings', function (Blueprint $table) {
            $table->dropColumn(['no_shipment', 'fwd_agent', 'no_polisi', 'nama_supir', 'keterangan']);
        });
    }
};