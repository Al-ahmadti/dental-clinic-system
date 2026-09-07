<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PatientAccountStatementPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_route_requires_auth(): void
    {
        $patient = Patient::query()->create([
            'name' => 'PDF',
            'phone' => '050',
            'gender' => 'male',
        ]);

        $this->getJson(route('patients.account-statement.pdf', $patient))
            ->assertUnauthorized();
    }

    public function test_pdf_denied_without_view_permission(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'PDF',
            'phone' => '0501111111',
            'gender' => 'male',
        ]);

        Gate::before(function ($user, string $ability, array $arguments = []) {
            if ($ability === 'view' && isset($arguments[0]) && $arguments[0] instanceof Patient) {
                return false;
            }

            return null;
        });

        $this->actingAs($user)
            ->get(route('patients.account-statement.pdf', $patient))
            ->assertForbidden();
    }

    public function test_authenticated_user_can_download_pdf(): void
    {
        $user = User::factory()->create();
        $patient = Patient::query()->create([
            'name' => 'PDF',
            'phone' => '0502222222',
            'gender' => 'male',
        ]);

        $response = $this->actingAs($user)->get(route('patients.account-statement.pdf', [
            'patient' => $patient,
            'from' => now()->subMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $this->assertStringContainsString('pdf', strtolower($response->headers->get('content-type', '')));
    }
}
