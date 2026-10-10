<?php

namespace App\Services;

use App\Models\Product;

class StockService
{
    /**
     * Terapkan perubahan stok ke produk.
     * Positif = tambah, negatif = kurangi.
     */
    public static function apply(Product $product, int $delta): void
    {
        $product->stock = max(0, $product->stock + $delta);
        $product->save();
    }

    /**
     * Hitung nilai delta berdasarkan tipe transaksi.
     * Return + untuk stok masuk, - untuk stok keluar.
     */
    public static function delta(string $type, int $qty): int
    {
        return match ($type) {
            'in', 'return_customer'  =>  $qty,
            'out', 'return_supplier' => -$qty,
            default                  =>  0,
        };
    }
}