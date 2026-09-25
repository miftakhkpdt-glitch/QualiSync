<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_performances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id'); // Relasi ke master_vendors
            $table->string('periode_bulan', 20); // Contoh: "Juli", "Agustus"
            $table->integer('periode_tahun');

            // --- INPUT KUANTITATIF (Bentuk Angka % dari QC) ---
            $table->float('q_item_diterima')->default(0);
            $table->float('q_scar')->default(0);
            $table->float('q_qty')->default(0);
            $table->float('q_jenis')->default(0);
            $table->float('q_coa')->default(0);
            $table->float('q_packaging')->default(0);
            $table->float('q_pengiriman')->default(0);

            // --- INPUT KUALITATIF (Bentuk Pilihan A/B/C/D dari QC) ---
            $table->enum('k_manajemen', ['A', 'B', 'C', 'D']);
            $table->enum('k_kompetensi', ['A', 'B', 'C', 'D']);
            $table->enum('k_efek_internal', ['A', 'B', 'C', 'D']);
            $table->enum('k_masalah_kritis', ['A', 'B', 'C', 'D']);

            // --- HASIL KALKULASI OTOMATIS SISTEM ---
            $table->float('skor_kuantitatif')->default(0);
            $table->string('status_kuantitatif', 50)->nullable();
            
            $table->float('skor_kualitatif')->default(0);
            $table->string('status_kualitatif', 50)->nullable();

            $table->unsignedBigInteger('user_id'); // Siapa yang menilai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_performances');
    }
};