<?php

namespace App\Observers;

use App\Models\Patient;
use App\Models\PatientAuditLog;

class PatientObserver
{
    /** @var array<int, string> */
    private const WATCHED = ['name', 'phone', 'gender', 'birth_date', 'notes', 'file_number'];

    public function updated(Patient $patient): void
    {
        $keys = array_keys($patient->getDirty());
        if (array_intersect($keys, self::WATCHED) === []) {
            return;
        }

        $changes = [];
        foreach (self::WATCHED as $key) {
            if ($patient->wasChanged($key)) {
                $changes[$key] = [
                    'old' => $patient->getOriginal($key),
                    'new' => $patient->getAttribute($key),
                ];
            }
        }

        $this->writeLog($patient, 'updated', $changes);
    }

    public function deleted(Patient $patient): void
    {
        $this->writeLog(
            patient: $patient,
            action: 'deleted',
            changes: ['snapshot' => $patient->only(self::WATCHED)],
        );
    }

    public function restored(Patient $patient): void
    {
        $this->writeLog(
            patient: $patient,
            action: 'restored',
            changes: ['snapshot' => $patient->only(self::WATCHED)],
        );
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function writeLog(Patient $patient, string $action, array $changes): void
    {
        PatientAuditLog::query()->create([
            'patient_id' => $patient->getKey(),
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
