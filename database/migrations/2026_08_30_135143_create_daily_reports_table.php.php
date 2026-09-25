<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAHKAN BARIS INI (Penangkal Error Tabel Nyangkut)
        Schema::dropIfExists('daily_reports');

        // 2. BARU BUAT TABELNYA
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('shift');
            $table->string('batch_num')->nullable();
            $table->string('line')->nullable();
            $table->string('mesin')->nullable();
            $table->integer('speed')->nullable(); // Kecepatan Mesin
            $table->string('operator');
            $table->string('packer')->nullable();
            
            // Pilar Quality
            $table->integer('output_actual')->default(0); // Barang Bagus (FG)
            $table->integer('total_reject_process')->default(0); // Akumulasi dari rincian
            $table->integer('total_reject_printing')->default(0); // Akumulasi dari rincian
            
            // Pilar Performance & Availability
            $table->integer('output_standard')->default(0); // Target output shift tsb
            $table->integer('planned_time_menit')->default(480); // Default 8 jam = 480 mnt
            $table->integer('total_downtime_menit')->default(0); // Akumulasi dari rincian

            $table->string('status_laporan')->default('Draft'); // Draft / Disetujui
            $table->timestamps();
        });
    }
};
