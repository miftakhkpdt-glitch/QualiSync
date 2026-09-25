<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qir_details', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key ke tabel Header (Cascade: jika header dihapus, detail ikut terhapus)
            $table->foreignId('qir_id')->constrained('qir_records')->onDelete('cascade');
            
            // Nomor Urut Sampel (1, 2, 3, 4, 5, dst)
            $table->integer('sample_no');
            
            // HASIL PENGUKURAN AKTUAL (Variabel & Atribut)
            $table->decimal('actual_weight', 8, 2)->nullable();
            $table->decimal('actual_height', 8, 2)->nullable();
            $table->decimal('actual_lof', 8, 2)->nullable();
            $table->decimal('actual_separation_force', 8, 2)->nullable();
            
            // Hasil Cek Visual (Biasanya diisi "OK" atau "NG")
            $table->string('actual_air_tight', 20)->nullable();
            $table->string('actual_tape_test', 20)->nullable();
            $table->string('actual_unzip', 20)->nullable();
            $table->string('actual_pinch', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qir_details');
    }
};