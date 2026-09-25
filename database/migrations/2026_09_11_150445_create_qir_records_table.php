<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qir_records', function (Blueprint $table) {
            $table->id();
            
            // 1. IDENTITAS DOKUMEN
            $table->date('tanggal');
            $table->string('no_batch', 50);
            $table->string('no_mm', 50);
            $table->string('shift', 20);
            $table->string('line_produksi', 50);
            
            // 2. SNAPSHOT MASTER STANDART (Menyimpan standar saat inspeksi dilakukan)
            // Weight (gr)
            $table->decimal('std_weight_min', 8, 2)->nullable();
            $table->decimal('std_weight_max', 8, 2)->nullable();
            
            // Height (mm)
            $table->decimal('std_height_min', 8, 2)->nullable();
            $table->decimal('std_height_max', 8, 2)->nullable();
            
            // LOF (KgF)
            $table->decimal('std_lof_min', 8, 2)->nullable();
            $table->decimal('std_lof_max', 8, 2)->nullable();
            
            // Separation Force (KgF) -> di form hanya ada Std: min 3.5
            $table->decimal('std_separation_force_min', 8, 2)->nullable();
            
            // Standar Visual / Text
            $table->string('std_air_tight', 100)->nullable(); // contoh: '0.5 bar (5 menit)'
            $table->string('std_tape_test', 100)->nullable(); // contoh: '3M 616'
            $table->string('std_unzip', 100)->nullable();     // contoh: 'No Clean Peel'
            $table->string('std_pinch', 100)->nullable();     // contoh: 'No Cracking'

            // Informasi Tambahan
            $table->string('status', 20)->default('Draft'); // Draft / Approved / Void
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qir_records');
    }
};