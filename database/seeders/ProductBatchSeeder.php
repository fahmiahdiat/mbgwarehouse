<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductBatchSeeder extends Seeder
{
    public function run(): void
    {
        // Produk yang butuh batch + set has_batch = true
        $batchProducts = [
            'BRG001' => ['expired_months' => 12, 'price' => 12000],  // Beras
            'BRG002' => ['expired_months' => 18, 'price' => 15000],  // Gula
            'BRG003' => ['expired_months' => 10, 'price' => 11000],  // Tepung
            'BRG004' => ['expired_months' => 15, 'price' => 18000],  // Minyak
            'BRG005' => ['expired_months' => null, 'price' => 20000],// Air Galon (no expired)
            'BRG006' => ['expired_months' => 12, 'price' => 25000],  // Kopi
            'BRG007' => ['expired_months' => 8,  'price' => 12000],  // Teh
        ];

        $created = 0;

        foreach ($batchProducts as $code => $config) {
            $product = Product::where('code', $code)->first();
            if (! $product) continue;

            // Mark product as has_batch
            $product->update(['has_batch' => true]);

            // Skip kalau sudah ada batch
            if ($product->batches()->exists()) continue;

            // Bikin 2 batch per produk dengan tanggal berbeda
            $entryDates = [
                Carbon::now()->subDays(30),
                Carbon::now()->subDays(10),
            ];

            $qtyPerBatch = (int) ($product->stock / count($entryDates));

            foreach ($entryDates as $i => $entryDate) {
                // Batch terakhir dapat sisa
                $qty = ($i === count($entryDates) - 1)
                    ? $product->stock - ($qtyPerBatch * $i)
                    : $qtyPerBatch;

                if ($qty <= 0) continue;

                $expiredDate = $config['expired_months']
                    ? $entryDate->copy()->addMonths($config['expired_months'])
                    : null;

                ProductBatch::create([
                    'product_id'    => $product->id,
                    'batch_code'    => ProductBatch::generateCode($product->code, $entryDate),
                    'entry_date'    => $entryDate->toDateString(),
                    'expired_date'  => $expiredDate?->toDateString(),
                    'qty_initial'   => $qty,
                    'qty_remaining' => $qty,
                    'price'         => $config['price'],
                    'source_type'   => 'initial',
                    'notes'         => 'Saldo awal sistem',
                ]);

                $created++;
            }
        }

        $this->command->info("✅ ProductBatches seeded: {$created}");
        $this->command->info("ℹ️  Products with has_batch=true: " . Product::where('has_batch', true)->count());
    }
}