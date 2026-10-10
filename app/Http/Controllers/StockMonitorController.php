<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use Illuminate\Http\Request;


class StockMonitorController extends Controller
{
    /**
     * Halaman "Stok Barang".
     */
    public function index(Request $request)
    {
        $items = Product::query()
            ->active()
            ->with(['category', 'unit', 'location'])
            ->search($request->search)
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filter === 'aman',    fn ($q) => $q->safeStock())
            ->when($request->filter === 'menipis', fn ($q) => $q->lowStock())
            ->when($request->filter === 'habis',   fn ($q) => $q->outOfStock())
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('stocks.index', [
            'items'      => $items,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function batches(Product $product)
{
    $product->load('unit', 'category');

    $batches = $product->batches()
        ->orderByRaw('expired_date IS NULL, expired_date ASC')
        ->orderBy('entry_date')
        ->get();

    return view('stocks.batches', compact('product', 'batches'));
}


    /**
     * Kartu Stok — pergerakan satu barang dengan saldo.
     */
    public function card(Request $request, Product $product)
    {
        $product->load(['unit', 'category', 'location']);

        $movements = $product->movements();

        // Filter tanggal
        if ($request->from) {
            $movements = $movements->filter(fn ($m) => $m['date'] >= $request->from);
        }
        if ($request->to) {
            $movements = $movements->filter(fn ($m) => $m['date'] <= $request->to);
        }

        // Hitung saldo berjalan
        $saldo = 0;
        $rows  = [];
        foreach ($movements as $m) {
            $saldo += $m['in'] - $m['out'];
            $rows[] = $m + ['saldo' => $saldo];
        }

        return view('stocks.card', compact('product', 'rows'));
    }

    /**
     * Mutasi Barang — ringkasan pergerakan semua barang dalam periode.
     */
    public function mutation(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        // Ringkasan qty
        $in       = $this->sumTransactionQty(StockTransaction::TYPE_IN, $from, $to);
        $out      = $this->sumTransactionQty(StockTransaction::TYPE_OUT, $from, $to);
        $retSup   = $this->sumTransactionQty(StockTransaction::TYPE_RETURN_SUPPLIER, $from, $to);
        $retCust  = $this->sumTransactionQty(StockTransaction::TYPE_RETURN_CUSTOMER, $from, $to);
        $adj      = (int) StockAdjustment::whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->sum('adjustment');

        // Detail baris
        $details = StockTransaction::with(['details.product.unit', 'supplier'])
            ->betweenDate($from, $to)
            ->when($request->type, fn ($q) => $q->ofType($request->type))
            ->latest('date')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('stocks.mutation', compact(
            'from', 'to', 'in', 'out', 'retSup', 'retCust', 'adj', 'details'
        ));
    }

    // ============================================================
    // HELPER
    // ============================================================

    private function sumTransactionQty(string $type, string $from, string $to): int
    {
        return (int) StockTransaction::ofType($type)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->with('details')
            ->get()
            ->flatMap->details
            ->sum('qty');
    }

    
}