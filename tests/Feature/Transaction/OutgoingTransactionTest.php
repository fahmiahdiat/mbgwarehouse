<?php

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockTransaction;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Barang Keluar — Validasi Stok
|--------------------------------------------------------------------------
*/

test('barang keluar menolak qty melebihi stok', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'has_batch' => false]);

    $response = $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 100]],
    ]);

    $response->assertSessionHas('error');
    expect($product->fresh()->stock)->toBe(5); // tidak berubah
});

test('barang keluar mengurangi stok untuk non-batch', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100, 'has_batch' => false]);

    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 30]],
    ]);

    expect($product->fresh()->stock)->toBe(70);
});

test('barang keluar tepat sama dengan stok berhasil', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 50, 'has_batch' => false]);

    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 50]],
    ]);

    expect($product->fresh()->stock)->toBe(0);
});

test('barang keluar multi item memvalidasi semuanya', function () {
    $user = User::factory()->create();
    $p1 = Product::factory()->create(['stock' => 100]);
    $p2 = Product::factory()->create(['stock' => 5]); // ← tidak cukup

    $response = $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $p1->id, 'qty' => 10],  // OK
            ['product_id' => $p2->id, 'qty' => 50],  // GAGAL
        ],
    ]);

    $response->assertSessionHas('error');
    expect($p1->fresh()->stock)->toBe(100); // semuanya tidak berubah
    expect($p2->fresh()->stock)->toBe(5);
});

test('barang keluar mencatat kode BK & menyimpan penerima', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'recipient' => 'Divisi Dapur',
        'purpose' => 'Kebutuhan harian',
        'items' => [['product_id' => $product->id, 'qty' => 10]],
    ]);

    $trx = StockTransaction::first();
    expect($trx->code)->toStartWith('BK-' . now()->format('Ymd'));
    expect($trx->type)->toBe(StockTransaction::TYPE_OUT);
    expect($trx->recipient)->toBe('Divisi Dapur');
    expect($trx->purpose)->toBe('Kebutuhan harian');
});

/*
|--------------------------------------------------------------------------
| Barang Keluar dengan FEFO
|--------------------------------------------------------------------------
*/

test('barang keluar dengan batch pakai FEFO otomatis', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    // Batch A: expired 3 bulan
    $batchA = ProductBatch::factory()->create([
        'product_id' => $product->id,
        'expired_date' => now()->addMonths(3)->toDateString(),
        'qty_initial' => 50,
        'qty_remaining' => 50,
    ]);

    // Batch B: expired 12 bulan
    $batchB = ProductBatch::factory()->create([
        'product_id' => $product->id,
        'expired_date' => now()->addMonths(12)->toDateString(),
        'qty_initial' => 100,
        'qty_remaining' => 100,
    ]);

    // Sync stok total
    $product->update(['stock' => 150]);

    // Keluar 30 → harus dari batchA (expired lebih dekat)
    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 30]],
    ]);

    expect($batchA->fresh()->qty_remaining)->toBe(20);
    expect($batchB->fresh()->qty_remaining)->toBe(100); // tidak tersentuh
    expect($product->fresh()->stock)->toBe(120);
});

test('barang keluar batch besar split ke multiple batch FEFO', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $batchA = ProductBatch::factory()->create([
        'product_id' => $product->id,
        'expired_date' => now()->addMonths(1)->toDateString(),
        'qty_remaining' => 30,
    ]);

    $batchB = ProductBatch::factory()->create([
        'product_id' => $product->id,
        'expired_date' => now()->addMonths(6)->toDateString(),
        'qty_remaining' => 100,
    ]);

    $product->update(['stock' => 130]);

    // Keluar 50 → 30 dari A + 20 dari B
    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 50]],
    ]);

    expect($batchA->fresh()->qty_remaining)->toBe(0);
    expect($batchB->fresh()->qty_remaining)->toBe(80);
    expect($product->fresh()->stock)->toBe(80);
});

test('barang keluar batch yang tidak cukup ditolak', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    ProductBatch::factory()->create([
        'product_id' => $product->id,
        'qty_remaining' => 20,
    ]);
    $product->update(['stock' => 20]);

    $response = $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 100]],
    ]);

    $response->assertSessionHas('error');
    expect($product->fresh()->stock)->toBe(20);
});

/*
|--------------------------------------------------------------------------
| Activity Log
|--------------------------------------------------------------------------
*/

test('barang keluar mencatat activity log', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/transactions/out', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 10]],
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'action'  => 'Transaksi Barang Keluar',
    ]);
});