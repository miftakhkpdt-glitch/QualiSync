<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qa_coa_details', function (Blueprint $table) {
            $table->string('result_min')->nullable()->after('result_avg');
            $table->string('result_max')->nullable()->after('result_min');
        });
    }

    public function down(): void
    {
        Schema::table('qa_coa_details', function (Blueprint $table) {
            $table->dropColumn(['result_min', 'result_max']);
        });
    }
};
