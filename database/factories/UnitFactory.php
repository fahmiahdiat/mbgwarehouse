<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'   => fake()->unique()->word(),
            'symbol' => strtoupper(fake()->lexify('???')),
        ];
    }

    public function kilogram(): static
    {
        return $this->state(fn () => ['name' => 'Kilogram', 'symbol' => 'Kg']);
    }

    public function liter(): static
    {
        return $this->state(fn () => ['name' => 'Liter', 'symbol' => 'L']);
    }

    public function pcs(): static
    {
        return $this->state(fn () => ['name' => 'Pieces', 'symbol' => 'Pcs']);
    }
}