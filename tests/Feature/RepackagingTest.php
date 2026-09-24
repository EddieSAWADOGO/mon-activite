<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepackagingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Product $product;
    private StockUnit $baseUnit;
    private StockUnit $cartonUnit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->cashier = User::factory()->create([
            'role' => UserRole::CASHIER,
        ]);

        $this->product = Product::factory()->create(['is_active' => true]);
        
        $this->baseUnit = StockUnit::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Bidon',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'current_stock' => 50,
        ]);

        $this->cartonUnit = StockUnit::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Carton de 12',
            'is_base_unit' => false,
            'base_unit_equivalent' => 12,
            'current_stock' => 2,
        ]);
    }

    public function test_admin_can_execute_valid_repackaging(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('reconditionnement.store'), [
                'product_id' => $this->product->id,
                'source_stock_unit_id' => $this->baseUnit->id,
                'source_quantity' => 24, // 24 bidons
                'target_stock_unit_id' => $this->cartonUnit->id,
                'target_quantity' => 2,   // 2 cartons of 12 (24 bidons)
                'repackaging_date' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect();

        $this->baseUnit->refresh();
        $this->cartonUnit->refresh();

        $this->assertEquals(26, $this->baseUnit->current_stock); // 50 - 24 = 26
        $this->assertEquals(4, $this->cartonUnit->current_stock); // 2 + 2 = 4

        $this->assertDatabaseHas('repackagings', [
            'product_id' => $this->product->id,
            'source_stock_unit_id' => $this->baseUnit->id,
            'source_quantity' => 24,
            'target_stock_unit_id' => $this->cartonUnit->id,
            'target_quantity' => 2,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::REPACKAGING_OUT->value,
            'quantity' => 24,
            'direction' => 'out',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'stock_unit_id' => $this->cartonUnit->id,
            'type' => MovementType::REPACKAGING_IN->value,
            'quantity' => 2,
            'direction' => 'in',
        ]);
    }

    public function test_repackaging_with_mismatched_equivalence_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('reconditionnement.store'), [
                'product_id' => $this->product->id,
                'source_stock_unit_id' => $this->baseUnit->id,
                'source_quantity' => 10, // 10 bidons
                'target_stock_unit_id' => $this->cartonUnit->id,
                'target_quantity' => 1,  // 1 carton = 12 bidons (10 != 12)
                'repackaging_date' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHas('error');

        $this->baseUnit->refresh();
        $this->cartonUnit->refresh();

        $this->assertEquals(50, $this->baseUnit->current_stock);
        $this->assertEquals(2, $this->cartonUnit->current_stock);
    }

    public function test_cashier_cannot_access_repackaging(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('reconditionnement.index'));

        $response->assertStatus(403);
    }
}
