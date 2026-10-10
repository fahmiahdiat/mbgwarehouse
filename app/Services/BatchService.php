<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BatchService
{
    /**
     * Buat batch baru untuk barang masuk.
     */
    public static function createBatch(
        Product $product,
        int $qty,
        ?string $expiredDate = null,
        ?float $price = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $notes = null
    ): ProductBatch {
        $entryDate = now();

        $batch = ProductBatch::create([
            'product_id'    => $product->id,
            'batch_code'    => ProductBatch::generateCode($product->code, $entryDate),
            'entry_date'    => $entryDate->toDateString(),
            'expired_date'  => $expiredDate,
            'qty_initial'   => $qty,
            'qty_remaining' => $qty,
            'price'         => $price,
            'source_type'   => $sourceType,
            'source_id'     => $sourceId,
            'notes'         => $notes,
        ]);

        // Sync product.stock = sum qty_remaining
        static::syncProductStock($product);

        return $batch;
    }

    /**
     * Alokasi FEFO — cari batch mana saja yang akan dikurangi.
     *
     * Return: array of ['batch' => ProductBatch, 'qty' => int]
     */
    public static function allocateFefo(Product $product, int $qty): array
    {
        $batches = $product->activeBatches()
                           ->fefo()
                           ->get();

        $remaining = $qty;
        $allocation = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $take = min($batch->qty_remaining, $remaining);

            $allocation[] = [
                'batch' => $batch,
                'qty'   => $take,
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            // Stok tidak cukup
            throw new \RuntimeException(
                "Stok {$product->name} tidak mencukupi. " .
                "Tersedia: " . ($qty - $remaining) . ", diminta: {$qty}."
            );
        }

        return $allocation;
    }

    /**
     * Kurangi qty batch tertentu.
     */
    public static function reduceBatch(ProductBatch $batch, int $qty): void
    {
        if ($batch->qty_remaining < $qty) {
            throw new \RuntimeException(
                "Batch {$batch->batch_code} tidak cukup. " .
                "Sisa: {$batch->qty_remaining}, diminta: {$qty}."
            );
        }

        $batch->qty_remaining -= $qty;
        $batch->save();

        static::syncProductStock($batch->product);
    }

    /**
     * Tambah qty batch tertentu (untuk retur gudang).
     */
    public static function addToBatch(ProductBatch $batch, int $qty): void
    {
        $batch->qty_remaining += $qty;
        $batch->save();

        static::syncProductStock($batch->product);
    }

    /**
     * Sync products.stock = sum(qty_remaining) dari semua batch.
     */
    public static function syncProductStock(Product $product): void
    {
        $total = $product->batches()->sum('qty_remaining');

        $product->stock = (int) $total;
        $product->save();
    }

    /**
     * Cek apakah stok total cukup (sum semua batch).
     */
    public static function hasEnoughStock(Product $product, int $qty): bool
    {
        if (! $product->has_batch) {
            return $product->stock >= $qty;
        }

        return $product->activeBatches()->sum('qty_remaining') >= $qty;
    }

    /**
     * Ambil ringkasan batch untuk 1 produk.
     */
    public static function getBatches(Product $product, bool $onlyAvailable = true)
    {
        $query = $product->batches();

        if ($onlyAvailable) {
            $query->available();
        }

        return $query->orderByRaw('expired_date IS NULL, expired_date ASC')
                     ->orderBy('entry_date')
                     ->get();
    }
}