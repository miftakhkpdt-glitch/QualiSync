<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('master_customers', function (Blueprint $table) {
            // Menambahkan kolom alamat (tipe text agar bisa panjang) setelah kolom nama_customer
            $table->text('alamat')->nullable()->after('nama_customer');
        });
    }

    public function down()
    {
        Schema::table('master_customers', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });
    }
};