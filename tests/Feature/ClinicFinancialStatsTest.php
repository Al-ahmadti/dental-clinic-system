<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitLineItem;
use App\Services\Clinic\ClinicFinancialStats;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicFinancialStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot_aggregates_outstanding_unpaid_and_monthly_totals(): void
    {
        Carbon::setTestNow('2026-05-15 12:00:00');

        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'مريض',
            'phone' => '050',
            'gender' => 'male',
        ]);

        $service = Service::query()->create([
            'category_id' => null,
            'name' => 'علاج',
            'price' => 100,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $visit = Visit::query()->create([
            'patient_id' => $patient->getKey(),
            'user_id' => $user->getKey(),
            'visit_at' => now(),
            'status' => 'completed',
        ]);
        VisitLineItem::query()->create([
            'visit_id' => $visit->getKey(),
            'fdi_number' => null,
            'service_id' => $service->getKey(),
            'quantity' => 1,
            'unit_price' => 100,
        ]);
        Payment::query()->create([
            'visit_id' => $visit->getKey(),
            'amount' => 30,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        Expense::query()->create([
            'amount' => 20,
            'expense_date' => now()->toDateString(),
            'category' => 'عام',
            'description' => 'مصروف',
            'user_id' => $user->getKey(),
        ]);

        $snapshot = (new ClinicFinancialStats)->snapshot(now());

        $this->assertSame(70.0, $snapshot->total_outstanding);
        $this->assertSame(1, $snapshot->unpaid_visits_count);
        $this->assertSame(30.0, $snapshot->revenue_month);
        $this->assertSame(20.0, $snapshot->expenses_month);
        $this->assertSame(10.0, $snapshot->net_month);

        Carbon::setTestNow();
    }

    public function test_monthly_series_returns_expected_keys(): void
    {
        $series = (new ClinicFinancialStats)->monthlySeries(3);

        $this->assertCount(3, $series['labels']);
        $this->assertCount(3, $series['revenue']);
        $this->assertCount(3, $series['expenses']);
        $this->assertCount(3, $series['visits']);
    }
}
