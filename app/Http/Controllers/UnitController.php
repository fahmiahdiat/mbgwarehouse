<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $items = Unit::withCount('products')->orderBy('name')->paginate(15);
        return view('master.units.index', compact('items'));
    }

    public function create()
    {
        return view('master.units.form', ['item' => new Unit]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:50|unique:units,name',
            'symbol' => 'required|string|max:20',
        ]);

        $unit = Unit::create($data);

        ActivityLog::record('Tambah Satuan', "Menambah satuan: {$unit->name} ({$unit->symbol})");

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('master.units.form', ['item' => $unit]);
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:50|unique:units,name,' . $unit->id,
            'symbol' => 'required|string|max:20',
        ]);

        $unit->update($data);

        ActivityLog::record('Edit Satuan', "Mengubah satuan: {$unit->name}");

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return back()->with('error',
                "Satuan '{$unit->name}' tidak bisa dihapus karena masih digunakan produk.");
        }

        $name = $unit->name;
        $unit->delete();

        ActivityLog::record('Hapus Satuan', "Menghapus satuan: {$name}");

        return back()->with('success', 'Satuan berhasil dihapus.');
    }
}