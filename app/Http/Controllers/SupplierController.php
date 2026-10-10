<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $items = Supplier::query()
            ->search($request->search)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('master.suppliers.index', compact('items'));
    }

    public function create()
    {
        return view('master.suppliers.form', ['item' => new Supplier]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'pic'     => 'nullable|string|max:100',
        ]);

        $supplier = Supplier::create($data);

        ActivityLog::record('Tambah Supplier', "Menambah supplier: {$supplier->name}");

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('master.suppliers.form', ['item' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'pic'     => 'nullable|string|max:100',
        ]);

        $supplier->update($data);

        ActivityLog::record('Edit Supplier', "Mengubah supplier: {$supplier->name}");

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        // Kalau supplier masih dipakai transaksi, nonaktifkan saja (skip karena tidak ada field status)
        // Untuk sekarang cek dulu: kalau ada transaksi, tolak hapus
        if ($supplier->stockTransactions()->exists()) {
            return back()->with('error',
                "Supplier '{$supplier->name}' tidak bisa dihapus karena masih ada transaksi terkait.");
        }

        $name = $supplier->name;
        $supplier->delete();

        ActivityLog::record('Hapus Supplier', "Menghapus supplier: {$name}");

        return back()->with('success', 'Supplier berhasil dihapus.');
    }
}