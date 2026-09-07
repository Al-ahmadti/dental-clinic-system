<?php

namespace Tests\Feature;

use App\Enums\ToothStatus;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\Odontogram\ToothHistoryService;
use App\Services\Odontogram\ToothStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ToothOdontogramServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tooth_state_service_rejects_invalid_fdi(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'Test',
            'phone' => '1',
            'gender' => 'male',
        ]);

        $service = Service::query()->create([
            'category_id' => null,
            'name' => 'حشو',
            'price' => 100,
            'is_active' => true,
        ]);

        $svc = new ToothStateService(new ToothHistoryService);

        $this->expectException(InvalidArgumentException::class);
        $svc->applyTreatment(
            patient: $patient,
            fdiNumber: 99,
            newStatus: ToothStatus::Treated,
            userId: (int) $user->getKey(),
            serviceId: (int) $service->getKey(),
        );
    }

    public function test_tooth_state_and_history_persist_for_valid_fdi(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'Test',
            'phone' => '1',
            'gender' => 'male',
        ]);

        $service = Service::query()->create([
            'category_id' => null,
            'name' => 'حشو',
            'price' => 100,
            'is_active' => true,
        ]);

        $svc = new ToothStateService(new ToothHistoryService);

        $svc->applyTreatment(
            patient: $patient,
            fdiNumber: 11,
            newStatus: ToothStatus::Treated,
            userId: (int) $user->getKey(),
            serviceId: (int) $service->getKey(),
            doctorNotes: 'ملاحظة',
        );

        $this->assertDatabaseHas('patient_teeth', [
            'patient_id' => $patient->getKey(),
            'fdi_number' => 11,
            'current_status' => 'treated',
        ]);

        $this->assertDatabaseHas('tooth_treatment_histories', [
            'patient_id' => $patient->getKey(),
            'fdi_number' => 11,
            'status' => 'treated',
            'service_id' => $service->getKey(),
        ]);
    }

    public function test_tooth_history_service_accepts_null_service_and_notes(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'Test',
            'phone' => '1',
            'gender' => 'male',
        ]);

        $hist = new ToothHistoryService;
        $row = $hist->record(
            patientId: (int) $patient->getKey(),
            fdiNumber: 21,
            status: 'healthy',
            userId: (int) $user->getKey(),
            serviceId: null,
            visitId: null,
            doctorNotes: null,
        );

        $this->assertSame('healthy', $row->status);
        $this->assertNull($row->doctor_notes);
    }
}
