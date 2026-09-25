<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_batch');
            $table->string('no_mm');
            $table->string('shift');
            $table->string('line');
            $table->string('inspection_type')->nullable();
            $table->string('pic')->nullable();
            $table->string('no_box')->nullable();
            $table->string('defect')->nullable();
            $table->integer('critical')->default(0);
            $table->integer('major')->default(0);
            $table->integer('minor')->default(0);
            $table->string('decision')->nullable();
            $table->string('jml_box')->nullable();
            $table->string('sortir_ok')->nullable();
            $table->string('sortir_ng')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finished_goods');
    }
};