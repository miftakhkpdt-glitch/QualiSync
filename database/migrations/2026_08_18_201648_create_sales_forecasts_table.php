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
        Schema::create('sales_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_customer');
            
            // Info Produk
            $table->string('no_mm');
            $table->string('nama_produk');
            
            // Periode Forecast (Misal: Bulan 9, Tahun 2026)
            $table->integer('bulan');
            $table->integer('tahun');
            
            // Target Angka
            $table->decimal('qty_forecast', 15, 2);
            $table->string('satuan')->default('Pcs');
            
            // Sistem Tracking Revisi
            $table->integer('versi')->default(1); // Mulai dari versi 1
            $table->string('alasan_revisi')->nullable(); // Alasan jika ada perubahan
            $table->enum('status', ['Aktif', 'Direvisi'])->default('Aktif'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_forecasts');
    }
};
