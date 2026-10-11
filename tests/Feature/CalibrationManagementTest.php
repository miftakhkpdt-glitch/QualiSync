<?php

namespace Tests\Feature;

use App\Models\CalibrationEquipment;
use App\Models\CalibrationVerification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CalibrationManagementTest extends TestCase
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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role')->default('staff_quality');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('incoming_materials', function (Blueprint $table) {
            $table->id();
            $table->string('status_qc')->nullable();
        });

        Schema::create('traceability_logs', function (Blueprint $table) {
            $table->id();
            $table->string('qa_status')->nullable();
        });

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

    public function test_staff_can_manage_equipment_and_certificate_verifications(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff_quality']));

        $response = $this->post('/quality/kalibrasi/checklist', [
            'identity_number' => 'KPDT/TUBE/001',
            'item_name' => 'DIGIMATIC MICROMETER',
            'type' => '293-240-30 MDC-25PX',
            'serial_number' => '72141598',
            'expiration_date' => '2027-09-23',
            'tool_condition' => 'OK',
            'label_condition' => 'ADA',
            'holder' => 'QA',
        ]);
        $response->assertRedirect('/quality/kalibrasi/checklist');
        $equipment = CalibrationEquipment::firstOrFail();

        $this->post('/quality/kalibrasi/verifikasi', [
            'equipment_id' => $equipment->id,
            'measurement_point' => 'In',
            'capacity' => '0 - 25 mm',
            'resolution' => '0.001 mm',
            'internal_lop' => '0.05',
            'certificate_correction' => '0',
            'certificate_uncertainty' => '0.006',
            'te' => '0.006',
            'verification_result' => '-',
            'equipment_status' => 'Alat layak digunakan',
        ])->assertRedirect('/quality/kalibrasi/verifikasi');

        $this->assertDatabaseHas('calibration_verifications', [
            'equipment_id' => $equipment->id,
            'measurement_point' => 'In',
            'equipment_status' => 'Alat layak digunakan',
        ]);

        $this->get('/quality/kalibrasi/checklist')->assertOk();
        $this->get('/quality/kalibrasi/verifikasi')->assertOk();
    }

    public function test_calibration_pages_are_limited_to_admin_and_quality_staff(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'quality']));

        $this->get('/quality/kalibrasi/checklist')
            ->assertRedirect('/qualisync/home');
    }
}
