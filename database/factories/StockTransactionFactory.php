<?php

namespace Database\Factories;

use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'          => 'TRX-' . strtoupper(fake()->unique()->bothify('####??')),
            'type'          => StockTransaction::TYPE_IN,
            'date'          => now()->toDateString(),
            'supplier_id'   => null,
            'delivery_note' => null,
            'recipient'     => null,
            'purpose'       => null,
            'notes'         => fake()->optional()->sentence(),
            'user_id'       => User::factory(),
        ];
    }

    // ============================================================
    // STATE HELPERS
    // ============================================================

    /** Transaksi barang masuk */
    public function incoming(): static
    {
        return $this->state(fn () => [
            'type'          => StockTransaction::TYPE_IN,
            'supplier_id'   => Supplier::factory(),
            'delivery_note' => 'SJ-' . strtoupper(fake()->bothify('####')),
        ]);
    }

    /** Transaksi barang keluar */
    public function outgoing(): static
    {
        return $this->state(fn () => [
            'type'          => StockTransaction::TYPE_OUT,
            'supplier_id'   => null,
            'delivery_note' => null,
            'recipient'     => fake()->randomElement([
                'Divisi Dapur', 'Divisi Kantor', 'Divisi Kebersihan',
            ]),
            'purpose'       => fake()->sentence(3),
        ]);
    }

    /** Retur ke supplier */
    public function returnSupplier(): static
    {
        return $this->state(fn () => [
            'type'          => StockTransaction::TYPE_RETURN_SUPPLIER,
            'supplier_id'   => Supplier::factory(),
            'delivery_note' => 'RET-' . strtoupper(fake()->bothify('####')),
        ]);
    }

    /** Retur ke gudang */
    public function returnCustomer(): static
    {
        return $this->state(fn () => [
            'type'      => StockTransaction::TYPE_RETURN_CUSTOMER,
            'recipient' => 'Divisi Kantor',
            'purpose'   => 'Kelebihan permintaan',
        ]);
    }
}