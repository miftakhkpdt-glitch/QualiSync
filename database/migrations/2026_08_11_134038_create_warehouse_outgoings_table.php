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
    Schema::create('warehouse_outgoings', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal_pengiriman');
        $table->string('no_po');
        $table->string('no_mm');
        $table->string('batch_number');
        $table->integer('qty_kirim');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_outgoings');
    }
};
