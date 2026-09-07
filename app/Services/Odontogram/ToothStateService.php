<?php

namespace App\Services\Odontogram;

use App\Enums\ToothStatus;
use App\Models\Patient;
use App\Models\PatientTooth;
use App\Models\Service;
use App\Support\FdiTooth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ToothStateService
{
    public function __construct(
        protected ToothHistoryService $history,
    ) {}

    public function applyTreatment(
        Patient $patient,
        int $fdiNumber,
        ToothStatus $newStatus,
        int $userId,
        ?int $serviceId = null,
        ?int $visitId = null,
        ?string $doctorNotes = null,
    ): void {
        if (! FdiTooth::isValid($fdiNumber)) {
            throw new InvalidArgumentException("Invalid FDI tooth number: {$fdiNumber}");
        }

        if ($serviceId !== null && ! Service::query()->whereKey($serviceId)->exists()) {
            throw new InvalidArgumentException("Invalid service id: {$serviceId}");
        }

        DB::transaction(function () use ($patient, $fdiNumber, $newStatus, $userId, $serviceId, $visitId, $doctorNotes): void {
            PatientTooth::query()->updateOrCreate(
                [
                    'patient_id' => $patient->getKey(),
                    'fdi_number' => $fdiNumber,
                ],
                [
                    'current_status' => $newStatus->value,
                ],
            );

            $this->history->record(
                patientId: (int) $patient->getKey(),
                fdiNumber: $fdiNumber,
                status: $newStatus->value,
                userId: $userId,
                serviceId: $serviceId,
                visitId: $visitId,
                doctorNotes: $doctorNotes,
            );
        });
    }

    public static function inferStatusFromService(Service $service): ToothStatus
    {
        $name = mb_strtolower($service->name);

        if (str_contains($name, 'خلع') || str_contains($name, 'extraction')) {
            return ToothStatus::Missing;
        }

        if (str_contains($name, 'تسوس') || str_contains($name, 'decay')) {
            return ToothStatus::Decayed;
        }

        return ToothStatus::Treated;
    }
}
