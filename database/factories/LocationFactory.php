<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'        => strtoupper(fake()->unique()->bothify('??#')),
            'name'        => 'Rak ' . fake()->unique()->bothify('?#'),
            'description' => fake()->sentence(),
        ];
    }
}