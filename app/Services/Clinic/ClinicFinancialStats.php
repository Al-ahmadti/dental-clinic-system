<?php

namespace App\Services\Clinic;

use App\Models\Expense;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Visit;
use App\Services\Patients\PatientFinancialTotals;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class ClinicFinancialSnapshot
{
    public function __construct(
        public float $total_outstanding,
        public int $unpaid_visits_count,
        public float $revenue_month,
        public float $expenses_month,
        public float $net_month,
    ) {}
}

class ClinicFinancialStats
{
    public function snapshot(?Carbon $month = null): ClinicFinancialSnapshot
    {
        $month ??= now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $totalOutstanding = 0.0;
        $unpaidVisits = 0;

        Visit::query()
            ->with(['lineItems', 'payments'])
            ->chunkById(100, function ($visits) use (&$totalOutstanding, &$unpaidVisits): void {
                foreach ($visits as $visit) {
                    $balance = round($visit->balanceDue(), 2);
                    $totalOutstanding += $balance;
                    if ($balance > 0.01) {
                        $unpaidVisits++;
                    }
                }
            });

        $revenueMonth = (float) Payment::query()
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        $expensesMonth = (float) Expense::query()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        return new ClinicFinancialSnapshot(
            total_outstanding: round($totalOutstanding, 2),
            unpaid_visits_count: $unpaidVisits,
            revenue_month: round($revenueMonth, 2),
            expenses_month: round($expensesMonth, 2),
            net_month: round($revenueMonth - $expensesMonth, 2),
        );
    }

    /**
     * Last N months labels and series for charts.
     *
     * @return array{labels: array<int, string>, revenue: array<int, float>, expenses: array<int, float>, visits: array<int, int>}
     */
    public function monthlySeries(int $months = 6): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];
        $visits = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $revenue[] = round((float) Payment::query()
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount'), 2);

            $expenses[] = round((float) Expense::query()
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount'), 2);

            $visits[] = (int) Visit::query()
                ->whereBetween('visit_at', [$start, $end])
                ->count();
        }

        return compact('labels', 'revenue', 'expenses', 'visits');
    }

    /**
     * @return Collection<int, array{patient: Patient, balance: float}>
     */
    public function topOutstandingPatients(int $limit = 10): Collection
    {
        $totals = app(PatientFinancialTotals::class);
        $rows = collect();

        Patient::query()
            ->orderBy('name')
            ->chunk(50, function ($patients) use ($totals, &$rows): void {
                foreach ($patients as $patient) {
                    $result = $totals->forPatient($patient);
                    if ($result->total_balance > 0.01) {
                        $rows->push([
                            'patient' => $patient,
                            'balance' => $result->total_balance,
                        ]);
                    }
                }
            });

        return $rows->sortByDesc('balance')->take($limit)->values();
    }

    /**
     * Visits today / week counts for dashboard.
     *
     * @return array{today: int, week: int, month: int}
     */
    public function visitCounts(): array
    {
        return [
            'today' => Visit::query()->whereDate('visit_at', today())->count(),
            'week' => Visit::query()->whereBetween('visit_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Visit::query()
                ->whereMonth('visit_at', now()->month)
                ->whereYear('visit_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Top service by line item quantity this month.
     */
    public function topServiceNameThisMonth(): string
    {
        $topServiceId = DB::table('visit_line_items')
            ->join('visits', 'visit_line_items.visit_id', '=', 'visits.id')
            ->whereMonth('visits.visit_at', now()->month)
            ->whereYear('visits.visit_at', now()->year)
            ->select('visit_line_items.service_id', DB::raw('SUM(visit_line_items.quantity) as total_qty'))
            ->groupBy('visit_line_items.service_id')
            ->orderByDesc('total_qty')
            ->value('service_id');

        if (! $topServiceId) {
            return '—';
        }

        return (string) DB::table('services')->where('id', $topServiceId)->value('name') ?: '—';
    }
}
