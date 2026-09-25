<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produksi_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produksi_stocks');
    }
};