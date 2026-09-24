<?php

namespace Tests\Feature;

use App\Domain\Clients\Models\Customer;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_and_admin_can_view_customers_list(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);
        $customer = Customer::factory()->create(['name' => 'Jean Dupont']);

        $response = $this->actingAs($cashier)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('Jean Dupont');
    }

    public function test_cashier_can_create_customer(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $response = $this->actingAs($cashier)->post(route('clients.store'), [
            'type' => 'particulier',
            'name' => 'Alice Kouadio',
            'phone' => '+229 97000000',
            'email' => 'alice@example.com',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'name' => 'Alice Kouadio',
            'type' => 'particulier',
        ]);
    }

    public function test_cashier_cannot_delete_customer(): void
    {
        $cashier = User::factory()->create(['role' => UserRole::CASHIER]);
        $customer = Customer::factory()->create();

        $response = $this->actingAs($cashier)->delete(route('clients.destroy', $customer));

        $response->assertStatus(403);
    }
}
