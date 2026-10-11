<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calibration_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('identity_number')->unique();
            $table->string('item_name');
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('tool_condition')->nullable();
            $table->string('label_condition')->nullable();
            $table->string('holder')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('calibration_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('calibration_equipment')->cascadeOnDelete();
            $table->string('measurement_point')->nullable();
            $table->string('capacity')->nullable();
            $table->string('resolution')->nullable();
            $table->string('internal_lop')->nullable();
            $table->string('certificate_correction')->nullable();
            $table->string('certificate_uncertainty')->nullable();
            $table->string('te')->nullable();
            $table->string('verification_result')->nullable();
            $table->string('equipment_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calibration_verifications');
        Schema::dropIfExists('calibration_equipment');
    }
};
