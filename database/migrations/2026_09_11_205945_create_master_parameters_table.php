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
    Schema::create('master_parameters', function (Blueprint $table) {
        $table->id();
        $table->string('nama_parameter'); // cth: Weight, Height, Air Tight
        $table->string('satuan')->nullable(); // cth: gr, mm, bar, KgF
        $table->enum('tipe_input', ['Angka', 'Teks/Visual']); // Untuk membedakan input number atau dropdown OK/NG
        $table->timestamps();
    });
}

};
