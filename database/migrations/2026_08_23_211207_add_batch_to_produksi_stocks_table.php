<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_stocks', function (Blueprint $table) {
            // Menambahkan kolom batch setelah kolom no_mm
            $table->string('batch')->nullable()->after('no_mm');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_stocks', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
};