<?php

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Stock Opname — Create
|--------------------------------------------------------------------------
*/

test('halaman stock opname bisa dibuka user login', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/opnames')->assertOk();
});

test('form create SO bisa dibuka', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/opnames/create')->assertOk();
});

test('SO bisa dibuat dengan selisih', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [
            $product->id => 95, // fisik lebih sedikit
        ],
    ]);

    $so = StockOpname::first();
    expect($so)->not->toBeNull();
    expect($so->code)->toStartWith('SO-');
    expect($so->details()->count())->toBe(1);

    $detail = $so->details()->first();
    expect($detail->system_stock)->toBe(100);
    expect($detail->physical_stock)->toBe(95);
    expect($detail->difference)->toBe(-5);
});

test('SO dengan selisih positif tercatat', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 50]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 55],
    ]);

    $detail = StockOpname::first()->details()->first();
    expect($detail->difference)->toBe(5);
});

test('SO dengan stok sama punya selisih 0', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 100],
    ]);

    $detail = StockOpname::first()->details()->first();
    expect($detail->difference)->toBe(0);
});

test('SO tanpa isi fisik di-skip', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => null],
    ]);

    // Detail mungkin ada tapi fisik null
    $so = StockOpname::first();
    expect($so->details()->count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| Stock Opname — Apply Adjustment
|--------------------------------------------------------------------------
*/

test('apply adjustment memperbarui stok produk', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    // Buat SO dengan selisih -5
    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 95],
    ]);

    $so = StockOpname::first();

    // Apply
    $this->actingAs($user)->post("/opnames/{$so->id}/apply");

    expect($product->fresh()->stock)->toBe(95);
    expect(StockAdjustment::count())->toBe(1);
});

test('apply adjustment membuat adjustment dengan reference SO', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 105],
    ]);

    $so = StockOpname::first();
    $this->actingAs($user)->post("/opnames/{$so->id}/apply");

    $adj = StockAdjustment::first();
    expect($adj->adjustment)->toBe(5);
    expect($adj->reference)->toBe($so->code);
    expect($adj->reason)->toBe('Selisih Stock Opname');
});

test('apply adjustment skip detail dengan selisih 0', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 100], // sama
    ]);

    $so = StockOpname::first();
    $this->actingAs($user)->post("/opnames/{$so->id}/apply");

    expect(StockAdjustment::count())->toBe(0);
});

test('apply adjustment tidak dobel kalau diklik 2x', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 90],
    ]);

    $so = StockOpname::first();

    // Apply 2x
    $this->actingAs($user)->post("/opnames/{$so->id}/apply");
    $this->actingAs($user)->post("/opnames/{$so->id}/apply");

    // Cuma boleh ada 1 adjustment
    expect(StockAdjustment::count())->toBe(1);
    expect($product->fresh()->stock)->toBe(90);
});

/*
|--------------------------------------------------------------------------
| Validasi
|--------------------------------------------------------------------------
*/

test('SO butuh tanggal', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->post('/opnames', [
        'physical' => [$product->id => 100],
    ]);

    $response->assertSessionHasErrors('date');
});

test('SO mencatat activity log', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 100]);

    $this->actingAs($user)->post('/opnames', [
        'date' => now()->toDateString(),
        'physical' => [$product->id => 95],
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $user->id,
        'action'  => 'Stock Opname',
    ]);
});