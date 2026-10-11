<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Models\CalibrationEquipment;
use App\Models\CalibrationVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalibrationController extends Controller
{
    public function checklist(): View
    {
        $equipment = CalibrationEquipment::orderBy('identity_number')->paginate(25);

        return view('quality.calibration.checklist', compact('equipment'));
    }

    public function createEquipment(): View
    {
        return view('quality.calibration.equipment-form', ['equipment' => new CalibrationEquipment()]);
    }

    public function storeEquipment(Request $request): RedirectResponse
    {
        $equipment = CalibrationEquipment::create($this->validateEquipment($request));

        return redirect()->route('quality.calibration.checklist.index')
            ->with('success', "Alat ukur {$equipment->identity_number} berhasil ditambahkan.");
    }

    public function editEquipment(CalibrationEquipment $equipment): View
    {
        return view('quality.calibration.equipment-form', compact('equipment'));
    }

    public function updateEquipment(Request $request, CalibrationEquipment $equipment): RedirectResponse
    {
        $equipment->update($this->validateEquipment($request, $equipment));

        return redirect()->route('quality.calibration.checklist.index')
            ->with('success', "Data alat ukur {$equipment->identity_number} berhasil diperbarui.");
    }

    public function destroyEquipment(CalibrationEquipment $equipment): RedirectResponse
    {
        $identityNumber = $equipment->identity_number;
        $equipment->delete();

        return redirect()->route('quality.calibration.checklist.index')
            ->with('success', "Alat ukur {$identityNumber} dan data verifikasinya berhasil dihapus.");
    }

    public function verifications(): View
    {
        $verifications = CalibrationVerification::with('equipment')
            ->join('calibration_equipment', 'calibration_verifications.equipment_id', '=', 'calibration_equipment.id')
            ->select('calibration_verifications.*')
            ->orderBy('calibration_equipment.identity_number')
            ->orderBy('calibration_verifications.measurement_point')
            ->paginate(25);

        return view('quality.calibration.verifications', compact('verifications'));
    }

    public function createVerification(): View
    {
        $equipment = CalibrationEquipment::orderBy('identity_number')->get();

        return view('quality.calibration.verification-form', [
            'verification' => new CalibrationVerification(),
            'equipmentList' => $equipment,
        ]);
    }

    public function storeVerification(Request $request): RedirectResponse
    {
        $verification = CalibrationVerification::create($this->validateVerification($request));

        return redirect()->route('quality.calibration.verification.index')
            ->with('success', "Data verifikasi untuk {$verification->equipment->identity_number} berhasil ditambahkan.");
    }

    public function editVerification(CalibrationVerification $verification): View
    {
        return view('quality.calibration.verification-form', [
            'verification' => $verification,
            'equipmentList' => CalibrationEquipment::orderBy('identity_number')->get(),
        ]);
    }

    public function updateVerification(Request $request, CalibrationVerification $verification): RedirectResponse
    {
        $verification->update($this->validateVerification($request));

        return redirect()->route('quality.calibration.verification.index')
            ->with('success', 'Data verifikasi sertifikat berhasil diperbarui.');
    }

    public function destroyVerification(CalibrationVerification $verification): RedirectResponse
    {
        $verification->delete();

        return redirect()->route('quality.calibration.verification.index')
            ->with('success', 'Data verifikasi sertifikat berhasil dihapus.');
    }

    private function validateEquipment(Request $request, ?CalibrationEquipment $equipment = null): array
    {
        return $request->validate([
            'identity_number' => ['required', 'string', 'max:100', Rule::unique('calibration_equipment', 'identity_number')->ignore($equipment?->id)],
            'item_name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'expiration_date' => ['nullable', 'date'],
            'tool_condition' => ['nullable', 'string', 'max:100'],
            'label_condition' => ['nullable', 'string', 'max:100'],
            'holder' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function validateVerification(Request $request): array
    {
        return $request->validate([
            'equipment_id' => ['required', 'exists:calibration_equipment,id'],
            'measurement_point' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'string', 'max:100'],
            'resolution' => ['nullable', 'string', 'max:100'],
            'internal_lop' => ['nullable', 'string', 'max:100'],
            'certificate_correction' => ['nullable', 'string', 'max:100'],
            'certificate_uncertainty' => ['nullable', 'string', 'max:100'],
            'te' => ['nullable', 'string', 'max:100'],
            'verification_result' => ['nullable', 'string', 'max:100'],
            'equipment_status' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
