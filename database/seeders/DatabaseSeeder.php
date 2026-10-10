<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Master data (urutan penting karena ada foreign key)
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            SupplierSeeder::class,
            LocationSeeder::class,
            ProductSeeder::class,

            // Transaksi & stock opname (bergantung pada master)
            StockTransactionSeeder::class,
            StockOpnameSeeder::class,
            StockAdjustmentSeeder::class,

            ProductBatchSeeder::class,
        ]);
    }
}
