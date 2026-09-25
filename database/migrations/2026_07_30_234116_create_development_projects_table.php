<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_projects', function (Blueprint $table) {
            $table->id();
            $table->string('judul_riset');
            $table->string('kode_material'); // Contoh: EG25, dll
            $table->float('target_suhu')->nullable(); // Contoh: 150 derajad celcius
            $table->enum('status', ['Planning', 'On Progress', 'Evaluation', 'Completed'])->default('Planning');
            $table->text('keterangan')->nullable();
            $table->string('dibuat_oleh');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_projects');
    }
};