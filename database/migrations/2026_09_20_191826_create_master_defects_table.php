<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_defects', function (Blueprint $table) {
            $table->id();$table->string('nama_defect'); // Contoh: "Bocor", "Penyok", "Label Miring", dll.
            $table->string('kategori')->nullable(); // Opsional: misal untuk Finish Good, Packaging, dll.
            $table->text('deskripsi')->nullable();$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_defects');
    }
};