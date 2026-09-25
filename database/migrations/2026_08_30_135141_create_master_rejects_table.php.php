<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAHKAN BARIS INI (Penangkal Error Tabel Nyangkut)
        Schema::dropIfExists('master_rejects');

        // 2. BARU BUAT TABELNYA
        Schema::create('master_rejects', function (Blueprint $table) {
            $table->id();
            $table->string('mesin'); // Misal: AISA, COMBITOOL, PRINTING
            $table->string('kategori'); // Misal: Reject Process, Reject Printing
            $table->string('nama_reject'); // Misal: Bocor, Sealing Miring, dll
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });
    }
};
