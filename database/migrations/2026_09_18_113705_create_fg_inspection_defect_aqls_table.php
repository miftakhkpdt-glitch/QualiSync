<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFgInspectionDefectAqlsTable extends Migration
{
    public function up()
    {
        Schema::create('fg_inspection_defect_aqls', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel detail temuan/defect (Baris per defect)
            // Asumsi tabel detail Anda bernama 'fg_inspection_details'
            $table->foreignId('fg_inspection_detail_id')->constrained('fg_inspection_details')->onDelete('cascade');
            
            // Menyimpan nama kategori (Snapshot). Contoh: 'Critical', 'Amber', 'Zero Defect'
            $table->string('kategori_standar', 50); 
            
            // Menyimpan angka jumlah temuan di box tersebut
            $table->integer('jumlah_temuan')->default(0); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fg_inspection_defect_aqls');
    }
}