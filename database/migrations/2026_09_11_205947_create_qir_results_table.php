<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qir_results', function (Blueprint $table) {
            $table->id();
            
            // Kita gunakan unsignedBigInteger biasa tanpa constraint strict
            // untuk menghindari bentrok tipe data dengan tabel lama Anda
            $table->unsignedBigInteger('qir_detail_id');
            $table->unsignedBigInteger('parameter_id');
            
            $table->string('hasil_aktual')->nullable();
            $table->enum('status', ['OK', 'NG']);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qir_results');
    }
};