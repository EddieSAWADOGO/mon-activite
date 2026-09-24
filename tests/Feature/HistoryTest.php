<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->cashier = User::factory()->create(['role' => UserRole::CASHIER]);
    }

    public function test_admin_can_access_history_pages(): void
    {
        $this->actingAs($this->admin)->get(route('historique.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('historique.purchases'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('historique.sales'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('historique.stock-at-date'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('historique.top-products'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('historique.financial-overview'))->assertStatus(200);
    }

    public function test_cashier_is_forbidden_from_history(): void
    {
        $this->actingAs($this->cashier)->get(route('historique.index'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('historique.purchases'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('historique.sales'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('historique.stock-at-date'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('historique.top-products'))->assertStatus(403);
        $this->actingAs($this->cashier)->get(route('historique.financial-overview'))->assertStatus(403);
    }
}
