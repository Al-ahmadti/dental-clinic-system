<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitLineItem;
use App\Services\Patients\PatientFinancialTotals;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientFinancialTotalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_totals_sum_visits_line_items_and_payments(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'مالي',
            'phone' => '050',
            'gender' => 'male',
        ]);

        $service = Service::query()->create([
            'category_id' => null,
            'name' => 'خدمة',
            'price' => 100,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $visit1 = Visit::query()->create([
            'patient_id' => $patient->getKey(),
            'user_id' => $user->getKey(),
            'visit_at' => now()->subDays(10),
            'status' => 'completed',
        ]);
        VisitLineItem::query()->create([
            'visit_id' => $visit1->getKey(),
            'fdi_number' => null,
            'service_id' => $service->getKey(),
            'quantity' => 1,
            'unit_price' => 100,
        ]);
        Payment::query()->create([
            'visit_id' => $visit1->getKey(),
            'amount' => 40,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $visit2 = Visit::query()->create([
            'patient_id' => $patient->getKey(),
            'user_id' => $user->getKey(),
            'visit_at' => now()->subDay(),
            'status' => 'completed',
        ]);
        VisitLineItem::query()->create([
            'visit_id' => $visit2->getKey(),
            'fdi_number' => null,
            'service_id' => $service->getKey(),
            'quantity' => 2,
            'unit_price' => 50,
        ]);

        $svc = new PatientFinancialTotals;
        $all = $svc->forPatient($patient, null, null);

        $this->assertSame(200.0, $all->total_due);
        $this->assertSame(40.0, $all->total_paid);
        $this->assertSame(160.0, $all->total_balance);

        $range = $svc->forPatient($patient, now()->subDays(5), now());
        $this->assertCount(1, $range->visits);
        $this->assertSame(100.0, $range->total_due);
        $this->assertSame(0.0, $range->total_paid);
        $this->assertSame(100.0, $range->total_balance);
    }
}
