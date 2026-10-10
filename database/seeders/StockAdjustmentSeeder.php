<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        if (! $admin) {
            $this->command->warn('⚠️  Skip StockAdjustmentSeeder: user admin belum ada.');
            return;
        }

        // Adjustment manual (di luar SO)
        $adjustments = [
            [
                'product_code' => 'BRG011',
                'days_ago'     => 1,
                'adjustment'   => -2,
                'reason'       => 'Kemasan bocor saat penyimpanan',
                'reference'    => null,
            ],
            [
                'product_code' => 'BRG009',
                'days_ago'     => 1,
                'adjustment'   => -1,
                'reason'       => 'Barang rusak (halaman sobek)',
                'reference'    => null,
            ],
        ];

        $total = 0;

        DB::transaction(function () use ($admin, $adjustments, &$total) {
            foreach ($adjustments as $adj) {
                $product = Product::where('code', $adj['product_code'])->first();
                if (! $product) continue;

                $date = Carbon::now()->subDays($adj['days_ago']);
                $code = $this->buildCode($date);

                StockAdjustment::create([
                    'code'       => $code,
                    'date'       => $date->toDateString(),
                    'product_id' => $product->id,
                    'adjustment' => $adj['adjustment'],
                    'reason'     => $adj['reason'],
                    'reference'  => $adj['reference'],
                    'user_id'    => $admin->id,
                ]);

                $product->applyStockChange($adj['adjustment']);

                ActivityLog::create([
                    'user_id'     => $admin->id,
                    'action'      => 'Penyesuaian Stok',
                    'description' => "Adjustment {$code}: {$adj['reason']}",
                    'reference'   => $code,
                    'created_at'  => $date,
                    'updated_at'  => $date,
                ]);

                $total++;
            }
        });

        $this->command->info("✅ StockAdjustments seeded: {$total}");
    }

    private function buildCode(Carbon $date): string
    {
        $dateStr = $date->format('Ymd');
        $count   = StockAdjustment::where('code', 'like', "ADJ-{$dateStr}-%")->count();
        return sprintf('ADJ-%s-%03d', $dateStr, $count + 1);
    }
}