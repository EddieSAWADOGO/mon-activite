<?php

namespace Tests\Feature;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Supplier $supplier;
    protected Product $product;
    protected StockUnit $baseUnit;
    protected StockUnit $packUnit;

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

        $this->supplier = Supplier::factory()->create([
            'name' => 'Fournisseur SODEFA',
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Engrais NPK 15-15-15',
        ]);

        $this->baseUnit = StockUnit::create([
            'product_id' => $this->product->id,
            'name' => 'Sac 50kg',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'default_selling_price' => 22000,
            'low_stock_threshold' => 10,
            'current_stock' => 5,
        ]);

        $this->packUnit = StockUnit::create([
            'product_id' => $this->product->id,
            'name' => 'Palette 20 sacs',
            'is_base_unit' => false,
            'base_unit_equivalent' => 20,
            'default_selling_price' => 420000,
            'low_stock_threshold' => 2,
            'current_stock' => 1,
        ]);
    }

    public function test_admin_can_view_purchases_list()
    {
        $response = $this->actingAs($this->admin)->get(route('achats.index'));

        $response->assertStatus(200);
        $response->assertSee('Achats / Approvisionnements');
    }

    public function test_cashier_cannot_view_or_create_purchases()
    {
        $response = $this->actingAs($this->cashier)->get(route('achats.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->cashier)->get(route('achats.create'));
        $responseCreate->assertStatus(403);
    }

    public function test_admin_can_record_multi_line_purchase_and_update_stock()
    {
        $purchaseData = [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => '2026-01-15',
            'paid_amount' => 500000,
            'notes' => 'Livraison camion N° 4589',
            'lines' => [
                [
                    'product_id' => $this->product->id,
                    'stock_unit_id' => $this->baseUnit->id,
                    'quantity' => 10,
                    'unit_price' => 18000, // 180 000 FCFA
                ],
                [
                    'product_id' => $this->product->id,
                    'stock_unit_id' => $this->packUnit->id,
                    'quantity' => 2,
                    'unit_price' => 380000, // 760 000 FCFA
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('achats.store'), $purchaseData);

        // Should redirect to purchase show page
        $purchase = Purchase::latest()->first();
        $this->assertNotNull($purchase);
        $response->assertRedirect(route('achats.show', $purchase));

        // Check purchase details
        $this->assertEquals(940000, $purchase->total_amount); // 180 000 + 760 000
        $this->assertEquals(500000, $purchase->paid_amount);
        $this->assertEquals(440000, $purchase->remaining_amount);
        $this->assertCount(2, $purchase->lines);

        // Check stock incremented
        $this->assertEquals(15, (float) $this->baseUnit->fresh()->current_stock); // 5 + 10
        $this->assertEquals(3, (float) $this->packUnit->fresh()->current_stock); // 1 + 2

        // Check StockMovement created
        $movements = StockMovement::all();
        $this->assertCount(2, $movements);

        $movementBase = StockMovement::where('stock_unit_id', $this->baseUnit->id)->first();
        $this->assertEquals(MovementType::PURCHASE, $movementBase->type);
        $this->assertEquals(10, (float) $movementBase->quantity);
        $this->assertEquals('in', $movementBase->direction);
    }
}
