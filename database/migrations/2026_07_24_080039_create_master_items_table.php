<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();
            
            // Kolom untuk identitas barang
            $table->string('no_mm')->unique();
            $table->string('item_name');
            
            // Kolom untuk standar parameter QC
            $table->string('std_netto');
            $table->string('std_tinggi');
            $table->string('std_air_tight');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};