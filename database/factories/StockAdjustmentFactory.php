<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockAdjustment>
 */
class StockAdjustmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'       => 'ADJ-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('###'),
            'date'       => now()->toDateString(),
            'product_id' => Product::factory(),
            'batch_id'   => null,
            'adjustment' => -1,
            'reason'     => 'Barang rusak',
            'reference'  => null,
            'user_id'    => User::factory(),
        ];
    }

    // ============================================================
    // STATE HELPERS
    // ============================================================

    /** State: penyesuaian positif (tambah stok) */
    public function increase(int $qty = 5): static
    {
        return $this->state(fn () => [
            'adjustment' => $qty,
            'reason'     => 'Koreksi stok sistem',
        ]);
    }

    /** State: penyesuaian negatif (kurangi stok) */
    public function decrease(int $qty = 5): static
    {
        return $this->state(fn () => [
            'adjustment' => -$qty,
            'reason'     => 'Barang rusak',
        ]);
    }

    /** State: dari stock opname */
    public function fromOpname(string $soCode, int $difference): static
    {
        return $this->state(fn () => [
            'adjustment' => $difference,
            'reason'     => 'Selisih Stock Opname',
            'reference'  => $soCode,
        ]);
    }
}