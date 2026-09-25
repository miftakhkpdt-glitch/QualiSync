<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('master_aqls', function (Blueprint $table) {
            // Hapus kolom lot & sample jika sebelumnya sudah dibuat
            if (Schema::hasColumn('master_aqls', 'lot_size_min')) {
                $table->dropColumn(['lot_size_min', 'lot_size_max', 'sample_size']);
            }
        });
    }

    public function down(): void {
        Schema::table('master_aqls', function (Blueprint $table) {
            $table->integer('lot_size_min')->nullable();
            $table->integer('lot_size_max')->nullable();
            $table->integer('sample_size')->nullable();
        });
    }
};