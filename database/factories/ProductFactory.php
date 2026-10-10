<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'        => 'BRG' . str_pad(
                (string) fake()->unique()->numberBetween(1, 99999),
                5, '0', STR_PAD_LEFT
            ),
            'name'        => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category_id' => Category::factory(),
            'unit_id'     => Unit::factory(),
            'supplier_id' => Supplier::factory(),
            'location_id' => Location::factory(),
            'stock'       => 100,
            'min_stock'   => 20,
            'status'      => 'active',
            'has_batch'   => false,
        ];
    }

    // ============================================================
    // STATE HELPERS
    // ============================================================

    /** Barang aktif (default, tapi bisa eksplisit) */
    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    /** Barang nonaktif */
    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    /** Stok aman (stok > min_stock) */
    public function safeStock(): static
    {
        return $this->state(fn () => [
            'stock'     => 100,
            'min_stock' => 20,
        ]);
    }

    /** Stok menipis (0 < stok <= min_stock) */
    public function lowStock(): static
    {
        return $this->state(fn () => [
            'stock'     => 10,
            'min_stock' => 20,
        ]);
    }

    /** Stok habis (stok = 0) */
    public function outOfStock(): static
    {
        return $this->state(fn () => [
            'stock'     => 0,
            'min_stock' => 20,
        ]);
    }

    /** Barang dengan tracking batch */
    public function withBatch(): static
    {
        return $this->state(fn () => ['has_batch' => true]);
    }

    /** Stok 0, siap ditambahkan batch */
    public function emptyWithBatch(): static
    {
        return $this->state(fn () => [
            'stock'     => 0,
            'has_batch' => true,
        ]);
    }
}