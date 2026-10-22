<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_materials', function (Blueprint $table) {
            $table->string('item_code_customer')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('master_materials', function (Blueprint $table) {
            $table->dropColumn('item_code_customer');
        });
    }
};
