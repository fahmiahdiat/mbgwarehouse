<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }

    /** State: kategori bahan pokok */
    public function bahanPokok(): static
    {
        return $this->state(fn () => [
            'name'        => 'Bahan Pokok',
            'description' => 'Beras, gula, tepung, minyak goreng, dan sejenisnya',
        ]);
    }

    /** State: kategori minuman */
    public function minuman(): static
    {
        return $this->state(fn () => [
            'name'        => 'Minuman',
            'description' => 'Air mineral, kopi, teh, dan sejenisnya',
        ]);
    }
}