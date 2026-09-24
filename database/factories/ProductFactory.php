<?php

namespace Database\Factories;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Produits\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if ($product->units()->count() === 0) {
                StockUnit::create([
                    'product_id' => $product->id,
                    'name' => 'Unité',
                    'is_base_unit' => true,
                    'base_unit_equivalent' => 1.0000,
                    'default_selling_price' => 1000,
                    'low_stock_threshold' => 10,
                    'current_stock' => 50,
                    'is_active' => true,
                ]);
            }
        });
    }
}
