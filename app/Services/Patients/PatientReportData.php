<?php

namespace App\Services\Patients;

use App\Models\ClinicSetting;
use App\Models\Patient;
use Illuminate\Support\Collection;

readonly class PatientReportData
{
    /**
     * @param  Collection<int, PatientReportVisitRow>  $visits
     * @param  Collection<int, array{fdi_number: int|string, current_status: string}>  $teeth
     */
    public function __construct(
        public Patient $patient,
        public ?ClinicSetting $clinic,
        public ?string $logoUrl,
        public string $primaryColor,
        public float $total_due,
        public float $total_paid,
        public float $total_balance,
        public Collection $visits,
        public Collection $teeth,
        public ?string $notes_excerpt,
    ) {}

    public function genderLabel(): string
    {
        return match ($this->patient->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => $this->patient->gender ?? '—',
        };
    }
}
