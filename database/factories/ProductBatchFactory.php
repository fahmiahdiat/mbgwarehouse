<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductBatch>
 */
class ProductBatchFactory extends Factory
{
    public function definition(): array
    {
        $entryDate = now();
        $qty       = fake()->numberBetween(50, 200);

        return [
            'product_id'    => Product::factory()->withBatch(),
            'batch_code'    => strtoupper(fake()->unique()->bothify('BATCH-####??')),
            'entry_date'    => $entryDate->toDateString(),
            'expired_date'  => $entryDate->copy()->addMonths(12)->toDateString(),
            'qty_initial'   => $qty,
            'qty_remaining' => $qty,
            'price'         => fake()->numberBetween(5000, 50000),
            'source_type'   => 'in',
            'source_id'     => null,
            'notes'         => null,
        ];
    }

    // ============================================================
    // STATE HELPERS
    // ============================================================

    /** Batch yang akan expired dalam 15 hari */
    public function expiringSoon(): static
    {
        return $this->state(fn () => [
            'expired_date' => now()->addDays(15)->toDateString(),
        ]);
    }

    /** Batch yang sudah expired */
    public function expired(): static
    {
        return $this->state(fn () => [
            'expired_date' => now()->subDays(5)->toDateString(),
        ]);
    }

    /** Batch tanpa tanggal expired */
    public function noExpiry(): static
    {
        return $this->state(fn () => [
            'expired_date' => null,
        ]);
    }

    /** Batch yang sudah habis (qty_remaining = 0) */
    public function empty(): static
    {
        return $this->state(fn () => [
            'qty_remaining' => 0,
        ]);
    }
}