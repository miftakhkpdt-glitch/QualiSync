<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capa_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('no_capa')->unique();
            $table->string('tema_masalah');
            $table->string('nama_supplier');
            $table->date('tanggal_temuan');
            $table->text('deskripsi_masalah');
            $table->string('foto_masalah')->nullable();
            
            // --- Kolom Feedback Step-by-Step dari Supplier ---
            // Langkah 3: Tindakan Penahanan (Containment Actions)
            $table->text('containment_action')->nullable();
            
            // Langkah 4 & 5: Analisa Akar Masalah & Why-Why Analysis
            $table->string('root_cause_category')->nullable(); // Man, Machine, Method, Material, dll
            $table->text('why_1')->nullable();
            $table->text('why_2')->nullable();
            $table->text('why_3')->nullable();
            $table->text('why_4')->nullable();
            $table->text('why_5')->nullable();
            
            // Langkah 7: Kesimpulan Akar Masalah
            $table->text('root_cause')->nullable();
            
            // Langkah 8: Tindakan Perbaikan & Pencegahan
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            
            // Langkah 9 & 10: Implementasi, PIC, & Tanggal
            $table->string('pic_supplier')->nullable();
            $table->date('tanggal_implementasi')->nullable();
            
            // Metadata & Status
            $table->date('tanggal_submit_supplier')->nullable();
            $table->enum('status', ['Open', 'Waiting Review', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capa_suppliers');
    }
};