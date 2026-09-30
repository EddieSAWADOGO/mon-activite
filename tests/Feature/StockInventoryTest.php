<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInventoryTest extends TestCase
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

        $this->product = Product::factory()->create(['name' => 'Engrais NPK Test', 'is_active' => true]);
        $this->baseUnit = StockUnit::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Sac 50kg',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'current_stock' => 100,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_inventory_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stock.inventory.create'));
        $response->assertStatus(200);
    }

    public function test_cashier_cannot_access_inventory_page(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('stock.inventory.create'));
        $response->assertStatus(403);
    }

    public function test_admin_can_submit_inventory_adjustment_surplus(): void
    {
        $response = $this->actingAs($this->admin)->post(route('stock.inventory.store'), [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->baseUnit->id,
            'physical_quantity' => 105,
            'inventory_date' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Inventaire mensuel : surplus de 5 sacs',
        ]);

        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseHas('stock_units', [
            'id' => $this->baseUnit->id,
            'current_stock' => 105,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::INVENTORY_ADJUSTMENT->value,
            'quantity' => 5,
            'direction' => 'in',
        ]);
    }

    public function test_admin_can_submit_inventory_adjustment_deficit(): void
    {
        $response = $this->actingAs($this->admin)->post(route('stock.inventory.store'), [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->baseUnit->id,
            'physical_quantity' => 92,
            'inventory_date' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Inventaire physique : manquant de 8 sacs',
        ]);

        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseHas('stock_units', [
            'id' => $this->baseUnit->id,
            'current_stock' => 92,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::INVENTORY_ADJUSTMENT->value,
            'quantity' => 8,
            'direction' => 'out',
        ]);
    }
}
