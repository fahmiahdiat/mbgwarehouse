<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $items = Product::query()
            ->with(['category', 'unit', 'location', 'supplier'])
            ->search($request->search)
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->location, fn ($q) => $q->where('location_id', $request->location))
            ->when($request->status,   fn ($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('master.products.index', [
            'items'      => $items,
            'categories' => Category::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('master.products.form', [
            'item'       => new Product(['status' => 'active', 'min_stock' => 0]),
            'categories' => Category::orderBy('name')->get(),
            'units'      => Unit::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Stok baru selalu 0, diubah via transaksi
        $data['stock'] = 0;

        $product = Product::create($data);

        ActivityLog::record(
            'Tambah Barang',
            "Menambah barang: {$product->name} ({$product->code})",
            $product->code
        );

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil ditambahkan. Stok awal 0, silakan input lewat Barang Masuk.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'location', 'supplier']);
        return view('master.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('master.products.form', [
            'item'       => $product,
            'categories' => Category::orderBy('name')->get(),
            'units'      => Unit::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);

        // stok TIDAK boleh diubah lewat form edit
        unset($data['stock']);

        $product->update($data);

        ActivityLog::record(
            'Edit Barang',
            "Mengubah barang: {$product->name}",
            $product->code
        );

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Kalau sudah ada transaksi, jangan hapus, cukup nonaktifkan
        if ($product->transactionDetails()->exists()) {
            $product->update(['status' => 'inactive']);

            ActivityLog::record(
                'Nonaktifkan Barang',
                "Barang {$product->name} dinonaktifkan karena sudah memiliki transaksi",
                $product->code
            );

            return back()->with('success',
                "Barang '{$product->name}' tidak dihapus, tetapi dinonaktifkan karena sudah memiliki transaksi.");
        }

        $name = $product->name;
        $code = $product->code;
        $product->delete();

        ActivityLog::record('Hapus Barang', "Menghapus barang: {$name} ({$code})");

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Validasi input barang.
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code'        => 'required|string|max:50|unique:products,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            'name'        => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id'     => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'location_id' => 'nullable|exists:locations,id',
            'min_stock'   => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
            'has_batch'   => 'nullable|boolean',
        ]) + ['has_batch' => $request->boolean('has_batch')];
    }
}