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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pr_id');
            $table->unsignedBigInteger('supplier_id');
            $table->date('tanggal_po');
            $table->decimal('harga_satuan', 15, 2);
            $table->date('eta')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
};
