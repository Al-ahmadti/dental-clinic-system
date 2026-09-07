<?php

namespace Tests\Feature;

use App\Livewire\VisitFinancialPanel;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitLineItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_media_requires_auth(): void
    {
        $patient = Patient::query()->create([
            'name' => 'مريض',
            'phone' => '0501111111',
            'gender' => 'male',
        ]);

        $media = $patient->patientMedia()->create([
            'kind' => 'radiology',
            'path' => 'patient-media/test.txt',
            'original_name' => 'test.txt',
        ]);

        $this->getJson(route('patients.media.show', ['patient' => $patient, 'media' => $media]))
            ->assertUnauthorized();
    }

    public function test_visit_financial_panel_rejects_overpayment(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'مالي',
            'phone' => '0502222222',
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
            'amount' => 40,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(VisitFinancialPanel::class, ['visitId' => $visit->getKey()])
            ->set('payment_amount', '100')
            ->set('payment_method', 'cash')
            ->set('paid_at', now()->format('Y-m-d\TH:i'))
            ->call('savePayment')
            ->assertHasErrors('payment_amount');
    }

    public function test_visit_financial_panel_rejects_zero_payment(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'صفر',
            'phone' => '0503333333',
            'gender' => 'female',
        ]);

        $service = Service::query()->create([
            'category_id' => null,
            'name' => 'علاج',
            'price' => 50,
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
            'unit_price' => 50,
        ]);

        Livewire::actingAs($user)
            ->test(VisitFinancialPanel::class, ['visitId' => $visit->getKey()])
            ->set('payment_amount', '0')
            ->set('payment_method', 'cash')
            ->set('paid_at', now()->format('Y-m-d\TH:i'))
            ->call('savePayment')
            ->assertHasErrors('payment_amount');
    }
}
