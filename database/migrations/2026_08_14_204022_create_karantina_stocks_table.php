<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('karantina_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->decimal('qty', 12, 4)->default(0);
            $table->string('status_karantina')->default('Hold'); // Bisa Hold atau NG
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('karantina_stocks');
    }
};