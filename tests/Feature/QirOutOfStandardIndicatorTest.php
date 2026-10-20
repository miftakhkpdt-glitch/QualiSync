<?php

namespace Tests\Feature;

use App\Http\Controllers\Quality\QirController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QirOutOfStandardIndicatorTest extends TestCase
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
            $table->date('tanggal');
            $table->string('no_batch');
            $table->string('no_mm');
            $table->string('shift');
            $table->string('line_produksi');
            $table->timestamps();
        });

        Schema::create('master_materials', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->string('nama_material');
        });

        Schema::create('qir_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qir_id');
            $table->integer('sample_no');
        });

        Schema::create('qir_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qir_detail_id');
            $table->unsignedBigInteger('parameter_id');
            $table->string('hasil_aktual')->nullable();
            $table->string('status');
        });

        Schema::create('master_item_standards', function (Blueprint $table) {
            $table->id();
            $table->string('no_mm');
            $table->unsignedBigInteger('parameter_id');
            $table->decimal('min_value', 8, 2)->nullable();
            $table->decimal('max_value', 8, 2)->nullable();
        });

        Schema::create('master_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_input');
        });

        DB::table('master_materials')->insert([
            'no_mm' => 'MM-001',
            'nama_material' => 'Produk Uji',
        ]);
        DB::table('master_parameters')->insert([
            'id' => 1,
            'tipe_input' => 'Angka',
        ]);
        DB::table('master_item_standards')->insert([
            'no_mm' => 'MM-001',
            'parameter_id' => 1,
            'min_value' => 1,
            'max_value' => 10,
        ]);

        foreach ([['BATCH-OUT', '11'], ['BATCH-OK', '5']] as $index => [$batch, $value]) {
            $qirId = DB::table('qir_records')->insertGetId([
                'tanggal' => '2026-10-20',
                'no_batch' => $batch,
                'no_mm' => 'MM-001',
                'shift' => 'Shift 1',
                'line_produksi' => 'Line 1',
                'created_at' => now()->addMinutes($index),
                'updated_at' => now(),
            ]);
            $detailId = DB::table('qir_details')->insertGetId([
                'qir_id' => $qirId,
                'sample_no' => 1,
            ]);
            DB::table('qir_results')->insert([
                'qir_detail_id' => $detailId,
                'parameter_id' => 1,
                'hasil_aktual' => $value,
                'status' => 'OK',
            ]);
        }
    }

    public function test_only_admin_and_quality_staff_receive_out_of_standard_indicators(): void
    {
        foreach (['admin', 'staff_quality'] as $role) {
            Auth::setUser(new User(['role' => $role]));

            $view = app(QirController::class)->history(Request::create('/qir/riwayat'));
            $rows = $view->getData()['riwayat']->keyBy('no_batch');

            $this->assertTrue($view->getData()['canSeeOutOfStandard']);
            $this->assertTrue($rows['BATCH-OUT']->ada_nilai_di_luar_standar);
            $this->assertFalse($rows['BATCH-OK']->ada_nilai_di_luar_standar);
        }

        Auth::setUser(new User(['role' => 'operator_quality']));

        $view = app(QirController::class)->history(Request::create('/qir/riwayat'));
        $this->assertFalse($view->getData()['canSeeOutOfStandard']);
        foreach ($view->getData()['riwayat'] as $row) {
            $this->assertFalse(property_exists($row, 'ada_nilai_di_luar_standar'));
        }
    }

    public function test_column_filters_can_be_combined_and_filter_out_of_standard_results(): void
    {
        Auth::setUser(new User(['role' => 'admin']));

        $view = app(QirController::class)->history(Request::create('/qir/riwayat', 'GET', [
            'tanggal_dari' => '2026-10-20',
            'tanggal_sampai' => '2026-10-20',
            'no_batch' => 'OUT',
            'no_mm' => 'MM-001',
            'nama_item' => 'Produk Uji',
            'shift' => 'Shift 1',
            'line' => 'Line 1',
            'status_edit' => 'baru',
            'hasil_qir' => 'di_luar_standar',
        ]));

        $rows = $view->getData()['riwayat'];
        $this->assertCount(1, $rows);
        $this->assertSame('BATCH-OUT', $rows->first()->no_batch);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('master_parameters');
        Schema::dropIfExists('master_item_standards');
        Schema::dropIfExists('qir_results');
        Schema::dropIfExists('qir_details');
        Schema::dropIfExists('master_materials');
        Schema::dropIfExists('qir_records');

        parent::tearDown();
    }
}
