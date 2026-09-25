<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warehouse_outgoings', function (Blueprint $table) {
            // Menambahkan kolom status dengan default 'Pending'
            $table->string('status', 50)->default('Pending')->after('qty_kirim');
            
            // Menambahkan kolom untuk mencatat siapa admin yang melakukan approval
            $table->string('approved_by')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('warehouse_outgoings', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn(['status', 'approved_by']);
        });
    }
};