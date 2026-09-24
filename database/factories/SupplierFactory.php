<?php

namespace Database\Factories;

use App\Domain\Fournisseurs\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Fournisseurs\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'type' => 'company',
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'notes' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
