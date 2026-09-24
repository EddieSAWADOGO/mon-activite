<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Retours\Models\CustomerReturn;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReturnTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Product $product;
    private StockUnit $unit;

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
        $this->unit = StockUnit::factory()->create([
            'product_id' => $this->product->id,
            'is_base_unit' => true,
            'current_stock' => 10,
        ]);
    }

    public function test_admin_can_record_return_without_altering_stock(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('retours.store'), [
                'product_id' => $this->product->id,
                'stock_unit_id' => $this->unit->id,
                'quantity' => 5,
                'reason' => 'Produit non conforme',
                'return_date' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customer_returns', [
            'product_id' => $this->product->id,
            'quantity' => 5,
            'status' => 'pending',
        ]);

        // Stock MUST NOT change on return creation!
        $this->unit->refresh();
        $this->assertEquals(10, $this->unit->current_stock);
    }

    public function test_admin_can_validate_and_restock_return(): void
    {
        $return = CustomerReturn::create([
            'return_number' => 'RET-20260101-0001',
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'quantity' => 5,
            'reason' => 'Client insatisfait',
            'status' => 'pending',
            'return_date' => now(),
            'created_by_user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('retours.restock', $return));

        $response->assertRedirect(route('retours.show', $return));

        $return->refresh();
        $this->unit->refresh();

        $this->assertEquals('restocked', $return->status);
        $this->assertEquals($this->admin->id, $return->validated_by_user_id);
        $this->assertEquals(15, $this->unit->current_stock); // 10 + 5 = 15

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'type' => MovementType::RETURN->value,
            'quantity' => 5,
            'direction' => 'in',
        ]);
    }

    public function test_admin_can_validate_and_discard_return(): void
    {
        $return = CustomerReturn::create([
            'return_number' => 'RET-20260101-0002',
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'quantity' => 3,
            'reason' => 'Produit cassé',
            'status' => 'pending',
            'return_date' => now(),
            'created_by_user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('retours.discard', $return));

        $response->assertRedirect(route('retours.show', $return));

        $return->refresh();
        $this->unit->refresh();

        $this->assertEquals('discarded', $return->status);
        // Stock should NOT be incremented for discarded return
        $this->assertEquals(10, $this->unit->current_stock);

        $this->assertDatabaseHas('losses', [
            'customer_return_id' => $return->id,
            'quantity' => 3,
        ]);
    }

    public function test_cashier_cannot_access_or_create_returns(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('retours.index'));

        $response->assertStatus(403);
    }
}
