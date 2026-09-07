<?php

namespace App\Services\Patients;

use App\Models\Patient;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class PatientFinancialTotalsResult
{
    /**
     * @param  Collection<int, Visit>  $visits
     */
    public function __construct(
        public float $total_due,
        public float $total_paid,
        public float $total_balance,
        public Collection $visits,
    ) {}

    public static function empty(): self
    {
        return new self(0.0, 0.0, 0.0, collect());
    }
}

class PatientFinancialTotals
{
    /**
     * Aggregate amounts for a patient's visits. Optional date range filters on `visit_at`.
     */
    public function forPatient(Patient $patient, ?Carbon $from = null, ?Carbon $to = null): PatientFinancialTotalsResult
    {
        $query = Visit::query()
            ->where('patient_id', $patient->getKey())
            ->with(['lineItems', 'payments'])
            ->orderBy('visit_at');

        if ($from !== null) {
            $query->where('visit_at', '>=', $from->copy()->startOfDay());
        }
        if ($to !== null) {
            $query->where('visit_at', '<=', $to->copy()->endOfDay());
        }

        $visits = $query->get();

        $totalDue = 0.0;
        $totalPaid = 0.0;
        $totalBalance = 0.0;

        /** @var Visit $visit */
        foreach ($visits as $visit) {
            $totalDue += round($visit->totalLineAmount(), 2);
            $totalPaid += round($visit->totalPayments(), 2);
            $totalBalance += round($visit->balanceDue(), 2);
        }

        return new PatientFinancialTotalsResult(
            total_due: round($totalDue, 2),
            total_paid: round($totalPaid, 2),
            total_balance: round($totalBalance, 2),
            visits: $visits,
        );
    }
}
