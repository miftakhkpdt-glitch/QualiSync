<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TAMBAHKAN BARIS INI (Penangkal Error Tabel Nyangkut)
        Schema::dropIfExists('daily_report_rejects');

        // 2. BARU BUAT TABELNYA
        Schema::create('daily_report_rejects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignId('master_reject_id')->constrained('master_rejects')->onDelete('restrict');
            $table->integer('qty');
            $table->timestamps();
        });
    }
};
