<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_fg_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->integer('shift');
            $table->string('operator')->nullable();
            $table->integer('qty_good');
            $table->integer('qty_reject');
            $table->string('jenis_reject')->nullable();
            $table->enum('status_laporan', ['Parsial', 'Closing']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_fg_logs');
    }
};