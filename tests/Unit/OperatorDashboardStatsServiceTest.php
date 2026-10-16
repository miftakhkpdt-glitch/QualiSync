<?php

namespace Tests\Unit;

use App\Services\Quality\OperatorDashboardStatsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OperatorDashboardStatsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['fg_inspections', 'qir_records'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('no_batch');
                $table->string('no_mm');
                $table->string('shift');
                $table->string('line_produksi');
                $table->timestamps();
            });
        }

        Schema::create('qa_coas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('qa_coas');
        Schema::dropIfExists('qir_records');
        Schema::dropIfExists('fg_inspections');

        parent::tearDown();
    }

    public function test_counts_unique_inspections_created_today_and_coas_created_this_month(): void
    {
        $today = now()->toDateString();
        $now = now()->toDateTimeString();
        $firstInspection = [
            'tanggal' => $today,
            'no_batch' => 'BATCH-001',
            'no_mm' => 'MM-001',
            'shift' => 'Shift 1',
            'line_produksi' => 'Line 1',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('fg_inspections')->insert($firstInspection);
        DB::table('qir_records')->insert($firstInspection);
        DB::table('qir_records')->insert(array_merge($firstInspection, ['no_batch' => 'BATCH-002']));
        DB::table('fg_inspections')->insert(array_merge($firstInspection, [
            'tanggal' => now()->subDay()->toDateString(),
            'no_batch' => 'BATCH-003',
            'created_at' => now()->subDay()->toDateTimeString(),
            'updated_at' => now()->subDay()->toDateTimeString(),
        ]));

        DB::table('qa_coas')->insert([
            ['created_at' => $now, 'updated_at' => $now],
            [
                'created_at' => now()->subMonth()->toDateTimeString(),
                'updated_at' => now()->subMonth()->toDateTimeString(),
            ],
        ]);

        $counts = (new OperatorDashboardStatsService())->counts();

        $this->assertSame(['inProsesCount' => 2, 'coaCount' => 1], $counts);
    }
}
