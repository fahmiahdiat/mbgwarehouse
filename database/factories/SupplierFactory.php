<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'    => fake()->company(),
            'phone'   => fake()->numerify('021-#######'),
            'email'   => fake()->unique()->companyEmail(),
            'address' => fake()->address(),
            'pic'     => fake()->name(),
        ];
    }
}