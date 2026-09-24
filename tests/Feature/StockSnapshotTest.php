<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\Stock\Services\StockSnapshotService;
use App\Models\User;
use App\Support\Enums\MovementType;
use App\Support\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockSnapshotTest extends TestCase
{
    use RefreshDatabase;

    private StockSnapshotService $service;
    private Product $product;
    private StockUnit $unit;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(StockSnapshotService::class);
        $this->user = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->product = Product::factory()->create(['is_active' => true]);
        $this->unit = StockUnit::factory()->create([
            'product_id' => $this->product->id,
            'is_base_unit' => true,
            'current_stock' => 50,
        ]);
    }

    public function test_can_generate_monthly_snapshot(): void
    {
        $count = $this->service->generateSnapshotForMonth(2026, 1);

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('stock_snapshots', [
            'year' => 2026,
            'month' => 1,
            'stock_unit_id' => $this->unit->id,
            'quantity' => 50,
        ]);
    }

    public function test_compute_stock_at_date_replays_movements_from_snapshot(): void
    {
        // 1. Generate snapshot at end of Jan 2026 with 50 units
        $this->service->generateSnapshotForMonth(2026, 1);

        // 2. Add movement in Feb 2026: Entry +20
        StockMovement::create([
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'type' => MovementType::PURCHASE,
            'quantity' => 20,
            'direction' => 'in',
            'reference_type' => 'test',
            'reference_id' => 1,
            'movement_date' => Carbon::create(2026, 2, 10, 10, 0, 0),
            'created_by_user_id' => $this->user->id,
        ]);

        // 3. Add movement in Feb 2026: Exit -5
        StockMovement::create([
            'product_id' => $this->product->id,
            'stock_unit_id' => $this->unit->id,
            'type' => MovementType::SALE,
            'quantity' => 5,
            'direction' => 'out',
            'reference_type' => 'test',
            'reference_id' => 2,
            'movement_date' => Carbon::create(2026, 2, 15, 14, 0, 0),
            'created_by_user_id' => $this->user->id,
        ]);

        // Compute stock at Feb 12 (before the exit of 5)
        $stockFeb12 = $this->service->computeStockAtDate($this->product, Carbon::create(2026, 2, 12));
        $qtyFeb12 = $stockFeb12->firstWhere('unit.id', $this->unit->id)['calculated_stock'];
        $this->assertEquals(70, $qtyFeb12); // 50 (snapshot Jan) + 20 = 70

        // Compute stock at Feb 20 (after the exit of 5)
        $stockFeb20 = $this->service->computeStockAtDate($this->product, Carbon::create(2026, 2, 20));
        $qtyFeb20 = $stockFeb20->firstWhere('unit.id', $this->unit->id)['calculated_stock'];
        $this->assertEquals(65, $qtyFeb20); // 50 + 20 - 5 = 65
    }
}
