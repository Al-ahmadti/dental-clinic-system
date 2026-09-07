<?php

namespace App\Services\Patients;

use App\Livewire\PatientVisitsTimeline;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\PatientTooth;
use App\Models\Visit;
use Illuminate\Support\Str;

class PatientReportBuilder
{
    public function build(Patient $patient): PatientReportData
    {
        $clinic = ClinicSetting::current();
        $financial = app(PatientFinancialTotals::class)->forPatient($patient, null, null);

        $logoUrl = null;
        if ($clinic?->logo_path && file_exists(storage_path('app/public/'.$clinic->logo_path))) {
            $logoUrl = asset('storage/'.$clinic->logo_path);
        }

        $visits = Visit::query()
            ->where('patient_id', $patient->getKey())
            ->with(['lineItems.service', 'payments', 'user'])
            ->orderByDesc('visit_at')
            ->get()
            ->map(fn (Visit $visit): PatientReportVisitRow => new PatientReportVisitRow(
                visit_at: $visit->visit_at?->format('Y-m-d H:i') ?? '—',
                doctor_name: $visit->user?->name ?? '—',
                services_summary: PatientVisitsTimeline::servicesSummary($visit),
                diagnosis: $visit->diagnosis,
                due: round($visit->totalLineAmount(), 2),
                paid: round($visit->totalPayments(), 2),
                balance: round($visit->balanceDue(), 2),
                payment_label: PatientVisitsTimeline::paymentLabel($visit),
            ));

        $teeth = PatientTooth::query()
            ->where('patient_id', $patient->getKey())
            ->orderBy('fdi_number')
            ->get()
            ->map(fn (PatientTooth $tooth): array => [
                'fdi_number' => $tooth->fdi_number,
                'current_status' => $tooth->current_status ?? '—',
            ]);

        $notes = $patient->notes;
        $notesExcerpt = $notes !== null && $notes !== ''
            ? Str::limit($notes, 500)
            : null;

        return new PatientReportData(
            patient: $patient,
            clinic: $clinic,
            logoUrl: $logoUrl,
            primaryColor: $clinic?->primary_color ?: '#2563EB',
            total_due: $financial->total_due,
            total_paid: $financial->total_paid,
            total_balance: $financial->total_balance,
            visits: $visits,
            teeth: $teeth,
            notes_excerpt: $notesExcerpt,
        );
    }
}
