<?php

namespace Tests\Feature;

use App\Http\Controllers\Quality\CpkController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CpkControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('master_materials', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->string('nama_material');
            $table->string('kategori');
        });

        Schema::create('master_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_input');
        });

        Schema::create('qir_records', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->date('tanggal');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('qir_records');
        Schema::dropIfExists('master_parameters');
        Schema::dropIfExists('master_materials');

        parent::tearDown();
    }

    public function test_frequent_items_are_ranked_by_qir_count_in_the_selected_date_range(): void
    {
        for ($index = 1; $index <= 6; $index++) {
            DB::table('master_materials')->insert([
                'no_mm' => "FG-{$index}",
                'nama_material' => "Produk {$index}",
                'kategori' => 'Finish Good',
            ]);

            for ($record = 0; $record < 7 - $index; $record++) {
                DB::table('qir_records')->insert([
                    'no_mm' => "FG-{$index}",
                    'tanggal' => '2026-10-15',
                ]);
            }
        }

        DB::table('master_materials')->insert([
            'no_mm' => 'RM-001',
            'nama_material' => 'Raw Material',
            'kategori' => 'Raw Material',
        ]);
        DB::table('qir_records')->insert([
            'no_mm' => 'RM-001',
            'tanggal' => '2026-10-15',
        ]);
        DB::table('qir_records')->insert([
            'no_mm' => 'FG-6',
            'tanggal' => '2026-10-20',
        ]);

        $response = app(CpkController::class)->index(Request::create('/in-proses/fg/cpk', 'GET', [
            'start_date' => '2026-10-10',
            'end_date' => '2026-10-19',
        ]));

        $frequentItems = $response->getData()['frequentItems'];

        $this->assertSame(['FG-1', 'FG-2', 'FG-3', 'FG-4', 'FG-5'], $frequentItems->pluck('no_mm')->all());
        $this->assertSame([6, 5, 4, 3, 2], $frequentItems->pluck('total_qir')->all());
    }
}
