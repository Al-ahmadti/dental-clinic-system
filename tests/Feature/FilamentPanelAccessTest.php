<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_admin_panel_in_production(): void
    {
        config(['app.env' => 'production']);

        $user = User::factory()->create([
            'email' => 'doctor@dental.local',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertFalse(
            str_contains($response->getContent(), '403') && str_contains($response->getContent(), 'Forbidden'),
            'Authenticated user must not receive Filament 403 in production'
        );
    }
}
