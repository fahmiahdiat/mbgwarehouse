<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    /**
     * Daftar transaksi berdasarkan tipe.
     */
    public function index(Request $request, string $type)
    {
        $this->ensureValidType($type);

        $items = StockTransaction::query()
            ->with(['supplier', 'user'])
            ->ofType($type)
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('transactions.index', compact('items', 'type'));
    }

    /**
     * Form input transaksi baru.
     */
    public function create(string $type)
    {
        $this->ensureValidType($type);

        return view('transactions.form', [
            'type'      => $type,
            'products'  => Product::active()->with('unit')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan transaksi baru.
     */
    public function store(Request $request, string $type)
{
    $this->ensureValidType($type);

    $data = $request->validate([
        'date'                    => 'required|date',
        'supplier_id'             => 'nullable|exists:suppliers,id',
        'delivery_note'           => 'nullable|string|max:100',
        'recipient'               => 'nullable|string|max:100',
        'purpose'                 => 'nullable|string|max:200',
        'notes'                   => 'nullable|string',
        'items'                   => 'required|array|min:1',
        'items.*.product_id'      => 'required|exists:products,id',
        'items.*.qty'             => 'required|integer|min:1',
        'items.*.notes'           => 'nullable|string|max:200',
        'items.*.expired_date'    => 'nullable|date',
        'items.*.price'           => 'nullable|numeric|min:0',
    ]);

    $isOutgoing = in_array($type, [StockTransaction::TYPE_OUT, StockTransaction::TYPE_RETURN_SUPPLIER], true);

    // Validasi stok untuk tipe yang mengurangi
    if ($isOutgoing) {
        $errors = $this->validateStockAvailability($data['items']);
        if (! empty($errors)) {
            return back()->withInput()->with('error', implode(' | ', $errors));
        }
    }

    DB::transaction(function () use ($data, $type, $isOutgoing) {
        $trx = StockTransaction::create([
            'code'          => StockTransaction::generateCode($type),
            'type'          => $type,
            'date'          => $data['date'],
            'supplier_id'   => $data['supplier_id']   ?? null,
            'delivery_note' => $data['delivery_note'] ?? null,
            'recipient'     => $data['recipient']     ?? null,
            'purpose'       => $data['purpose']       ?? null,
            'notes'         => $data['notes']         ?? null,
            'user_id'       => auth()->id(),
        ]);

        $delta = StockTransaction::stockDelta($type);

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->has_batch) {
                if ($isOutgoing) {
                    // FEFO: ambil dari batch yang paling cepat expired
                    $allocation = \App\Services\BatchService::allocateFefo($product, $item['qty']);

                    foreach ($allocation as $alloc) {
                        // 1 baris detail per batch yang dipakai
                        $trx->details()->create([
                            'product_id' => $product->id,
                            'batch_id'   => $alloc['batch']->id,
                            'qty'        => $alloc['qty'],
                            'notes'      => $item['notes'] ?? null,
                        ]);

                        \App\Services\BatchService::reduceBatch($alloc['batch'], $alloc['qty']);
                    }
                } else {
                    // Barang masuk dengan batch → bikin batch baru
                    $batch = \App\Services\BatchService::createBatch(
                        $product,
                        $item['qty'],
                        $item['expired_date'] ?? null,
                        isset($item['price']) ? (float) $item['price'] : null,
                        'in',
                        $trx->id,
                        $item['notes'] ?? null
                    );

                    $trx->details()->create([
                        'product_id' => $product->id,
                        'batch_id'   => $batch->id,
                        'qty'        => $item['qty'],
                        'notes'      => $item['notes'] ?? null,
                    ]);

                    // stok sudah disync oleh createBatch, tapi panggil juga biar konsisten
                    // (createBatch sudah handle sync)
                }
            } else {
                // Produk tanpa batch → flow biasa
                $trx->details()->create([
                    'product_id' => $product->id,
                    'batch_id'   => null,
                    'qty'        => $item['qty'],
                    'notes'      => $item['notes'] ?? null,
                ]);

                $product->applyStockChange($delta * $item['qty']);
            }
        }

        ActivityLog::record(
            'Transaksi ' . $trx->type_label,
            "Membuat {$trx->type_label} {$trx->code}",
            $trx->code
        );
    });

    return redirect()->route('transactions.index', $type)
        ->with('success', 'Transaksi berhasil disimpan. Stok otomatis diperbarui.');
}
    /**
     * Detail transaksi.
     */
    public function show(StockTransaction $transaction)
    {
        $transaction->load([
            'details.product.unit',
            'supplier',
            'user',
        ]);

        return view('transactions.show', compact('transaction'));
    }

    // ============================================================
    // HELPER
    // ============================================================

    private function ensureValidType(string $type): void
    {
        $valid = [
            StockTransaction::TYPE_IN,
            StockTransaction::TYPE_OUT,
            StockTransaction::TYPE_RETURN_SUPPLIER,
            StockTransaction::TYPE_RETURN_CUSTOMER,
        ];

        abort_unless(in_array($type, $valid, true), 404);
    }

    /**
     * Cek stok cukup atau tidak. Return array of error messages.
     */
   private function validateStockAvailability(array $items): array
{
    $errors = [];

    // Gabungkan qty per produk
    $required = [];
    foreach ($items as $item) {
        $pid = $item['product_id'];
        $required[$pid] = ($required[$pid] ?? 0) + (int) $item['qty'];
    }

    $products = Product::whereIn('id', array_keys($required))->get()->keyBy('id');

    foreach ($required as $pid => $qty) {
        $product = $products[$pid] ?? null;
        if (! $product) continue;

        $available = $product->has_batch
            ? $product->activeBatches()->sum('qty_remaining')
            : $product->stock;

        if ($available < $qty) {
            $satuan = $product->unit?->symbol ?? '';
            $errors[] = "Stok {$product->name} tidak mencukupi. " .
                        "Tersedia: {$available} {$satuan}, diminta: {$qty} {$satuan}.";
        }
    }

    return $errors;
}
}