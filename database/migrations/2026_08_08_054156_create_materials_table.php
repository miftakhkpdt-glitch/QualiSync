<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_material')->unique(); // Kode unik bahan baku
            $table->string('nama_material');          // Nama bahan baku (Contoh: Besi Plat, Biji Plastik, dll)
            $table->integer('stok');                  // Jumlah stok fisik saat ini di gudang
            $table->string('satuan');                 // Kg, Pcs, Lembar, dll
            $table->integer('minimum_stok')->default(10); // Batas aman stok untuk peringatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};