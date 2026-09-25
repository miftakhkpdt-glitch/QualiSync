<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qir_records', function (Blueprint $table) {
            $table->id();
            $table->string('no_batch');       // Menyimpan Nomor Batch
            $table->string('no_mm');          // Menyimpan Nomor MM
            $table->string('shift');          // Menyimpan info SHIFT 1, SHIFT 2, SHIFT 3
            $table->integer('nomor_urut');    // Menyimpan urutan baris ke 1 sampai 8
            
            // Kolom hasil pengukuran aktual (bisa desimal, dan boleh kosong/nullable)
            $table->decimal('netto', 8, 2)->nullable();
            $table->decimal('t_total', 8, 2)->nullable();
            $table->decimal('lof', 8, 2)->nullable();
            $table->decimal('sep_force', 8, 2)->nullable();
            
            // Kolom hasil tes visual/pilihan (OK/NG)
            $table->string('air_tight')->nullable();
            $table->string('tape_test')->nullable();
            
            // PERBAIKAN BUG: Mengganti cut_pull menjadi unzip agar sinkron dengan Controller
            $table->string('unzip')->nullable(); 
            
            $table->string('pinch')->nullable();
            
            $table->timestamps(); // Mencatat waktu data disimpan

            // ==========================================
            // OPTIMASI DATABASE (MEMASANG INDEX)
            // ==========================================
            
            // 1. Composite Index: Karena pencarian selalu menggabungkan 3 kolom ini
            $table->index(['no_batch', 'no_mm', 'shift'], 'idx_pencarian_qir');
            
            // 2. Index untuk kolom created_at karena di Controller Anda sering mengurutkan 
            //    riwayat (orderBy) dan menghapus data > 2 tahun berdasarkan tanggal ini.
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qir_records');
    }
};