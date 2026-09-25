<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('master_item_standards', function (Blueprint $table) {
        $table->id();
        $table->string('no_mm'); // Relasi ke master_items
        $table->foreignId('parameter_id')->constrained('master_parameters')->onDelete('cascade');
        
        // Standar angka (Jika tipe_input = Angka)
        $table->decimal('min_value', 8, 2)->nullable();
        $table->decimal('max_value', 8, 2)->nullable();
        
        // Standar visual/teks (Jika tipe_input = Teks/Visual, cth: "OK", "3M 616")
        $table->string('standar_teks')->nullable();
        
        $table->integer('urutan')->default(0); // Untuk mengatur posisi kolom di tabel nantinya (mana yang kiri/kanan)
        $table->timestamps();
    });
}

   
};
