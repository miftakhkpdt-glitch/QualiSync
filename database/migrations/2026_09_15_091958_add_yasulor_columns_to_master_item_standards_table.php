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
        Schema::table('master_item_standards', function (Blueprint $table) {
            // Kolom baru diletakkan setelah kolom 'standar_teks' agar rapi
            $table->string('control_method')->nullable()->after('standar_teks');
            $table->string('insp_level')->nullable()->after('control_method');
            $table->string('aql')->nullable()->after('insp_level');
            $table->string('freq')->nullable()->after('aql');
            $table->string('n')->nullable()->after('freq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_item_standards', function (Blueprint $table) {
            // Jika ada masalah, kolom ini akan dihapus
            $table->dropColumn(['control_method', 'insp_level', 'aql', 'freq', 'n']);
        });
    }
};