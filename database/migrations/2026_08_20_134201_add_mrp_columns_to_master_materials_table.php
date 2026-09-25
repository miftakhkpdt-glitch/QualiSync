<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('master_materials', function (Blueprint $table) {
            // Menambahkan kolom parameter MRP
            $table->integer('rop')->default(0)->nullable();
            $table->integer('max_stock')->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('master_materials', function (Blueprint $table) {
            $table->dropColumn(['rop', 'max_stock']);
        });
    }
};