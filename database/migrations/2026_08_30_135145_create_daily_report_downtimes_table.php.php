<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAHKAN BARIS INI (Penangkal Error Tabel Nyangkut)
        Schema::dropIfExists('daily_report_downtimes');

        // 2. BARU BUAT TABELNYA
        Schema::create('daily_report_downtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignId('master_downtime_id')->constrained('master_downtimes')->onDelete('restrict');
            $table->integer('durasi_menit');
            $table->text('keterangan')->nullable(); // Operator bisa nambahin catatan khusus
            $table->timestamps();
        });
    }
};
