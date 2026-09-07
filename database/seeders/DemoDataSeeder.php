<?php

namespace Database\Seeders;

use App\Enums\ToothStatus;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Patient;
use App\Models\PatientMedia;
use App\Models\PatientTooth;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\ToothTreatmentHistory;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitLineItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'doctor@dental.local')->first();
        if (! $user) {
            $this->command?->warn('تخطّي DemoDataSeeder: لا يوجد مستخدم doctor@dental.local (شغّل DentistUserSeeder أولاً).');

            return;
        }

        if (Service::query()->doesntExist()) {
            $this->call(ServiceCatalogSeeder::class);
        }

        Storage::disk('local')->makeDirectory('patient-media');
        if (! Storage::disk('local')->exists('patient-media/seed-demo.txt')) {
            Storage::disk('local')->put('patient-media/seed-demo.txt', 'ملف تجريبي من السيدر');
        }

        DB::transaction(function () use ($user): void {
            $this->seedPatientsVisitsPayments($user);
            $this->seedOdontogram($user);
            $this->seedPatientMedia();
            $this->seedSuppliersInventoryExpenses($user);
        });
    }

    private function seedPatientsVisitsPayments(User $user): void
    {
        $rows = [
            ['name' => 'أحمد محمد العلي', 'phone' => '0501111111', 'gender' => 'male', 'birth_date' => '1992-03-15', 'notes' => 'حساسية خفيفة من البنج'],
            ['name' => 'فاطمة عبدالله السعيد', 'phone' => '0502222222', 'gender' => 'female', 'birth_date' => '1988-07-22', 'notes' => null],
            ['name' => 'خالد سعد الدوسري', 'phone' => '0503333333', 'gender' => 'male', 'birth_date' => '2001-11-01', 'notes' => 'زيارة متابعة تقويم'],
            ['name' => 'نورة حسن القحطاني', 'phone' => '0504444444', 'gender' => 'female', 'birth_date' => '1995-01-30', 'notes' => null],
            ['name' => 'عبدالرحمن ماجد الشهري', 'phone' => '0505555555', 'gender' => 'male', 'birth_date' => '1979-09-09', 'notes' => 'مراجعة دورية'],
            ['name' => 'لينا طارق الحربي', 'phone' => '0506666666', 'gender' => 'female', 'birth_date' => '2003-05-18', 'notes' => null],
        ];

        $consultation = Service::query()->where('name', 'استشارة')->first();
        $servicesPool = Service::query()
            ->where('name', '!=', 'استشارة')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        if ($servicesPool->isEmpty() && $consultation === null) {
            $this->command?->warn('تخطّي زيارات العرض: لا توجد خدمات في قاعدة البيانات.');

            return;
        }

        foreach ($rows as $row) {
            $patient = Patient::query()->firstOrCreate(
                ['phone' => $row['phone']],
                [
                    'name' => $row['name'],
                    'gender' => $row['gender'],
                    'birth_date' => $row['birth_date'],
                    'notes' => $row['notes'],
                ]
            );

            if ($patient->visits()->exists()) {
                continue;
            }

            $visitCount = fake()->numberBetween(1, 3);
            for ($v = 0; $v < $visitCount; $v++) {
                $visitAt = now()->subDays(fake()->numberBetween(5, 120))->setHour(fake()->numberBetween(9, 16));

                $visit = Visit::query()->create([
                    'patient_id' => $patient->id,
                    'user_id' => $user->id,
                    'visit_at' => $visitAt,
                    'status' => fake()->randomElement(['completed', 'completed', 'completed', 'scheduled']),
                    'diagnosis' => $v === 0 ? 'فحص أولي وتخطيط علاج' : 'متابعة بعد العلاج',
                    'notes' => 'سجلات تجريبية للتجربة',
                ]);

                $pickCount = min(2, max(1, $servicesPool->count()));
                $picked = $servicesPool->isNotEmpty()
                    ? $servicesPool->random($pickCount)
                    : collect();

                $total = 0.0;
                foreach ($picked as $svc) {
                    $qty = fake()->numberBetween(1, 2);
                    $unit = (float) $svc->price;
                    VisitLineItem::query()->create([
                        'visit_id' => $visit->id,
                        'service_id' => $svc->id,
                        'quantity' => $qty,
                        'unit_price' => $unit,
                        'notes' => null,
                    ]);
                    $total += $qty * $unit;
                }

                if ($picked->isEmpty() && $consultation) {
                    $u = (float) $consultation->price;
                    VisitLineItem::query()->create([
                        'visit_id' => $visit->id,
                        'service_id' => $consultation->id,
                        'quantity' => 1,
                        'unit_price' => $u,
                        'notes' => null,
                    ]);
                    $total += $u;
                } elseif ($consultation && $picked->isNotEmpty() && fake()->boolean(40)) {
                    $u = (float) $consultation->price;
                    VisitLineItem::query()->create([
                        'visit_id' => $visit->id,
                        'service_id' => $consultation->id,
                        'quantity' => 1,
                        'unit_price' => $u,
                        'notes' => null,
                    ]);
                    $total += $u;
                }

                $total = round($total, 2);
                if ($total <= 0) {
                    continue;
                }

                if (fake()->boolean(75)) {
                    Payment::query()->create([
                        'visit_id' => $visit->id,
                        'amount' => $total,
                        'payment_method' => fake()->randomElement(['cash', 'cash', 'card', 'transfer']),
                        'paid_at' => $visitAt->copy()->addHour(),
                        'notes' => null,
                    ]);
                } else {
                    $first = round($total * 0.5, 2);
                    Payment::query()->create([
                        'visit_id' => $visit->id,
                        'amount' => $first,
                        'payment_method' => 'cash',
                        'paid_at' => $visitAt->copy()->addHour(),
                        'notes' => 'دفعة جزئية',
                    ]);
                    Payment::query()->create([
                        'visit_id' => $visit->id,
                        'amount' => round($total - $first, 2),
                        'payment_method' => 'card',
                        'paid_at' => $visitAt->copy()->addDays(7),
                        'notes' => 'تسوية المتبقي',
                    ]);
                }
            }
        }
    }

    private function seedOdontogram(User $user): void
    {
        $patient = Patient::query()->where('phone', '0501111111')->first();
        if (! $patient || $patient->patientTeeth()->exists()) {
            return;
        }

        $fdiSet = [11, 12, 16, 21, 26, 36, 46];
        $service = Service::query()->where('name', 'like', '%حشو%')->first()
            ?? Service::query()->first();

        $visit = $patient->visits()->orderByDesc('visit_at')->first();

        foreach ($fdiSet as $fdi) {
            $status = match ($fdi) {
                16, 46 => ToothStatus::Treated,
                11, 21 => ToothStatus::Decayed,
                default => ToothStatus::Healthy,
            };

            PatientTooth::query()->create([
                'patient_id' => $patient->id,
                'fdi_number' => $fdi,
                'current_status' => $status->value,
            ]);

            ToothTreatmentHistory::query()->create([
                'patient_id' => $patient->id,
                'fdi_number' => $fdi,
                'service_id' => $service?->id,
                'status' => $status->value,
                'doctor_notes' => 'سجل تجريبي من السيدر',
                'visit_id' => $visit?->id,
                'user_id' => $user->id,
                'performed_at' => now()->subDays(10),
            ]);
        }
    }

    private function seedPatientMedia(): void
    {
        $patient = Patient::query()->where('phone', '0502222222')->first();
        if (! $patient || $patient->patientMedia()->exists()) {
            return;
        }

        $visit = $patient->visits()->orderByDesc('visit_at')->first();

        foreach (['before', 'after', 'radiology'] as $kind) {
            PatientMedia::query()->create([
                'patient_id' => $patient->id,
                'visit_id' => $visit?->id,
                'kind' => $kind,
                'path' => 'patient-media/seed-demo.txt',
                'original_name' => "seed-{$kind}.txt",
            ]);
        }
    }

    private function seedSuppliersInventoryExpenses(User $user): void
    {
        $supplier = Supplier::query()->firstOrCreate(
            ['company_name' => 'مورد تجريبي — شركة المستلزمات الطبية'],
            [
                'contact_name' => 'محمد المورد',
                'phone' => '0112345678',
                'notes' => 'بيانات تجريبية',
            ]
        );

        Supplier::query()->firstOrCreate(
            ['company_name' => 'مورد تجريبي — أدوات الأسنان'],
            [
                'contact_name' => 'سارة',
                'phone' => '0123456789',
                'notes' => null,
            ]
        );

        $gloves = InventoryItem::query()->firstOrCreate(
            ['name' => 'قفازات نتريل (صندوق)'],
            [
                'type' => 'medical',
                'unit' => 'piece',
                'quantity_on_hand' => 0,
            ]
        );

        $masks = InventoryItem::query()->firstOrCreate(
            ['name' => 'كمامات طبية'],
            [
                'type' => 'medical',
                'unit' => 'piece',
                'quantity_on_hand' => 0,
            ]
        );

        if ($gloves->movements()->doesntExist()) {
            $qty = 100;
            $unitCost = 0.5;
            $purchaseMovement = InventoryMovement::query()->create([
                'inventory_item_id' => $gloves->id,
                'supplier_id' => $supplier->id,
                'type' => 'purchase',
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'amount_paid' => $qty * $unitCost,
                'balance_due' => 0,
                'movement_at' => now()->subDays(14),
                'notes' => 'شراء تجريبي',
            ]);
            $gloves->update(['quantity_on_hand' => $qty]);

            Expense::query()->create([
                'amount' => $qty * $unitCost,
                'expense_date' => now()->subDays(14)->toDateString(),
                'category' => 'inventory_purchase',
                'description' => 'شراء قفازات (تجريبي)',
                'user_id' => $user->id,
                'inventory_movement_id' => $purchaseMovement->id,
            ]);
        }

        if ($masks->movements()->doesntExist()) {
            InventoryMovement::query()->create([
                'inventory_item_id' => $masks->id,
                'supplier_id' => $supplier->id,
                'type' => 'purchase',
                'quantity' => 200,
                'unit_cost' => 0.25,
                'amount_paid' => 30,
                'balance_due' => 20,
                'movement_at' => now()->subDays(5),
                'notes' => 'دفعة جزئية للمورد',
            ]);
            $masks->update(['quantity_on_hand' => 200]);
        }

        $issueNote = 'صرف للعيادة (تجريبي)';
        if (! InventoryMovement::query()
            ->where('inventory_item_id', $gloves->id)
            ->where('type', 'issue')
            ->where('notes', $issueNote)
            ->exists()) {
            InventoryMovement::query()->create([
                'inventory_item_id' => $gloves->id,
                'supplier_id' => null,
                'type' => 'issue',
                'quantity' => 10,
                'unit_cost' => 0,
                'amount_paid' => 0,
                'balance_due' => 0,
                'movement_at' => now()->subDay()->startOfHour(),
                'notes' => $issueNote,
            ]);
            $gloves->refresh();
            $onHand = max(0, (float) $gloves->quantity_on_hand - 10);
            $gloves->update(['quantity_on_hand' => $onHand]);
        }

        if (Expense::query()->where('category', 'rent')->doesntExist()) {
            Expense::query()->create([
                'amount' => 5000,
                'expense_date' => now()->startOfMonth()->toDateString(),
                'category' => 'rent',
                'description' => 'إيجار تجريبي للشهر',
                'user_id' => $user->id,
                'inventory_movement_id' => null,
            ]);
        }

        if (Expense::query()->where('category', 'other')->where('description', 'فاتورة كهرباء (تجريبي)')->doesntExist()) {
            Expense::query()->create([
                'amount' => 350.5,
                'expense_date' => now()->subDays(3)->toDateString(),
                'category' => 'other',
                'description' => 'فاتورة كهرباء (تجريبي)',
                'user_id' => $user->id,
                'inventory_movement_id' => null,
            ]);
        }
    }
}
