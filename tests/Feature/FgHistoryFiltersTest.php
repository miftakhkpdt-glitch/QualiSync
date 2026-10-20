<?php

namespace Tests\Feature;

use App\Http\Controllers\Quality\InprosesFgController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FgHistoryFiltersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('fg_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_batch');
            $table->string('no_mm');
            $table->string('item_name')->nullable();
            $table->string('shift');
            $table->string('line_produksi');
            $table->string('inspection_type');
            $table->timestamps();
        });

        Schema::create('master_materials', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->string('nama_material');
        });

        DB::table('master_materials')->insert([
            ['no_mm' => 'MM-001', 'nama_material' => 'Bright Tube'],
            ['no_mm' => 'MM-002', 'nama_material' => 'Shield Tube'],
        ]);

        foreach ([
            ['2026-10-19', 'BATCH-001', 'MM-001', 'Shift 1', 'Line 2', 'Normal Inspection'],
            ['2026-10-20', 'BATCH-002', 'MM-002', 'Shift 2', 'Line 3', 'Tightened Inspection'],
        ] as [$date, $batch, $material, $shift, $line, $inspectionType]) {
            DB::table('fg_inspections')->insert([
                'tanggal' => $date,
                'no_batch' => $batch,
                'no_mm' => $material,
                'item_name' => 'Customer',
                'shift' => $shift,
                'line_produksi' => $line,
                'inspection_type' => $inspectionType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_column_filters_can_be_combined(): void
    {
        $view = app(InprosesFgController::class)->riwayat(Request::create('/in-proses/fg/riwayat', 'GET', [
            'tanggal_dari' => '2026-10-20',
            'tanggal_sampai' => '2026-10-20',
            'no_batch' => '002',
            'no_mm' => 'MM-002',
            'nama_item' => 'Shield',
            'shift' => 'Shift 2',
            'line_produksi' => 'Line 3',
            'inspection_type' => 'Tightened Inspection',
        ]));

        $rows = $view->getData()['riwayat'];

        $this->assertCount(1, $rows);
        $this->assertSame('BATCH-002', $rows->first()->no_batch);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('master_materials');
        Schema::dropIfExists('fg_inspections');

        parent::tearDown();
    }
}
