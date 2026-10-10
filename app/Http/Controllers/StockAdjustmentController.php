<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $items = StockAdjustment::with(['product.unit', 'user'])
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('adjustments.index', compact('items'));
    }

    public function create()
    {
        return view('adjustments.form', [
            'products' => Product::with('unit')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'product_id' => 'required|exists:products,id',
            'adjustment' => 'required|integer|not_in:0',
            'reason'     => 'required|string|max:200',
            'reference'  => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Cek hasil akhir tidak negatif
        if ($product->stock + $data['adjustment'] < 0) {
            return back()->withInput()->with('error',
                "Penyesuaian akan membuat stok {$product->name} menjadi negatif. " .
                "Stok saat ini: {$product->stock}. Penyesuaian: {$data['adjustment']}.");
        }

        DB::transaction(function () use ($data, $product) {
            $adjustment = StockAdjustment::create([
                'code'       => StockAdjustment::generateCode(),
                'date'       => $data['date'],
                'product_id' => $product->id,
                'adjustment' => $data['adjustment'],
                'reason'     => $data['reason'],
                'reference'  => $data['reference'] ?? null,
                'user_id'    => auth()->id(),
            ]);

            $product->applyStockChange($data['adjustment']);

            ActivityLog::record(
                'Penyesuaian Stok',
                "Penyesuaian {$adjustment->code} untuk {$product->name}: " .
                "{$adjustment->formatted_adjustment}",
                $adjustment->code
            );
        });

        return redirect()->route('adjustments.index')
            ->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
}