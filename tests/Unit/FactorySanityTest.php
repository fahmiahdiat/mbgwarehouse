<?php

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockAdjustment;      // ← tambah
use App\Models\StockOpname;          // ← tambah
use App\Models\StockOpnameDetail;    // ← tambah
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;

// ============================================================
// BATCH 1 — MASTER DATA (existing)
// ============================================================

test('UserFactory bisa create user', function () {
    $user = User::factory()->create();
    expect($user->id)->toBeGreaterThan(0);
    expect($user->email)->not->toBeEmpty();
});

test('CategoryFactory bisa create kategori', function () {
    $cat = Category::factory()->create();
    expect($cat->id)->toBeGreaterThan(0);
    expect($cat->name)->not->toBeEmpty();
});

test('UnitFactory bisa create satuan', function () {
    $unit = Unit::factory()->create();
    expect($unit->id)->toBeGreaterThan(0);
    expect($unit->symbol)->not->toBeEmpty();
});

test('SupplierFactory bisa create supplier', function () {
    $supplier = Supplier::factory()->create();
    expect($supplier->id)->toBeGreaterThan(0);
    expect($supplier->name)->not->toBeEmpty();
});

test('LocationFactory bisa create lokasi', function () {
    $loc = Location::factory()->create();
    expect($loc->id)->toBeGreaterThan(0);
    expect($loc->code)->not->toBeEmpty();
});

test('ProductFactory bisa create barang', function () {
    $product = Product::factory()->create();
    expect($product->id)->toBeGreaterThan(0);
    expect($product->code)->toStartWith('BRG');
    expect($product->status)->toBe('active');
});

test('ProductFactory state lowStock menghasilkan stok <= min_stock', function () {
    $product = Product::factory()->lowStock()->create();
    expect($product->stock)->toBeLessThanOrEqual($product->min_stock);
    expect($product->stock)->toBeGreaterThan(0);
    expect($product->stock_status)->toBe('menipis');
});

test('ProductFactory state outOfStock menghasilkan stok 0', function () {
    $product = Product::factory()->outOfStock()->create();
    expect($product->stock)->toBe(0);
    expect($product->stock_status)->toBe('habis');
});

test('ProductFactory state withBatch menghasilkan has_batch true', function () {
    $product = Product::factory()->withBatch()->create();
    expect($product->has_batch)->toBeTrue();
});

test('ProductFactory otomatis membuat relasi', function () {
    $product = Product::factory()->create();
    expect($product->category)->toBeInstanceOf(Category::class);
    expect($product->unit)->toBeInstanceOf(Unit::class);
    expect($product->supplier)->toBeInstanceOf(Supplier::class);
    expect($product->location)->toBeInstanceOf(Location::class);
});

// ============================================================
// BATCH 2 — BATCH & TRANSACTION
// ============================================================

test('ProductBatchFactory bisa create batch', function () {
    $batch = ProductBatch::factory()->create();

    expect($batch->id)->toBeGreaterThan(0);
    expect($batch->batch_code)->not->toBeEmpty();
    expect($batch->qty_remaining)->toBe($batch->qty_initial);
    expect($batch->product_id)->not->toBeNull();
});

test('ProductBatchFactory otomatis membuat product dengan has_batch=true', function () {
    $batch = ProductBatch::factory()->create();
    expect($batch->product->has_batch)->toBeTrue();
});

test('ProductBatchFactory state expiringSoon expired dalam 30 hari', function () {
    $batch = ProductBatch::factory()->expiringSoon()->create();
    expect($batch->expiry_status)->toBe('segera');
    expect($batch->days_until_expired)->toBeLessThanOrEqual(30);
    expect($batch->days_until_expired)->toBeGreaterThanOrEqual(0);
});

test('ProductBatchFactory state expired menghasilkan status expired', function () {
    $batch = ProductBatch::factory()->expired()->create();
    expect($batch->expiry_status)->toBe('expired');
    expect($batch->days_until_expired)->toBeLessThan(0);
});

test('ProductBatchFactory state empty menghasilkan qty_remaining 0', function () {
    $batch = ProductBatch::factory()->empty()->create();
    expect($batch->qty_remaining)->toBe(0);
});

test('StockTransactionFactory default menghasilkan tipe in', function () {
    $trx = StockTransaction::factory()->create();
    expect($trx->type)->toBe(StockTransaction::TYPE_IN);
    expect($trx->code)->not->toBeEmpty();
});

test('StockTransactionFactory state incoming menghasilkan supplier & surat jalan', function () {
    $trx = StockTransaction::factory()->incoming()->create();
    expect($trx->type)->toBe(StockTransaction::TYPE_IN);
    expect($trx->supplier_id)->not->toBeNull();
    expect($trx->delivery_note)->not->toBeEmpty();
});

test('StockTransactionFactory state outgoing menghasilkan recipient & purpose', function () {
    $trx = StockTransaction::factory()->outgoing()->create();
    expect($trx->type)->toBe(StockTransaction::TYPE_OUT);
    expect($trx->recipient)->not->toBeEmpty();
    expect($trx->purpose)->not->toBeEmpty();
});

test('StockTransactionFactory state returnSupplier menghasilkan tipe return_supplier', function () {
    $trx = StockTransaction::factory()->returnSupplier()->create();
    expect($trx->type)->toBe(StockTransaction::TYPE_RETURN_SUPPLIER);
});

