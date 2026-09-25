<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_boms', function (Blueprint $table) {
            $table->id();
            $table->string('fg_mm'); // No. MM dari Finished Goods (Parent)
            $table->string('component_mm'); // No. MM dari Komponen/Material (Child)
            $table->decimal('qty_usage', 10, 4); // Jumlah kebutuhan untuk 1 unit FG
            $table->boolean('is_optional')->default(false); // 0 = Wajib, 1 = Opsional (misal Master Batch)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_boms');
    }
};