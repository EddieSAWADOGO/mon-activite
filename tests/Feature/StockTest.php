<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Product $product;
    protected StockUnit $baseUnit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->cashier = User::factory()->create([
            'role' => UserRole::CASHIER,
            'is_active' => true,
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Fungicide Super',
        ]);

        $this->baseUnit = StockUnit::create([
            'product_id' => $this->product->id,
            'name' => 'Bidon 1L',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'default_selling_price' => 5000,
            'low_stock_threshold' => 10,
            'current_stock' => 4, // low stock
        ]);
    }

    public function test_all_users_including_cashier_can_view_stock_overview()
    {
        $response = $this->actingAs($this->cashier)->get(route('stock.index'));

        $response->assertStatus(200);
        $response->assertSee('État Général des Stocks');
        $response->assertSee('Fungicide Super');
        $response->assertSee('Bidon 1L');
        $response->assertSee('Alerte Stock Bas');
    }

    public function test_can_view_stock_movements_history()
    {
        StockMovement::create([
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::PURCHASE,
            'quantity' => 10,
            'direction' => 'in',
            'created_by_user_id' => $this->admin->id,
            'movement_date' => now(),
            'notes' => 'Test movement',
        ]);

        $response = $this->actingAs($this->admin)->get(route('stock.movements'));

        $response->assertStatus(200);
        $response->assertSee('Historique Général des Mouvements');
        $response->assertSee('Fungicide Super');
        $response->assertSee('Test movement');
    }
}
