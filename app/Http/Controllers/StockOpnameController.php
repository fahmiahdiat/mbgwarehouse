<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $items = StockOpname::with('user')
            ->withCount('details')
            ->latest('date')
            ->latest('id')
            ->paginate(15);

        return view('opnames.index', compact('items'));
    }

    public function create()
    {
        $products = Product::active()->with('unit')->orderBy('name')->get();
        return view('opnames.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'     => 'required|date',
            'notes'    => 'nullable|string',
            'physical' => 'required|array',
        ]);

        DB::transaction(function () use ($data) {
            $so = StockOpname::create([
                'code'    => StockOpname::generateCode(),
                'date'    => $data['date'],
                'status'  => StockOpname::STATUS_COMPLETED,
                'notes'   => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($data['physical'] as $productId => $val) {
                // Skip kalau kosong
                if ($val === null || $val === '') continue;

                $product = Product::find($productId);
                if (! $product) continue;

                $so->details()->create([
                    'product_id'     => $product->id,
                    'system_stock'   => $product->stock,
                    'physical_stock' => (int) $val,
                    // difference dihitung otomatis via booted() di model
                ]);
            }

            ActivityLog::record(
                'Stock Opname',
                "Membuat Stock Opname {$so->code}",
                $so->code
            );
        });

        return redirect()->route('opnames.index')
            ->with('success', 'Stock Opname berhasil disimpan.');
    }

    public function show(StockOpname $opname)
    {
        $opname->load([
            'details.product.unit',
            'user',
        ]);

        return view('opnames.show', compact('opname'));
    }

    public function destroy(StockOpname $opname)
    {
        // Cek: kalau sudah ada penyesuaian dari SO ini, tolak hapus
        if (StockAdjustment::where('reference', $opname->code)->exists()) {
            return back()->with('error',
                "Stock Opname {$opname->code} tidak bisa dihapus karena sudah memiliki penyesuaian stok terkait.");
        }

        $code = $opname->code;
        $opname->delete();

        ActivityLog::record('Hapus SO', "Menghapus Stock Opname {$code}");

        return back()->with('success', 'Stock Opname berhasil dihapus.');
    }

    /**
     * Buat penyesuaian otomatis dari semua selisih SO.
     */
    public function applyAdjustments(StockOpname $opname)
    {
        $opname->load('details.product');

        $created = 0;

        DB::transaction(function () use ($opname, &$created) {
            foreach ($opname->details as $detail) {
                if ($detail->difference === 0) continue;

                // Skip kalau sudah pernah dibuat
                $exists = StockAdjustment::where('reference', $opname->code)
                    ->where('product_id', $detail->product_id)
                    ->exists();
                if ($exists) continue;

                $adjustment = StockAdjustment::create([
                    'code'       => StockAdjustment::generateCode(),
                    'date'       => now()->toDateString(),
                    'product_id' => $detail->product_id,
                    'adjustment' => $detail->difference,
                    'reason'     => 'Selisih Stock Opname',
                    'reference'  => $opname->code,
                    'user_id'    => auth()->id(),
                ]);

                $detail->product->applyStockChange($detail->difference);
                $created++;
            }

            if ($created > 0) {
                ActivityLog::record(
                    'Penyesuaian Stok',
                    "Membuat {$created} penyesuaian dari SO {$opname->code}",
                    $opname->code
                );
            }
        });

        if ($created === 0) {
            return back()->with('error',
                'Tidak ada penyesuaian yang dibuat. Mungkin sudah pernah dibuat sebelumnya.');
        }

        return back()->with('success',
            "{$created} penyesuaian stok berhasil dibuat dan stok telah diperbarui.");
    }
}