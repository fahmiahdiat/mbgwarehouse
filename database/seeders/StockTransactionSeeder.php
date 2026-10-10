<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('email', 'admin@admin.com')->first();
        $budi       = User::where('email', 'budi@admin.com')->first();
        $supplier   = Supplier::where('name', 'PT Sumber Pangan Nusantara')->first();
        $supplier2  = Supplier::where('name', 'PT Tirta Minuman Segar')->first();

        if (! $admin || ! $supplier) {
            $this->command->warn('⚠️  Skip StockTransactionSeeder: user/supplier belum ada.');
            return;
        }

        // ============================================================
        // Definisi transaksi: [type, hari_lalu, user, supplier, meta, items[]]
        // ============================================================
        $transactions = [
            // ---- BARANG MASUK ----
            [
                'type'          => StockTransaction::TYPE_IN,
                'days_ago'      => 7,
                'user'          => $admin,
                'supplier'      => $supplier,
                'delivery_note' => 'SJ-2026-1001',
                'notes'         => 'Penerimaan rutin awal bulan',
                'items'         => [
                    ['code' => 'BRG001', 'qty' => 300],
                    ['code' => 'BRG002', 'qty' => 50],
                ],
            ],
            [
                'type'          => StockTransaction::TYPE_IN,
                'days_ago'      => 5,
                'user'          => $admin,
                'supplier'      => $supplier2,
                'delivery_note' => 'SJ-2026-1005',
                'notes'         => 'Restock minuman',
                'items'         => [
                    ['code' => 'BRG005', 'qty' => 100],
                    ['code' => 'BRG006', 'qty' => 30],
                ],
            ],
            [
                'type'          => StockTransaction::TYPE_IN,
                'days_ago'      => 3,
                'user'          => $budi,
                'supplier'      => $supplier,
                'delivery_note' => 'SJ-2026-1008',
                'notes'         => 'Pengiriman susulan',
                'items'         => [
                    ['code' => 'BRG001', 'qty' => 200],
                    ['code' => 'BRG004', 'qty' => 35],
                ],
            ],
            [
                'type'          => StockTransaction::TYPE_IN,
                'days_ago'      => 1,
                'user'          => $admin,
                'supplier'      => $supplier,
                'delivery_note' => 'SJ-2026-1010',
                'notes'         => '',
                'items'         => [
                    ['code' => 'BRG008', 'qty' => 100],
                    ['code' => 'BRG009', 'qty' => 50],
                ],
            ],

            // ---- BARANG KELUAR ----
            [
                'type'      => StockTransaction::TYPE_OUT,
                'days_ago'  => 6,
                'user'      => $budi,
                'recipient' => 'Divisi Dapur',
                'purpose'   => 'Kebutuhan operasional harian',
                'notes'     => '',
                'items'     => [
                    ['code' => 'BRG001', 'qty' => 50],
                    ['code' => 'BRG004', 'qty' => 10],
                ],
            ],
            [
                'type'      => StockTransaction::TYPE_OUT,
                'days_ago'  => 4,
                'user'      => $admin,
                'recipient' => 'Divisi Kantor',
                'purpose'   => 'ATK bulanan',
                'notes'     => '',
                'items'     => [
                    ['code' => 'BRG008', 'qty' => 30],
                    ['code' => 'BRG009', 'qty' => 20],
                ],
            ],
            [
                'type'      => StockTransaction::TYPE_OUT,
                'days_ago'  => 2,
                'user'      => $budi,
                'recipient' => 'Divisi Dapur',
                'purpose'   => 'Kebutuhan harian',
                'notes'     => '',
                'items'     => [
                    ['code' => 'BRG001', 'qty' => 30],
                    ['code' => 'BRG002', 'qty' => 10],
                ],
            ],
            [
                'type'      => StockTransaction::TYPE_OUT,
                'days_ago'  => 1,
                'user'      => $admin,
                'recipient' => 'Divisi Kebersihan',
                'purpose'   => 'Stok bulanan',
                'notes'     => '',
                'items'     => [
                    ['code' => 'BRG011', 'qty' => 10],
                ],
            ],

            // ---- RETUR KE SUPPLIER (stok keluar) ----
            [
                'type'          => StockTransaction::TYPE_RETURN_SUPPLIER,
                'days_ago'      => 2,
                'user'          => $admin,
                'supplier'      => $supplier,
                'delivery_note' => 'RET-001',
                'notes'         => 'Barang rusak saat pengiriman',
                'items'         => [
                    ['code' => 'BRG002', 'qty' => 5],
                ],
            ],

            // ---- RETUR KE GUDANG (stok masuk) ----
            [
                'type'      => StockTransaction::TYPE_RETURN_CUSTOMER,
                'days_ago'  => 1,
                'user'      => $budi,
                'recipient' => 'Divisi Kantor',
                'purpose'   => 'Kelebihan permintaan',
                'notes'     => 'Dikembalikan karena tidak terpakai',
                'items'     => [
                    ['code' => 'BRG009', 'qty' => 5],
                ],
            ],
        ];

        $totalCreated = 0;

        DB::transaction(function () use ($transactions, &$totalCreated) {
            foreach ($transactions as $trx) {
                $date = Carbon::now()->subDays($trx['days_ago']);
                $code = $this->buildCode($trx['type'], $date);

                $header = StockTransaction::create([
                    'code'          => $code,
                    'type'          => $trx['type'],
                    'date'          => $date->toDateString(),
                    'supplier_id'   => $trx['supplier']->id    ?? null,
                    'delivery_note' => $trx['delivery_note']   ?? null,
                    'recipient'     => $trx['recipient']       ?? null,
                    'purpose'       => $trx['purpose']         ?? null,
                    'notes'         => $trx['notes']           ?? null,
                    'user_id'       => $trx['user']->id,
                ]);

                $delta = StockTransaction::stockDelta($trx['type']);

                foreach ($trx['items'] as $item) {
                    $product = Product::where('code', $item['code'])->first();
                    if (! $product) continue;

                    $header->details()->create([
                        'product_id' => $product->id,
                        'qty'        => $item['qty'],
                        'notes'      => $item['notes'] ?? null,
                    ]);

                    // Update stok produk
                    $product->applyStockChange($delta * $item['qty']);
                }

                // Catat di activity log
                ActivityLog::create([
                    'user_id'     => $trx['user']->id,
                    'action'      => 'Transaksi ' . $header->type_label,
                    'description' => "Membuat {$header->type_label} {$code}",
                    'reference'   => $code,
                    'created_at'  => $date,
                    'updated_at'  => $date,
                ]);

                $totalCreated++;
            }
        });

        $this->command->info("✅ StockTransactions seeded: {$totalCreated}");
    }

    /**
     * Bikin kode unik per type + tanggal.
     */
    private function buildCode(string $type, Carbon $date): string
    {
        $prefix = match ($type) {
            StockTransaction::TYPE_IN              => 'BM',
            StockTransaction::TYPE_OUT             => 'BK',
            StockTransaction::TYPE_RETURN_SUPPLIER => 'RS',
            StockTransaction::TYPE_RETURN_CUSTOMER => 'RG',
        };

        $dateStr  = $date->format('Ymd');
        $existing = StockTransaction::where('code', 'like', "{$prefix}-{$dateStr}-%")->count();
        $seq      = $existing + 1;

        return sprintf('%s-%s-%03d', $prefix, $dateStr, $seq);
    }
}