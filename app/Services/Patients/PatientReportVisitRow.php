<?php

namespace App\Services\Patients;

readonly class PatientReportVisitRow
{
    public function __construct(
        public string $visit_at,
        public string $doctor_name,
        public string $services_summary,
        public ?string $diagnosis,
        public float $due,
        public float $paid,
        public float $balance,
        public string $payment_label,
    ) {}
}
