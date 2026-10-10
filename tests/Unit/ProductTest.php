<?php

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;

/*
|--------------------------------------------------------------------------
| Accessor: stock_status
|--------------------------------------------------------------------------
*/

test('stock_status aman ketika stok > min_stock', function () {
    $product = Product::factory()->create(['stock' => 100, 'min_stock' => 50]);
    expect($product->stock_status)->toBe('aman');
});

test('stock_status menipis ketika 0 < stok <= min_stock', function () {
    $product = Product::factory()->create(['stock' => 30, 'min_stock' => 50]);
    expect($product->stock_status)->toBe('menipis');

    $exact = Product::factory()->create(['stock' => 50, 'min_stock' => 50]);
    expect($exact->stock_status)->toBe('menipis');
});

test('stock_status habis ketika stok = 0', function () {
    $product = Product::factory()->create(['stock' => 0, 'min_stock' => 50]);
    expect($product->stock_status)->toBe('habis');
});

test('stock_status habis ketika stok negatif', function () {
    $product = Product::factory()->create(['stock' => -5, 'min_stock' => 50]);
    expect($product->stock_status)->toBe('habis');
});

test('stock_status_label sesuai dengan status', function () {
    $aman = Product::factory()->create(['stock' => 100, 'min_stock' => 50]);
    expect($aman->stock_status_label)->toBe('Aman');

    $menipis = Product::factory()->create(['stock' => 10, 'min_stock' => 50]);
    expect($menipis->stock_status_label)->toBe('Menipis');

    $habis = Product::factory()->create(['stock' => 0, 'min_stock' => 50]);
    expect($habis->stock_status_label)->toBe('Habis');
});

test('stock_status_badge mengandung text status', function () {
    $aman = Product::factory()->create(['stock' => 100, 'min_stock' => 50]);
    expect($aman->stock_status_badge)->toContain('Aman');

    $menipis = Product::factory()->create(['stock' => 10, 'min_stock' => 50]);
    expect($menipis->stock_status_badge)->toContain('Menipis');

    $habis = Product::factory()->create(['stock' => 0, 'min_stock' => 50]);
    expect($habis->stock_status_badge)->toContain('Habis');
});

/*
|--------------------------------------------------------------------------
| Scope: lowStock, outOfStock, safeStock, active
|--------------------------------------------------------------------------
*/

test('scope lowStock hanya return barang dengan 0 < stok <= min_stock', function () {
    Product::factory()->create(['stock' => 100, 'min_stock' => 50]); // aman
    Product::factory()->create(['stock' => 30, 'min_stock' => 50]);  // menipis
    Product::factory()->create(['stock' => 10, 'min_stock' => 50]);  // menipis
    Product::factory()->create(['stock' => 0, 'min_stock' => 50]);   // habis

    expect(Product::lowStock()->count())->toBe(2);
});

test('scope outOfStock hanya return barang dengan stok <= 0', function () {
    Product::factory()->create(['stock' => 100, 'min_stock' => 50]); // aman
    Product::factory()->create(['stock' => 0, 'min_stock' => 50]);   // habis
    Product::factory()->create(['stock' => -5, 'min_stock' => 50]);  // habis

    expect(Product::outOfStock()->count())->toBe(2);
});

test('scope safeStock hanya return barang dengan stok > min_stock', function () {
    Product::factory()->create(['stock' => 100, 'min_stock' => 50]); // aman
    Product::factory()->create(['stock' => 30, 'min_stock' => 50]);  // menipis
    Product::factory()->create(['stock' => 0, 'min_stock' => 50]);   // habis

    expect(Product::safeStock()->count())->toBe(1);
});

test('scope active hanya return barang status active', function () {
    Product::factory()->create(['status' => 'active']);
    Product::factory()->create(['status' => 'active']);
    Product::factory()->create(['status' => 'inactive']);

    expect(Product::active()->count())->toBe(2);
});

test('scope search mencari di name & code', function () {
    Product::factory()->create(['name' => 'Beras Premium', 'code' => 'BRG001']);
    Product::factory()->create(['name' => 'Gula Pasir', 'code' => 'BRG002']);
    Product::factory()->create(['name' => 'Minyak Goreng', 'code' => 'MYK001']);

    expect(Product::search('Beras')->count())->toBe(1);
    expect(Product::search('BRG')->count())->toBe(2);
    expect(Product::search('MYK')->count())->toBe(1);
    expect(Product::search('tidak-ada')->count())->toBe(0);
});

