<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fg_inspections', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_output')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('fg_inspections', function (Blueprint $table) {
            $table->dropColumn('jumlah_output');
        });
    }
};
