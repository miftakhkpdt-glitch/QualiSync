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
        Schema::table('qir_records', function (Blueprint $table) {
            $table->string('inspector_name')->nullable()->after('line_produksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qir_records', function (Blueprint $table) {
            $table->dropColumn('inspector_name');
        });
    }
};
