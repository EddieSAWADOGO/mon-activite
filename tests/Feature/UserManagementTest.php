<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->get('/utilisateurs');

        $response->assertStatus(200);
        $response->assertSee('Gestion des Utilisateurs');
    }

    public function test_cashier_cannot_access_user_management(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $response = $this->actingAs($cashier)->get('/utilisateurs');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->post('/utilisateurs', [
            'name' => 'Nouveau Caissier',
            'email' => 'caissier.nouveau@suivremoncommerce.com',
            'password' => 'secret123',
            'role' => UserRole::CASHIER->value,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/utilisateurs');
        $this->assertDatabaseHas('users', [
            'email' => 'caissier.nouveau@suivremoncommerce.com',
            'role' => UserRole::CASHIER->value,
        ]);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRole::CASHIER]);

        $response = $this->actingAs($admin)->post("/utilisateurs/{$targetUser->id}/reset-password", [
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect('/utilisateurs');
        $this->assertTrue(auth()->attempt([
            'email' => $targetUser->email,
            'password' => 'newpassword123',
        ]));
    }
}
