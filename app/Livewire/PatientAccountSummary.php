<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use App\Services\Patients\PatientFinancialTotals;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientAccountSummary extends Component
{
    use AuthorizesPatientAccess;

    public int $patientId;

    public string $range_from = '';

    public string $range_to = '';

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
        $this->range_from = now()->subMonths(3)->format('Y-m-d');
        $this->range_to = now()->format('Y-m-d');
    }

    public function getTotalsLifetimeProperty(): array
    {
        $patient = Patient::query()->findOrFail($this->patientId);
        $r = app(PatientFinancialTotals::class)->forPatient($patient, null, null);

        return [
            'due' => $r->total_due,
            'paid' => $r->total_paid,
            'balance' => $r->total_balance,
        ];
    }

    public function getTotalsRangeProperty(): array
    {
        $patient = Patient::query()->findOrFail($this->patientId);
        $from = $this->range_from !== '' ? Carbon::parse($this->range_from)->startOfDay() : null;
        $to = $this->range_to !== '' ? Carbon::parse($this->range_to)->endOfDay() : null;
        $r = app(PatientFinancialTotals::class)->forPatient($patient, $from, $to);

        return [
            'due' => $r->total_due,
            'paid' => $r->total_paid,
            'balance' => $r->total_balance,
        ];
    }

    public function getStatementUrlProperty(): string
    {
        $base = PatientResource::getUrl('statement', ['record' => $this->patientId]);

        return $base.'?'.http_build_query([
            'from' => $this->range_from,
            'to' => $this->range_to,
        ]);
    }

    public function render(): View
    {
        return view('livewire.patient-account-summary');
    }
}
