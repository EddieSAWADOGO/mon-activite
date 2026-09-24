<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['particulier', 'entreprise']);
        $isCompany = $type === 'entreprise';

        return [
            'type' => $type,
            'name' => $isCompany ? $this->faker->company() : $this->faker->name(),
            'contact_person' => $isCompany ? $this->faker->name() : null,
            'phone' => $this->faker->phoneNumber(),
            'whatsapp' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'ifu' => $isCompany ? $this->faker->numerify('1#############') : null,
            'rccm' => $isCompany ? $this->faker->bothify('RB/COT/## B ####') : null,
            'notes' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
