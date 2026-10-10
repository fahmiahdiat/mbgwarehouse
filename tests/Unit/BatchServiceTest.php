<?php

use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\BatchService;

/*
|--------------------------------------------------------------------------
| BatchService::createBatch()
|--------------------------------------------------------------------------
*/

test('createBatch membuat batch baru dengan qty yang benar', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batch = BatchService::createBatch(
        product:     $product,
        qty:         100,
        expiredDate: now()->addYear()->toDateString(),
        price:       5000,
        sourceType:  'in',
    );

    expect($batch->id)->toBeGreaterThan(0);
    expect($batch->product_id)->toBe($product->id);
    expect($batch->qty_initial)->toBe(100);
    expect($batch->qty_remaining)->toBe(100);
    expect($batch->price)->toBe('5000.00');
    expect($batch->batch_code)->toContain($product->code);
});

test('createBatch otomatis sync stok produk', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    BatchService::createBatch($product, 100);

    expect($product->fresh()->stock)->toBe(100);
});

test('createBatch tanpa expired date tetap valid', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batch = BatchService::createBatch($product, 50, expiredDate: null);

    expect($batch->expired_date)->toBeNull();
    expect($batch->qty_remaining)->toBe(50);
});

test('createBatch dengan source_id menyimpan referensi', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batch = BatchService::createBatch(
        product:    $product,
        qty:        100,
        sourceType: 'in',
        sourceId:   99,
    );

    expect($batch->source_type)->toBe('in');
    expect($batch->source_id)->toBe(99);
});

test('createBatch multiple kali menumpuk stok produk', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    BatchService::createBatch($product, 100);
    BatchService::createBatch($product, 50);

    expect($product->fresh()->stock)->toBe(150);
    expect($product->batches()->count())->toBe(2);
});

/*
|--------------------------------------------------------------------------
| BatchService::allocateFefo()
|--------------------------------------------------------------------------
| Alokasi First Expired First Out.
*/

test('allocateFefo mengambil batch paling dekat expired duluan', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batchFar    = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(12)->toDateString(),
        'qty_initial'   => 50,
        'qty_remaining' => 50,
    ]);
    $batchNear   = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(3)->toDateString(), // ← paling dekat
        'qty_initial'   => 40,
        'qty_remaining' => 40,
    ]);

    $allocation = BatchService::allocateFefo($product, 30);

    expect($allocation)->toHaveCount(1);
    expect($allocation[0]['batch']->id)->toBe($batchNear->id);
    expect($allocation[0]['qty'])->toBe(30);
});

test('allocateFefo split ke beberapa batch kalau qty melebihi batch pertama', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batchNear = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(1)->toDateString(),
        'qty_remaining' => 30,
    ]);
    $batchFar = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(6)->toDateString(),
        'qty_remaining' => 100,
    ]);

    $allocation = BatchService::allocateFefo($product, 50);

    expect($allocation)->toHaveCount(2);
    expect($allocation[0]['batch']->id)->toBe($batchNear->id);
    expect($allocation[0]['qty'])->toBe(30);
    expect($allocation[1]['batch']->id)->toBe($batchFar->id);
    expect($allocation[1]['qty'])->toBe(20);
});

test('allocateFefo skip batch yang sudah habis', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    ProductBatch::factory()->empty()->create([
        'product_id'   => $product->id,
        'expired_date' => now()->addDays(1)->toDateString(), // expired lebih dekat
    ]);
    $activeBatch = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(6)->toDateString(),
        'qty_remaining' => 100,
    ]);

    $allocation = BatchService::allocateFefo($product, 20);

    expect($allocation)->toHaveCount(1);
    expect($allocation[0]['batch']->id)->toBe($activeBatch->id);
});

test('allocateFefo lempar exception kalau stok tidak cukup', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 10,
    ]);

    expect(fn () => BatchService::allocateFefo($product, 100))
        ->toThrow(\RuntimeException::class);
});

test('allocateFefo batch tanpa expired date diambil paling akhir', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batchWithExpiry = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'expired_date'  => now()->addMonths(12)->toDateString(),
        'qty_remaining' => 100,
    ]);
    $batchNoExpiry = ProductBatch::factory()->noExpiry()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 100,
    ]);

    $allocation = BatchService::allocateFefo($product, 20);

    expect($allocation[0]['batch']->id)->toBe($batchWithExpiry->id);
});

/*
|--------------------------------------------------------------------------
| BatchService::reduceBatch()
|--------------------------------------------------------------------------
*/

test('reduceBatch mengurangi qty_remaining', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 100]);
    $batch = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_initial'   => 100,
        'qty_remaining' => 100,
    ]);

    BatchService::reduceBatch($batch, 30);

    expect($batch->fresh()->qty_remaining)->toBe(70);
    expect($batch->fresh()->qty_initial)->toBe(100); // initial tidak berubah
});

test('reduceBatch sync stok produk', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 100]);
    $batch = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 100,
    ]);

    BatchService::reduceBatch($batch, 30);

    expect($product->fresh()->stock)->toBe(70);
});

test('reduceBatch gagal kalau qty melebihi sisa batch', function () {
    $product = Product::factory()->withBatch()->create();
    $batch = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 10,
    ]);

    expect(fn () => BatchService::reduceBatch($batch, 50))
        ->toThrow(\RuntimeException::class);

    expect($batch->fresh()->qty_remaining)->toBe(10); // tidak berubah
});

/*
|--------------------------------------------------------------------------
| BatchService::addToBatch()
|--------------------------------------------------------------------------
*/

test('addToBatch menambah qty_remaining', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 50]);
    $batch = ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 50,
    ]);

    BatchService::addToBatch($batch, 20);

    expect($batch->fresh()->qty_remaining)->toBe(70);
    expect($product->fresh()->stock)->toBe(70);
});

/*
|--------------------------------------------------------------------------
| BatchService::syncProductStock()
|--------------------------------------------------------------------------
*/

test('syncProductStock menghitung total dari semua batch', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 999]);

    ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 30,
    ]);
    ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 70,
    ]);

    BatchService::syncProductStock($product);

    expect($product->fresh()->stock)->toBe(100);
});

test('syncProductStock menghasilkan 0 kalau semua batch kosong', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 100]);

    ProductBatch::factory()->empty()->create(['product_id' => $product->id]);

    BatchService::syncProductStock($product);

    expect($product->fresh()->stock)->toBe(0);
});

/*
|--------------------------------------------------------------------------
| BatchService::hasEnoughStock()
|--------------------------------------------------------------------------
*/

test('hasEnoughStock true kalau total batch cukup', function () {
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    ProductBatch::factory()->create([
        'product_id'    => $product->id,
        'qty_remaining' => 100,
    ]);

    expect(BatchService::hasEnoughStock($product, 50))->toBeTrue();
    expect(BatchService::hasEnoughStock($product, 100))->toBeTrue();
    expect(BatchService::hasEnoughStock($product, 150))->toBeFalse();
});