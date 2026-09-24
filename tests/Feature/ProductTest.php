<?php

namespace Tests\Feature;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;

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
    }

    public function test_admin_can_view_product_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('produits.index'));

        $response->assertStatus(200);
        $response->assertViewIs('produits.index');
    }

    public function test_cashier_can_view_product_list_and_stock(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('produits.index'));

        $response->assertStatus(200);
        $response->assertViewIs('produits.index');
    }

    public function test_admin_can_create_product_with_base_unit_and_additional_units(): void
    {
        $payload = [
            'name' => 'Pesticide Delta',
            'description' => 'Traitement pour maïs',
            'base_unit_name' => 'Bidon',
            'base_unit_price' => 1500,
            'base_unit_low_stock_threshold' => 20,
            'base_unit_initial_stock' => 50,
            'additional_units' => [
                [
                    'name' => 'Carton de 12',
                    'base_unit_equivalent' => 12,
                    'default_selling_price' => 17000,
                    'low_stock_threshold' => 5,
                    'initial_stock' => 10,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('produits.store'), $payload);

        $product = Product::where('name', 'Pesticide Delta')->first();
        $this->assertNotNull($product);

        $response->assertRedirect(route('produits.show', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Pesticide Delta',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('stock_units', [
            'product_id' => $product->id,
            'name' => 'Bidon',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1.0000,
            'default_selling_price' => 1500,
            'low_stock_threshold' => 20.0000,
            'current_stock' => 50.0000,
        ]);

        $this->assertDatabaseHas('stock_units', [
            'product_id' => $product->id,
            'name' => 'Carton de 12',
            'is_base_unit' => false,
            'base_unit_equivalent' => 12.0000,
            'default_selling_price' => 17000,
            'low_stock_threshold' => 5.0000,
            'current_stock' => 10.0000,
        ]);
    }

    public function test_cashier_cannot_create_or_update_or_delete_product(): void
    {
        $responseCreate = $this->actingAs($this->cashier)->get(route('produits.create'));
        $responseCreate->assertStatus(403);

        $responseStore = $this->actingAs($this->cashier)->post(route('produits.store'), [
            'name' => 'Essai Cashier',
            'base_unit_name' => 'Pièce',
            'base_unit_price' => 500,
            'base_unit_low_stock_threshold' => 5,
        ]);
        $responseStore->assertStatus(403);

        $product = Product::factory()->create();

        $responseEdit = $this->actingAs($this->cashier)->get(route('produits.edit', $product));
        $responseEdit->assertStatus(403);

        $responseDelete = $this->actingAs($this->cashier)->delete(route('produits.destroy', $product));
        $responseDelete->assertStatus(403);
    }

    public function test_admin_can_update_product_and_add_new_units(): void
    {
        $product = Product::create([
            'name' => 'Maïs',
            'is_active' => true,
        ]);

        $baseUnit = StockUnit::create([
            'product_id' => $product->id,
            'name' => 'KG',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1.0000,
            'default_selling_price' => 300,
            'low_stock_threshold' => 100,
            'current_stock' => 500,
        ]);

        $payload = [
            'name' => 'Maïs Jaune Supérieur',
            'description' => 'Nouvelle description',
            'is_active' => 1,
            'existing_units' => [
                [
                    'id' => $baseUnit->id,
                    'name' => 'Kilogramme',
                    'default_selling_price' => 350,
                    'low_stock_threshold' => 120,
                ],
            ],
            'new_units' => [
                [
                    'name' => 'Sac de 50kg',
                    'base_unit_equivalent' => 50,
                    'default_selling_price' => 16500,
                    'low_stock_threshold' => 4,
                    'initial_stock' => 20,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('produits.update', $product), $payload);

        $response->assertRedirect(route('produits.show', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Maïs Jaune Supérieur',
            'description' => 'Nouvelle description',
        ]);

        $this->assertDatabaseHas('stock_units', [
            'id' => $baseUnit->id,
            'name' => 'Kilogramme',
            'default_selling_price' => 350,
            'low_stock_threshold' => 120.0000,
            'base_unit_equivalent' => 1.0000,
        ]);

        $this->assertDatabaseHas('stock_units', [
            'product_id' => $product->id,
            'name' => 'Sac de 50kg',
            'base_unit_equivalent' => 50.0000,
            'default_selling_price' => 16500,
        ]);
    }

    public function test_admin_can_toggle_unit_archived_status_but_not_base_unit(): void
    {
        $product = Product::create(['name' => 'Engrais', 'is_active' => true]);

        $baseUnit = StockUnit::create([
            'product_id' => $product->id,
            'name' => 'Sac 1kg',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1,
            'default_selling_price' => 1000,
        ]);

        $declaredUnit = StockUnit::create([
            'product_id' => $product->id,
            'name' => 'Sac 50kg',
            'is_base_unit' => false,
            'base_unit_equivalent' => 50,
            'default_selling_price' => 45000,
            'is_active' => true,
        ]);

        // Toggle non-base unit status
        $response = $this->actingAs($this->admin)->patch(route('produits.unites.toggle-status', [$product, $declaredUnit]));
        $response->assertRedirect();
        $this->assertDatabaseHas('stock_units', [
            'id' => $declaredUnit->id,
            'is_active' => false,
        ]);

        // Trying to toggle base unit should return error session
        $responseBase = $this->actingAs($this->admin)->patch(route('produits.unites.toggle-status', [$product, $baseUnit]));
        $responseBase->assertRedirect();
        $this->assertDatabaseHas('stock_units', [
            'id' => $baseUnit->id,
            'is_active' => true,
        ]);
    }
}
