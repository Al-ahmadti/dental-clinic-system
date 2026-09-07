<?php

namespace App\Livewire\Concerns;

use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use App\Models\Visit;

trait AuthorizesPatientAccess
{
    protected function authorizePatientAccess(int $patientId): Patient
    {
        $patient = Patient::query()->findOrFail($patientId);

        abort_unless(PatientResource::canView($patient), 403);

        return $patient;
    }

    protected function authorizeVisitAccess(int $visitId): Visit
    {
        $visit = Visit::query()->with('patient')->findOrFail($visitId);

        abort_unless($visit->patient !== null, 404);
        abort_unless(PatientResource::canView($visit->patient), 403);

        return $visit;
    }
}
