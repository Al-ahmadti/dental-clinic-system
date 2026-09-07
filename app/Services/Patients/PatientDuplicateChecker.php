<?php

namespace App\Services\Patients;

use App\Models\Patient;
use Illuminate\Support\Collection;

class PatientDuplicateChecker
{
    public function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === null || $digits === '') {
            return null;
        }

        if (strlen($digits) > 9) {
            return substr($digits, -9);
        }

        return $digits;
    }

    /**
     * @return Collection<int, Patient>
     */
    public function findSimilar(?string $name, ?string $phone, ?string $fileNumber = null, ?int $excludePatientId = null, int $limit = 5): Collection
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $name = trim((string) $name);
        $fileNumber = trim((string) $fileNumber);

        if ($normalizedPhone === null && $name === '' && $fileNumber === '') {
            return collect();
        }

        $query = Patient::query();
        if ($excludePatientId !== null) {
            $query->whereKeyNot($excludePatientId);
        }

        $query->where(function ($q) use ($normalizedPhone, $name, $fileNumber): void {
            if ($fileNumber !== '') {
                $q->orWhere('file_number', $fileNumber);
            }

            if ($normalizedPhone !== null) {
                $q->orWhere('phone', 'like', '%'.$normalizedPhone);
            }

            if (mb_strlen($name) >= 3) {
                $q->orWhere('name', 'like', '%'.$name.'%');
            }
        });

        return $query->orderBy('name')->limit($limit)->get();
    }

    public function hasSimilar(?string $name, ?string $phone, ?string $fileNumber = null, ?int $excludePatientId = null): bool
    {
        return $this->findSimilar($name, $phone, $fileNumber, $excludePatientId, 1)->isNotEmpty();
    }
}
