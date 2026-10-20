<?php

namespace Tests\Feature;

use App\Http\Controllers\Quality\CoaController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CoaMultiBatchResultsTest extends TestCase
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

        Schema::create('qir_records', function (Blueprint $table) {
            $table->id();
            $table->string('no_batch');
            $table->string('no_mm');
        });

        Schema::create('qir_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qir_id');
        });

        Schema::create('qir_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qir_detail_id');
            $table->unsignedBigInteger('parameter_id');
            $table->string('hasil_aktual')->nullable();
            $table->string('status');
        });

        Schema::create('master_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_input');
            $table->string('nama_parameter');
            $table->string('satuan')->nullable();
        });

        Schema::create('master_item_standards', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->unsignedBigInteger('parameter_id');
            $table->decimal('min_value', 8, 2)->nullable();
            $table->decimal('max_value', 8, 2)->nullable();
        });

        Schema::create('master_customers', function (Blueprint $table) {
            $table->id();
        });

        DB::table('master_parameters')->insert([
            'id' => 1,
            'tipe_input' => 'Angka',
            'nama_parameter' => 'Weight',
            'satuan' => 'gr',
        ]);
        DB::table('master_item_standards')->insert([
            'no_mm' => 'MM-001',
            'parameter_id' => 1,
            'min_value' => 1,
            'max_value' => 10,
        ]);

        foreach ([
            ['BATCH-001', 'MM-001', ['5', '7']],
            ['BATCH-002', 'MM-001', ['3', '9']],
            ['BATCH-003', 'MM-001', ['100']],
        ] as [$batch, $mm, $values]) {
            $recordId = DB::table('qir_records')->insertGetId([
                'no_batch' => $batch,
                'no_mm' => $mm,
            ]);

            foreach ($values as $value) {
                $detailId = DB::table('qir_details')->insertGetId([
                    'qir_id' => $recordId,
                ]);
                DB::table('qir_results')->insert([
                    'qir_detail_id' => $detailId,
                    'parameter_id' => 1,
                    'hasil_aktual' => $value,
                    'status' => 'OK',
                ]);
            }
        }
    }

    public function test_numeric_recommendations_include_all_selected_batches_only(): void
    {
        $view = app(CoaController::class)->formInput(Request::create('/coa/form-input', 'GET', [
            'batch' => 'BATCH-001,BATCH-002',
            'mm' => 'MM-001',
        ]));

        $recommendation = $view->getData()['rekomendasiHasil'][1];

        $this->assertSame([
            'min' => '3.00',
            'avg' => '6.00',
            'max' => '9.00',
        ], $recommendation);
        $this->assertSame('BATCH-001,BATCH-002', $view->getData()['batch']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('master_customers');
        Schema::dropIfExists('master_item_standards');
        Schema::dropIfExists('master_parameters');
        Schema::dropIfExists('qir_results');
        Schema::dropIfExists('qir_details');
        Schema::dropIfExists('qir_records');

        parent::tearDown();
    }
}
