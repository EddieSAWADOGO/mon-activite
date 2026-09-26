<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tableau de bord');
    }

    public function test_authenticated_cashier_can_access_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $response = $this->actingAs($cashier)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tableau de bord');
    }
}
