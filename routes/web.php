<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMonitorController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Sistem Administrasi & Monitoring Gudang
| 1 role: Admin
|
*/

// Redirect root ke dashboard
Route::redirect('/', '/dashboard');

/*
|--------------------------------------------------------------------------
| Semua route butuh login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // =========================================================
    // DASHBOARD
    // =========================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================================================
    // MASTER DATA
    // =========================================================
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('units',      UnitController::class)->except(['show']);
    Route::resource('suppliers',  SupplierController::class)->except(['show']);
    Route::resource('locations',  LocationController::class)->except(['show']);
    Route::resource('products',   ProductController::class);

    // =========================================================
    // TRANSAKSI (Barang Masuk, Keluar, Retur)
    // =========================================================
    // Tipe yang diizinkan
    $types = ['in', 'out', 'return_supplier', 'return_customer'];

    Route::prefix('transactions')->name('transactions.')->group(function () use ($types) {
        Route::get('{type}',            [StockTransactionController::class, 'index'])
            ->name('index')
            ->whereIn('type', $types);

        Route::get('{type}/create',     [StockTransactionController::class, 'create'])
            ->name('create')
            ->whereIn('type', $types);

        Route::post('{type}',           [StockTransactionController::class, 'store'])
            ->name('store')
            ->whereIn('type', $types);
    });

    // Detail transaksi (dipisah supaya tidak bentrok dengan {type})
    Route::get('transactions/detail/{transaction}', [StockTransactionController::class, 'show'])
        ->name('transactions.show');

    // =========================================================
    // STOCK OPNAME
    // =========================================================
    Route::resource('opnames', StockOpnameController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::post('opnames/{opname}/apply', [StockOpnameController::class, 'applyAdjustments'])
        ->name('opnames.apply');

    // =========================================================
    // PENYESUAIAN STOK
    // =========================================================
    Route::resource('adjustments', StockAdjustmentController::class)
        ->only(['index', 'create', 'store']);

    // =========================================================
    // MONITORING
    // =========================================================
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/',              [StockMonitorController::class, 'index'])->name('index');
        Route::get('mutation',       [StockMonitorController::class, 'mutation'])->name('mutation');
        Route::get('card/{product}', [StockMonitorController::class, 'card'])->name('card');
        Route::get('batch/{product}', [StockMonitorController::class, 'batches'])->name('batches');
    });

    // =========================================================
    // LAPORAN
    // =========================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        // Preview / halaman HTML
        Route::get('stock',      [ReportController::class, 'stock'])->name('stock');
        Route::get('in',         [ReportController::class, 'in'])->name('in');
        Route::get('out',        [ReportController::class, 'out'])->name('out');
        Route::get('opname',     [ReportController::class, 'opname'])->name('opname');
        Route::get('adjustment', [ReportController::class, 'adjustment'])->name('adjustment');

        // Export CSV/Excel
        Route::get('stock/export',      [ReportController::class, 'exportStock'])->name('stock.export');
        Route::get('in/export',         [ReportController::class, 'exportIn'])->name('in.export');
        Route::get('out/export',        [ReportController::class, 'exportOut'])->name('out.export');
        Route::get('opname/export',     [ReportController::class, 'exportOpname'])->name('opname.export');
        Route::get('adjustment/export', [ReportController::class, 'exportAdjustment'])->name('adjustment.export');

        Route::get('expired',          [ReportController::class, 'expired'])->name('expired');
Route::get('batch',            [ReportController::class, 'batch'])->name('batch');
Route::get('expired/export',   [ReportController::class, 'exportExpired'])->name('expired.export');
Route::get('batch/export',     [ReportController::class, 'exportBatch'])->name('batch.export');
    });

    // =========================================================
    // RIWAYAT AKTIVITAS
    // =========================================================
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // =========================================================
    // PROFIL (dari Breeze — biarkan tetap ada)
    // =========================================================
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth routes (login, register, logout, dll) — dari Breeze
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';