<?php

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Barang Masuk — Tanpa Batch
|--------------------------------------------------------------------------
*/

test('guest tidak bisa simpan transaksi barang masuk', function () {
    $response = $this->post('/transactions/in', []);

    $response->assertRedirect('/login');
});

test('barang masuk menambah stok produk non-batch', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100, 'has_batch' => false]);

    $response = $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $product->id, 'qty' => 50],
        ],
    ]);

    $response->assertRedirect('/transactions/in');
    expect($product->fresh()->stock)->toBe(150);
});

test('barang masuk dengan qty besar menambah stok dengan benar', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 0, 'has_batch' => false]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $product->id, 'qty' => 500],
        ],
    ]);

    expect($product->fresh()->stock)->toBe(500);
});

test('barang masuk dengan multi item menambah semua stok', function () {
    $user = User::factory()->create();
    $p1 = Product::factory()->create(['stock' => 0]);
    $p2 = Product::factory()->create(['stock' => 0]);
    $p3 = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $p1->id, 'qty' => 10],
            ['product_id' => $p2->id, 'qty' => 20],
            ['product_id' => $p3->id, 'qty' => 30],
        ],
    ]);

    expect($p1->fresh()->stock)->toBe(10);
    expect($p2->fresh()->stock)->toBe(20);
    expect($p3->fresh()->stock)->toBe(30);
});

test('barang masuk mencatat kode transaksi BM-YYYYMMDD-XXX', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $product->id, 'qty' => 10],
        ],
    ]);

    $trx = StockTransaction::first();
    expect($trx->code)->toStartWith('BM-' . now()->format('Ymd'));
    expect($trx->type)->toBe(StockTransaction::TYPE_IN);
});

test('barang masuk menyimpan supplier & surat jalan', function () {
    $user = User::factory()->create();
    $supplier = Supplier::factory()->create();
    $product = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'supplier_id' => $supplier->id,
        'delivery_note' => 'SJ-2026-001',
        'items' => [
            ['product_id' => $product->id, 'qty' => 10],
        ],
    ]);

    $trx = StockTransaction::first();
    expect($trx->supplier_id)->toBe($supplier->id);
    expect($trx->delivery_note)->toBe('SJ-2026-001');
});

/*
|--------------------------------------------------------------------------
| Barang Masuk — Dengan Batch
|--------------------------------------------------------------------------
*/

test('barang masuk dengan batch membuat batch baru', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            [
                'product_id' => $product->id,
                'qty' => 100,
                'expired_date' => now()->addYear()->toDateString(),
                'price' => 5000,
            ],
        ],
    ]);

    expect($product->batches()->count())->toBe(1);
    expect($product->fresh()->stock)->toBe(100);

    $batch = $product->batches()->first();
    expect($batch->qty_initial)->toBe(100);
    expect($batch->qty_remaining)->toBe(100);
});

test('barang masuk dengan batch menyimpan tanggal expired', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);
    $expiredDate = now()->addMonths(6)->toDateString();

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            [
                'product_id' => $product->id,
                'qty' => 50,
                'expired_date' => $expiredDate,
                'price' => 12000,
            ],
        ],
    ]);

    $batch = $product->batches()->first();
    expect($batch->expired_date->format('Y-m-d'))->toBe($expiredDate);
    expect($batch->price)->toBe('12000.00');
});

test('barang masuk dengan batch berulang membuat multiple batch', function () {
    $user = User::factory()->create();
    $product = Product::factory()->withBatch()->create(['stock' => 0]);

    // Batch 1
    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $product->id, 'qty' => 100, 'expired_date' => now()->addYear()->toDateString()],
        ],
    ]);

    // Batch 2
    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [
            ['product_id' => $product->id, 'qty' => 50, 'expired_date' => now()->addMonths(6)->toDateString()],
        ],
    ]);

    expect($product->batches()->count())->toBe(2);
    expect($product->fresh()->stock)->toBe(150);
});

/*
|--------------------------------------------------------------------------
| Validasi Input
|--------------------------------------------------------------------------
*/

test('barang masuk butuh tanggal', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->post('/transactions/in', [
        'items' => [['product_id' => $product->id, 'qty' => 10]],
    ]);

    $response->assertSessionHasErrors('date');
});

test('barang masuk butuh minimal 1 item', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [],
    ]);

    $response->assertSessionHasErrors('items');
});

test('qty barang masuk minimal 1', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 0]],
    ]);

    $response->assertSessionHasErrors('items.0.qty');
});

test('product_id harus valid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => 99999, 'qty' => 10]],
    ]);

    $response->assertSessionHasErrors('items.0.product_id');
});

/*
|--------------------------------------------------------------------------
| Activity Log
|--------------------------------------------------------------------------
*/

test('barang masuk mencatat activity log', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)->post('/transactions/in', [
        'date' => now()->toDateString(),
        'items' => [['product_id' => $product->id, 'qty' => 10]],
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'action'  => 'Transaksi Barang Masuk',
    ]);
});