test('scope bisa di-chain', function () {
    Product::factory()->create(['status' => 'active',  'stock' => 30, 'min_stock' => 50]); // match
    Product::factory()->create(['status' => 'active',  'stock' => 100, 'min_stock' => 50]); // aman
    Product::factory()->create(['status' => 'inactive', 'stock' => 30, 'min_stock' => 50]); // inactive

    $count = Product::active()->lowStock()->count();

    expect($count)->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Method: applyStockChange()
|--------------------------------------------------------------------------
*/

test('applyStockChange positif menambah stok', function () {
    $product = Product::factory()->create(['stock' => 100]);

    $product->applyStockChange(50);

    expect($product->fresh()->stock)->toBe(150);
});

test('applyStockChange negatif mengurangi stok', function () {
    $product = Product::factory()->create(['stock' => 100]);

    $product->applyStockChange(-30);

    expect($product->fresh()->stock)->toBe(70);
});

test('applyStockChange tidak membuat stok negatif', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $product->applyStockChange(-50);

    expect($product->fresh()->stock)->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Method: movements() — Kartu Stok
|--------------------------------------------------------------------------
*/

test('movements kosong kalau tidak ada transaksi', function () {
    $product = Product::factory()->create();

    expect($product->movements())->toHaveCount(0);
});

test('movements berisi transaksi masuk sebagai in', function () {
    $product = Product::factory()->create(['stock' => 0]);

    $trx = StockTransaction::factory()->incoming()->create();
    StockTransactionDetail::factory()->create([
        'stock_transaction_id' => $trx->id,
        'product_id'           => $product->id,
        'qty'                  => 50,
    ]);

    $movements = $product->movements();

    expect($movements)->toHaveCount(1);
    expect($movements[0]['in'])->toBe(50);
    expect($movements[0]['out'])->toBe(0);
    expect($movements[0]['type'])->toBe('in');
});

test('movements berisi transaksi keluar sebagai out', function () {
    $product = Product::factory()->create(['stock' => 100]);

    $trx = StockTransaction::factory()->outgoing()->create();
    StockTransactionDetail::factory()->create([
        'stock_transaction_id' => $trx->id,
        'product_id'           => $product->id,
        'qty'                  => 30,
    ]);

    $movements = $product->movements();

    expect($movements)->toHaveCount(1);
    expect($movements[0]['in'])->toBe(0);
    expect($movements[0]['out'])->toBe(30);
    expect($movements[0]['type'])->toBe('out');
});

test('movements menggabungkan transaksi & adjustment', function () {
    $product = Product::factory()->create(['stock' => 100]);

    $trx = StockTransaction::factory()->incoming()->create(['date' => now()->subDays(2)]);
    StockTransactionDetail::factory()->create([
        'stock_transaction_id' => $trx->id,
        'product_id'           => $product->id,
        'qty'                  => 50,
    ]);

    \App\Models\StockAdjustment::factory()->decrease(5)->create([
        'product_id' => $product->id,
        'date'       => now()->subDay(),
    ]);

    $movements = $product->movements();

    expect($movements)->toHaveCount(2);
});

test('movements diurutkan berdasarkan tanggal', function () {
    $product = Product::factory()->create(['stock' => 100]);

    // Transaksi lama
    $oldTrx = StockTransaction::factory()->incoming()->create(['date' => now()->subDays(10)]);
    StockTransactionDetail::factory()->create([
        'stock_transaction_id' => $oldTrx->id,
        'product_id'           => $product->id,
        'qty'                  => 10,
    ]);

    // Transaksi baru
    $newTrx = StockTransaction::factory()->incoming()->create(['date' => now()->subDay()]);
    StockTransactionDetail::factory()->create([
        'stock_transaction_id' => $newTrx->id,
        'product_id'           => $product->id,
        'qty'                  => 20,
    ]);

    $movements = $product->movements();

    expect($movements)->toHaveCount(2);
    expect($movements[0]['in'])->toBe(10); // yang lama duluan
    expect($movements[1]['in'])->toBe(20);
});