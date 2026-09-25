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
        Schema::create('master_customers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer')->nullable()->unique();
            $table->string('nama_customer');
            
            // Kolom Setup MRP yang Fleksibel
            $table->string('tipe_kalkulasi_mrp')->default('Min-Max'); // Contoh isi: Min-Max, Coverage, dll
            
            // Parameter Khusus untuk Tipe Coverage
            $table->integer('batas_coverage_bulan')->nullable(); // Contoh: 2 (untuk 2 bulan)
            $table->decimal('yield_pweb', 5, 2)->default(100); // Contoh: 85 (untuk 85%). Default 100% jika tidak ada waste
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_customers');
    }
};
