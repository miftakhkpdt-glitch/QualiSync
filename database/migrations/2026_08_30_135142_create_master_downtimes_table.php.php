<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAHKAN BARIS INI (Penangkal Error)
        Schema::dropIfExists('master_downtimes');

        // 2. BARU BUAT TABELNYA
        Schema::create('master_downtimes', function (Blueprint $table) {
            $table->id();
            $table->string('mesin'); 
            $table->string('kategori'); // Misal: Mesin, Manusia, Material, Eksternal
            $table->string('nama_masalah'); // Misal: Loading Issue, Ganti Roll, dll
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }
};
