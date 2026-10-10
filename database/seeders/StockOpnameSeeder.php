<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockAdjustment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockOpnameSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        if (! $admin) {
            $this->command->warn('⚠️  Skip StockOpnameSeeder: user admin belum ada.');
            return;
        }

        // Barang yang akan di-SO dengan stok fisik manual
        $opnameItems = [
            'BRG001' => 900,  // sistem 920, fisik 900 → -20
            'BRG004' => 60,   // sistem 60, fisik 60 → 0
            'BRG008' => 275,  // sistem 270, fisik 275 → +5
            'BRG010' => 3,    // sistem 3, fisik 3 → 0
        ];

        DB::transaction(function () use ($admin, $opnameItems) {
            $soDate = Carbon::now()->subDays(2);
            $code   = 'SO-' . $soDate->format('Ymd') . '-001';

            $so = StockOpname::create([
                'code'    => $code,
                'date'    => $soDate->toDateString(),
                'status'  => StockOpname::STATUS_COMPLETED,
                'notes'   => 'Stock opname rutin mingguan',
                'user_id' => $admin->id,
            ]);

            foreach ($opnameItems as $productCode => $physicalStock) {
                $product = Product::where('code', $productCode)->first();
                if (! $product) continue;

                $so->details()->create([
                    'product_id'     => $product->id,
                    'system_stock'   => $product->stock,
                    'physical_stock' => $physicalStock,
                    // 'difference' otomatis via booted() di model
                ]);
            }

            // Refresh relasi & buat adjustment untuk yang selisih
            $so->refresh();

            foreach ($so->details as $detail) {
                if ($detail->difference === 0) continue;

                $adjCode = 'ADJ-' . $soDate->format('Ymd') . '-' . str_pad(
                    (string) (StockAdjustment::whereDate('date', $soDate)->count() + 1),
                    3, '0', STR_PAD_LEFT
                );

                StockAdjustment::create([
                    'code'       => $adjCode,
                    'date'       => $soDate->toDateString(),
                    'product_id' => $detail->product_id,
                    'adjustment' => $detail->difference,
                    'reason'     => 'Selisih Stock Opname',
                    'reference'  => $so->code,
                    'user_id'    => $admin->id,
                ]);

                // Update stok produk
                $detail->product->applyStockChange($detail->difference);
            }

            // Log aktivitas
            ActivityLog::create([
                'user_id'     => $admin->id,
                'action'      => 'Stock Opname',
                'description' => "Membuat SO {$so->code}",
                'reference'   => $so->code,
                'created_at'  => $soDate,
                'updated_at'  => $soDate,
            ]);

            ActivityLog::create([
                'user_id'     => $admin->id,
                'action'      => 'Penyesuaian Stok',
                'description' => "Penyesuaian otomatis dari {$so->code}",
                'reference'   => $so->code,
                'created_at'  => $soDate,
                'updated_at'  => $soDate,
            ]);
        });

        $this->command->info('✅ StockOpname seeded: 1 SO + penyesuaian otomatis');
    }
}