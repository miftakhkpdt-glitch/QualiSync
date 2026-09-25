<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah kolom status di tabel sales_orders menjadi string
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });

        // Mengubah kolom status di tabel work_orders menjadi string
        Schema::table('work_orders', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });
    }

    public function down(): void
    {
        // Tidak perlu diisi jika tidak ingin dibatalkan
    }
};