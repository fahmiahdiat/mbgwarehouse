<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransactionDetail>
 */
class StockTransactionDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stock_transaction_id' => StockTransaction::factory(),
            'product_id'           => Product::factory(),
            'batch_id'             => null,
            'qty'                  => fake()->numberBetween(1, 50),
            'notes'                => null,
        ];
    }

    /** Detail dengan keterangan */
    public function withNotes(): static
    {
        return $this->state(fn () => [
            'notes' => fake()->sentence(4),
        ]);
    }
}