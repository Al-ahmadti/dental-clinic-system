<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientFinancialsPanel extends Component
{
    use AuthorizesPatientAccess;

    public int $patientId;

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
    }

    public function render(): View
    {
        $patient = Patient::query()
            ->with([
                'visits' => fn ($q) => $q->orderByDesc('visit_at')->with(['lineItems.service', 'payments']),
            ])
            ->findOrFail($this->patientId);

        $rows = $patient->visits->map(function (Visit $visit) {
            $due = round($visit->totalLineAmount(), 2);
            $paid = round($visit->totalPayments(), 2);
            $balance = round($visit->balanceDue(), 2);

            return [
                'visit' => $visit,
                'due' => $due,
                'paid' => $paid,
                'balance' => $balance,
            ];
        });

        return view('livewire.patient-financials-panel', [
            'rows' => $rows,
        ]);
    }
}
