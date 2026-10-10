<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockOpnameDetail>
 */
class StockOpnameDetailFactory extends Factory
{
    public function definition(): array
    {
        $systemStock = fake()->numberBetween(50, 200);

        return [
            'stock_opname_id' => StockOpname::factory(),
            'product_id'      => Product::factory(),
            'batch_id'        => null,
            'system_stock'    => $systemStock,
            'physical_stock'  => $systemStock,
            'difference'      => 0,
            'notes'           => null,
        ];
    }

    // ============================================================
    // STATE HELPERS
    // ============================================================

    /** State: sesuai (selisih 0) */
    public function matching(): static
    {
        return $this->state(function () {
            $stock = fake()->numberBetween(50, 200);
            return [
                'system_stock'   => $stock,
                'physical_stock' => $stock,
                'difference'     => 0,
            ];
        });
    }

    /** State: kurang (fisik < sistem) */
    public function shortage(int $difference = -5): static
    {
        return $this->state(function () use ($difference) {
            $system = fake()->numberBetween(100, 200);
            return [
                'system_stock'   => $system,
                'physical_stock' => $system + $difference,
                'difference'     => $difference,
            ];
        });
    }

    /** State: lebih (fisik > sistem) */
    public function surplus(int $difference = 5): static
    {
        return $this->state(function () use ($difference) {
            $system = fake()->numberBetween(50, 150);
            return [
                'system_stock'   => $system,
                'physical_stock' => $system + $difference,
                'difference'     => $difference,
            ];
        });
    }

    /** State: belum diisi fisik (null) */
    public function notCounted(): static
    {
        return $this->state(fn () => [
            'physical_stock' => null,
            'difference'     => 0,
        ]);
    }
}