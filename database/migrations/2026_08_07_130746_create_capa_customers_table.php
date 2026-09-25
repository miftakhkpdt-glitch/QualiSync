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
        Schema::create('capa_customers', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_month')->nullable();
            $table->string('customer')->nullable();
            $table->string('no_capa_customer')->nullable();
            $table->string('mm')->nullable();
            $table->string('item')->nullable();
            $table->string('defect')->nullable();
            $table->string('source')->nullable();
            $table->string('category_defect')->nullable();
            $table->string('sncr')->nullable(); // NC / NON NC
            $table->string('status')->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capa_customers');
    }
};