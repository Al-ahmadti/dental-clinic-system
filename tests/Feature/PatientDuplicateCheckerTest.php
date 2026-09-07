<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Services\Patients\PatientDuplicateChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientDuplicateCheckerTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_similar_by_normalized_phone(): void
    {
        $existing = Patient::query()->create([
            'name' => 'أحمد علي',
            'phone' => '0501234567',
            'gender' => 'male',
        ]);

        $checker = new PatientDuplicateChecker;
        $matches = $checker->findSimilar('مريض جديد', '0501234567');

        $this->assertCount(1, $matches);
        $this->assertTrue($matches->first()->is($existing));
    }

    public function test_excludes_current_patient_when_editing(): void
    {
        $patient = Patient::query()->create([
            'name' => 'سارة',
            'phone' => '0551112222',
            'gender' => 'female',
        ]);

        $checker = new PatientDuplicateChecker;
        $matches = $checker->findSimilar('سارة', '0551112222', null, $patient->getKey());

        $this->assertCount(0, $matches);
    }

    public function test_find_similar_by_name_when_at_least_three_chars(): void
    {
        Patient::query()->create([
            'name' => 'محمد حسن',
            'phone' => '0500000001',
            'gender' => 'male',
        ]);

        $checker = new PatientDuplicateChecker;
        $matches = $checker->findSimilar('محمد', '0599999999');

        $this->assertGreaterThanOrEqual(1, $matches->count());
    }
}
