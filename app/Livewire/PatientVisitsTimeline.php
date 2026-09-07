<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientVisitsTimeline extends Component
{
    use AuthorizesPatientAccess;

    public int $patientId;

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
    }

    public static function paymentLabel(Visit $visit): string
    {
        $balance = round($visit->balanceDue(), 2);
        $paid = round($visit->totalPayments(), 2);

        if ($balance <= 0.01) {
            return 'مدفوع بالكامل';
        }

        if ($paid > 0.01) {
            return 'مدفوع جزئياً';
        }

        return 'غير مدفوع';
    }

    public static function servicesSummary(Visit $visit): string
    {
        $names = $visit->lineItems->map(fn ($l) => $l->service?->name)->filter()->unique()->values();

        return $names->isEmpty() ? '—' : $names->implode('، ');
    }

    public function render(): View
    {
        $visits = Visit::query()
            ->where('patient_id', $this->patientId)
            ->with(['lineItems.service', 'payments', 'user'])
            ->orderByDesc('visit_at')
            ->get();

        return view('livewire.patient-visits-timeline', [
            'visits' => $visits,
        ]);
    }
}
