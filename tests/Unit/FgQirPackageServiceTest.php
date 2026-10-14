<?php

namespace Tests\Unit;

use App\Services\Quality\FgQirPackageService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FgQirPackageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('fg_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_batch');
            $table->string('no_mm');
            $table->string('shift');
            $table->string('line_produksi');
        });

        Schema::create('qir_records', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_batch');
            $table->string('no_mm');
            $table->string('shift');
            $table->string('line_produksi');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('qir_records');
        Schema::dropIfExists('fg_inspections');

        parent::tearDown();
    }

    public function test_matching_fg_and_qir_records_form_a_complete_package(): void
    {
        $package = $this->package();
        DB::table('fg_inspections')->insert($package);
        DB::table('qir_records')->insert($package);

        $service = new FgQirPackageService();

        $this->assertCount(0, $service->pendingPackages());
    }

    public function test_incomplete_package_remains_available_for_the_reminder(): void
    {
        DB::table('fg_inspections')->insert($this->package());

        $service = new FgQirPackageService();

        $this->assertCount(1, $service->pendingPackages('Line 1'));
        $this->assertCount(0, $service->pendingPackages('Line 2'));
        $pending = $service->pendingPackages()->first();
        $this->assertSame('QIR', $pending->missing_document);
    }

    public function test_pending_package_can_only_be_completed_with_its_original_identity(): void
    {
        $id = DB::table('fg_inspections')->insertGetId($this->package());
        $service = new FgQirPackageService();
        $pending = $service->findPendingPackage('FG', $id);

        $this->assertNotNull($pending);
        $this->assertTrue($service->matchesPackage($pending, $this->package()));
        $this->assertFalse($service->matchesPackage($pending, array_merge($this->package(), ['line_produksi' => 'Line 2'])));
    }

    private function package(): array
    {
        return [
            'tanggal' => '2026-10-14',
            'no_batch' => 'BATCH-001',
            'no_mm' => 'MM-001',
            'shift' => 'Shift 1',
            'line_produksi' => 'Line 1',
        ];
    }
}
