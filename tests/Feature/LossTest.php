<?php

namespace Tests\Feature;

use App\Domain\Pertes\Models\Loss;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LossTest extends TestCase
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
            'current_stock' => 20,
        ]);
    }

    public function test_admin_can_record_loss_and_decrement_stock(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('pertes.store'), [
                'product_id' => $this->product->id,
                'stock_unit_id' => $this->unit->id,
                'quantity' => 4,
                'reason' => 'Produit périmé',
                'loss_date' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Péremption constatée lors de l\'inventaire',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('losses', [
            'product_id' => $this->product->id,
            'quantity' => 4,
            'reason' => 'Produit périmé',
        ]);

        $this->unit->refresh();
        $this->assertEquals(16, $this->unit->current_stock); // 20 - 4 = 16

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'type' => MovementType::LOSS->value,
            'quantity' => 4,
            'direction' => 'out',
        ]);
    }

    public function test_cashier_cannot_access_losses(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('pertes.index'));

        $response->assertStatus(403);
    }
}