test('StockTransactionFactory state returnCustomer menghasilkan tipe return_customer', function () {
    $trx = StockTransaction::factory()->returnCustomer()->create();
    expect($trx->type)->toBe(StockTransaction::TYPE_RETURN_CUSTOMER);
});

test('StockTransactionDetailFactory bisa create detail', function () {
    $detail = StockTransactionDetail::factory()->create();

    expect($detail->id)->toBeGreaterThan(0);
    expect($detail->qty)->toBeGreaterThan(0);
    expect($detail->stock_transaction_id)->not->toBeNull();
    expect($detail->product_id)->not->toBeNull();
});

test('StockTransactionDetailFactory otomatis membuat transaksi & produk', function () {
    $detail = StockTransactionDetail::factory()->create();

    expect($detail->transaction)->toBeInstanceOf(StockTransaction::class);
    expect($detail->product)->toBeInstanceOf(Product::class);
});

// ============================================================
// BATCH 3 — STOCK OPNAME & ADJUSTMENT
// ============================================================

test('StockOpnameFactory bisa create SO', function () {
    $so = StockOpname::factory()->create();

    expect($so->id)->toBeGreaterThan(0);
    expect($so->code)->toStartWith('SO-');
    expect($so->status)->toBe(StockOpname::STATUS_COMPLETED);
    expect($so->user_id)->not->toBeNull();
});

test('StockOpnameFactory state draft menghasilkan status draft', function () {
    $so = StockOpname::factory()->draft()->create();
    expect($so->status)->toBe(StockOpname::STATUS_DRAFT);
});

test('StockOpnameFactory state at menghasilkan tanggal sesuai', function () {
    $so = StockOpname::factory()->at('2026-01-15')->create();

    // $so->date adalah Carbon object, jadi bandingkan hasil format-nya
    expect($so->date->format('Y-m-d'))->toBe('2026-01-15');
    expect($so->code)->toContain('20260115');
});

test('StockOpnameFactory accessor status label & badge bekerja', function () {
    $so = StockOpname::factory()->completed()->create();
    expect($so->status_label)->toBe('Selesai');
    expect($so->status_badge)->toContain('Selesai');

    $draft = StockOpname::factory()->draft()->create();
    expect($draft->status_label)->toBe('Draft');
});

test('StockOpnameDetailFactory bisa create detail', function () {
    $detail = StockOpnameDetail::factory()->create();

    expect($detail->id)->toBeGreaterThan(0);
    expect($detail->stock_opname_id)->not->toBeNull();
    expect($detail->product_id)->not->toBeNull();
});

test('StockOpnameDetailFactory state matching menghasilkan selisih 0', function () {
    $detail = StockOpnameDetail::factory()->matching()->create();

    expect($detail->system_stock)->toBe($detail->physical_stock);
    expect($detail->difference)->toBe(0);
    expect($detail->difference_label)->toBe('Sesuai');
});

test('StockOpnameDetailFactory state shortage menghasilkan selisih negatif', function () {
    $detail = StockOpnameDetail::factory()->shortage(-7)->create();

    expect($detail->difference)->toBe(-7);
    expect($detail->difference_label)->toBe('Kurang');
});

test('StockOpnameDetailFactory state surplus menghasilkan selisih positif', function () {
    $detail = StockOpnameDetail::factory()->surplus(10)->create();

    expect($detail->difference)->toBe(10);
    expect($detail->difference_label)->toBe('Lebih');
});

test('StockOpnameDetailFactory state notCounted menghasilkan fisik null', function () {
    $detail = StockOpnameDetail::factory()->notCounted()->create();
    expect($detail->physical_stock)->toBeNull();
});

test('StockOpnameDetailFactory booted() otomatis hitung difference', function () {
    // Coba create tanpa set difference → harus dihitung otomatis
    $detail = StockOpnameDetail::create([
        'stock_opname_id' => StockOpname::factory()->create()->id,
        'product_id'      => Product::factory()->create()->id,
        'system_stock'    => 100,
        'physical_stock'  => 95,
        // difference sengaja tidak di-set
    ]);

    expect($detail->fresh()->difference)->toBe(-5);
});

test('StockAdjustmentFactory bisa create adjustment', function () {
    $adj = StockAdjustment::factory()->create();

    expect($adj->id)->toBeGreaterThan(0);
    expect($adj->code)->toStartWith('ADJ-');
    expect($adj->adjustment)->toBe(-1);
    expect($adj->user_id)->not->toBeNull();
});

test('StockAdjustmentFactory state increase menghasilkan adjustment positif', function () {
    $adj = StockAdjustment::factory()->increase(10)->create();
    expect($adj->adjustment)->toBe(10);
    expect($adj->formatted_adjustment)->toBe('+10');
});

test('StockAdjustmentFactory state decrease menghasilkan adjustment negatif', function () {
    $adj = StockAdjustment::factory()->decrease(5)->create();
    expect($adj->adjustment)->toBe(-5);
    expect($adj->formatted_adjustment)->toBe('-5');
});

test('StockAdjustmentFactory state fromOpname mengisi reference', function () {
    $adj = StockAdjustment::factory()->fromOpname('SO-20261008-001', -5)->create();

    expect($adj->adjustment)->toBe(-5);
    expect($adj->reason)->toBe('Selisih Stock Opname');
    expect($adj->reference)->toBe('SO-20261008-001');
});