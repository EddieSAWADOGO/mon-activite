<?php

namespace Tests\Feature;

use App\Domain\Clients\Models\Customer;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\Ventes\Models\Sale;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Product $product;
    protected StockUnit $baseUnit;
    protected StockUnit $cartonUnit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->create(['role' => UserRole::CASHIER]);

        $this->product = Product::factory()->create(['name' => 'Pesticide Bio']);

        $this->baseUnit = StockUnit::create([
            'product_id' => $this->product->id,
            'name' => 'Bidon 1L',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'default_selling_price' => 5000,
            'low_stock_threshold' => 10,
            'current_stock' => 0, // 0 bidons in bulk initially
            'is_active' => true,
        ]);

        $this->cartonUnit = StockUnit::create([
            'product_id' => $this->product->id,
            'name' => 'Carton de 12',
            'is_base_unit' => false,
            'base_unit_equivalent' => 12,
            'default_selling_price' => 55000,
            'low_stock_threshold' => 2,
            'current_stock' => 5, // 5 cartons available
            'is_active' => true,
        ]);
    }

    public function test_sale_creation_requires_discount_reason_if_price_deviates(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('ventes.store'), [
            'sale_date' => now()->toDateTimeString(),
            'paid_amount' => 4000,
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'stock_unit_id' => $this->cartonUnit->id,
                    'quantity' => 1,
                    'unit_price' => 50000, // Default is 55000 -> 5000 deviation without discount_reason!
                    'discount_reason' => '',
                ]
            ]
        ]);

        $response->assertSessionHasErrors(['lines.0.discount_reason']);
    }

    public function test_sale_creation_with_direct_stock_deduction_and_invoice(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->cashier)->post(route('ventes.store'), [
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateTimeString(),
            'paid_amount' => 55000,
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'stock_unit_id' => $this->cartonUnit->id,
                    'quantity' => 1,
                    'unit_price' => 55000,
                    'discount_reason' => '',
                ]
            ]
        ]);

        $response->assertRedirect();

        // Check Sale created
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'total_amount' => 55000,
            'paid_amount' => 55000,
            'remaining_amount' => 0,
        ]);

        // Check Stock decremented
        $this->assertEquals(4, $this->cartonUnit->fresh()->current_stock);

        // Check StockMovement logged
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->cartonUnit->id,
            'type' => MovementType::SALE->value,
            'quantity' => 1,
            'direction' => 'out',
        ]);

        // Check Invoice generated
        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'total_amount' => 55000,
            'status' => 'paid',
        ]);
    }

    public function test_sale_with_breakage_opens_carton_and_reintegrates_reliquat(): void
    {
        // Target: sell 3 bidons 1L. Stock of bidons is 0, but 5 cartons of 12 are available.
        // We select source_stock_unit_id = cartonUnit->id.
        $response = $this->actingAs($this->cashier)->post(route('ventes.store'), [
            'sale_date' => now()->toDateTimeString(),
            'paid_amount' => 15000,
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'stock_unit_id' => $this->baseUnit->id,
                    'quantity' => 3,
                    'unit_price' => 5000,
                    'discount_reason' => '',
                    'source_stock_unit_id' => $this->cartonUnit->id, // Breakage!
                ]
            ]
        ]);

        $response->assertRedirect();

        // Check stock levels after breakage:
        // Carton stock: 5 - 1 = 4 cartons
        $this->assertEquals(4, $this->cartonUnit->fresh()->current_stock);

        // Base unit stock: 0 + 12 (equivalence) - 3 (sold) = 9 bidons!
        $this->assertEquals(9, $this->baseUnit->fresh()->current_stock);

        // Check StockMovements logged:
        // 1. BREAKAGE_OUT on carton (-1)
        $this->assertDatabaseHas('stock_movements', [
            'stock_unit_id' => $this->cartonUnit->id,
            'type' => MovementType::BREAKAGE_OUT->value,
            'quantity' => 1,
            'direction' => 'out',
        ]);

        // 2. BREAKAGE_IN on base unit (+12)
        $this->assertDatabaseHas('stock_movements', [
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::BREAKAGE_IN->value,
            'quantity' => 12,
            'direction' => 'in',
        ]);

        // 3. SALE on base unit (-3)
        $this->assertDatabaseHas('stock_movements', [
            'stock_unit_id' => $this->baseUnit->id,
            'type' => MovementType::SALE->value,
            'quantity' => 3,
            'direction' => 'out',
        ]);
    }
}
