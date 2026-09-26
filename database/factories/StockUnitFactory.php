<?php

namespace Database\Factories;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Produits\Models\StockUnit>
 */
class StockUnitFactory extends Factory
{
    protected $model = StockUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => 'Bidon',
            'is_base_unit' => true,
            'base_unit_equivalent' => 1.0000,
            'default_selling_price' => 1000,
            'low_stock_threshold' => 10,
            'current_stock' => 50,
            'is_active' => true,
        ];
    }
}
