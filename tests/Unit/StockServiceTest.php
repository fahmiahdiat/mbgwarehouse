<?php

use App\Models\Product;
use App\Services\StockService;

/*
|--------------------------------------------------------------------------
| StockService::delta()
|--------------------------------------------------------------------------
| Menentukan nilai + atau - berdasarkan tipe transaksi.
*/

test('delta untuk tipe in mengembalikan nilai positif', function () {
    expect(StockService::delta('in', 10))->toBe(10);
    expect(StockService::delta('in', 100))->toBe(100);
});

test('delta untuk tipe out mengembalikan nilai negatif', function () {
    expect(StockService::delta('out', 10))->toBe(-10);
    expect(StockService::delta('out', 50))->toBe(-50);
});

test('delta untuk tipe return_supplier mengembalikan nilai negatif', function () {
    expect(StockService::delta('return_supplier', 5))->toBe(-5);
});

test('delta untuk tipe return_customer mengembalikan nilai positif', function () {
    expect(StockService::delta('return_customer', 5))->toBe(5);
});

test('delta untuk tipe tidak dikenal mengembalikan 0', function () {
    expect(StockService::delta('unknown', 10))->toBe(0);
    expect(StockService::delta('', 10))->toBe(0);
});

test('delta dengan qty 0 tetap mengembalikan 0', function () {
    expect(StockService::delta('in', 0))->toBe(0);
    expect(StockService::delta('out', 0))->toBe(0);
});

/*
|--------------------------------------------------------------------------
| StockService::apply()
|--------------------------------------------------------------------------
| Mengubah stok produk dan menyimpannya.
*/

test('apply dengan delta positif menambah stok', function () {
    $product = Product::factory()->create(['stock' => 100]);

    StockService::apply($product, 50);

    expect($product->fresh()->stock)->toBe(150);
});

test('apply dengan delta negatif mengurangi stok', function () {
    $product = Product::factory()->create(['stock' => 100]);

    StockService::apply($product, -30);

    expect($product->fresh()->stock)->toBe(70);
});

test('apply tidak membiarkan stok menjadi negatif', function () {
    $product = Product::factory()->create(['stock' => 10]);

    StockService::apply($product, -50);

    expect($product->fresh()->stock)->toBe(0);
});

test('apply dengan delta 0 tidak mengubah stok', function () {
    $product = Product::factory()->create(['stock' => 100]);

    StockService::apply($product, 0);

    expect($product->fresh()->stock)->toBe(100);
});

test('apply mempertahankan id & field lain', function () {
    $product = Product::factory()->create([
        'stock' => 100,
        'name'  => 'Beras Premium',
        'code'  => 'BRG-TEST-001',
    ]);

    StockService::apply($product, 25);
    $product->refresh();

    expect($product->id)->toBe($product->id);
    expect($product->name)->toBe('Beras Premium');
    expect($product->code)->toBe('BRG-TEST-001');
    expect($product->stock)->toBe(125);
});