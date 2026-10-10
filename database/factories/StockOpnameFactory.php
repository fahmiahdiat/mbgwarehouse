<?php

namespace Database\Factories;

use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockOpname>
 */
class StockOpnameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'    => 'SO-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('###'),
            'date'    => now()->toDateString(),
            'status'  => StockOpname::STATUS_COMPLETED,
            'notes'   => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }

    /** State: draft (belum selesai) */
    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => StockOpname::STATUS_DRAFT,
        ]);
    }

    /** State: completed */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => StockOpname::STATUS_COMPLETED,
        ]);
    }

    /** State: SO tanggal tertentu */
    public function at(string $date): static
    {
        return $this->state(fn () => [
            'date' => $date,
            'code' => 'SO-' . date('Ymd', strtotime($date)) . '-' . fake()->unique()->numerify('###'),
        ]);
    }
}