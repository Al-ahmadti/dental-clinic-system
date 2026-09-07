<?php

namespace App\Services\Odontogram;

use App\Models\ToothTreatmentHistory;
use Illuminate\Support\Carbon;

class ToothHistoryService
{
    public function record(
        int $patientId,
        int $fdiNumber,
        string $status,
        int $userId,
        ?int $serviceId = null,
        ?int $visitId = null,
        ?string $doctorNotes = null,
        ?Carbon $performedAt = null,
    ): ToothTreatmentHistory {
        return ToothTreatmentHistory::query()->create([
            'patient_id' => $patientId,
            'fdi_number' => $fdiNumber,
            'service_id' => $serviceId,
            'status' => $status,
            'doctor_notes' => $doctorNotes,
            'visit_id' => $visitId,
            'user_id' => $userId,
            'performed_at' => $performedAt ?? now(),
        ]);
    }
}
