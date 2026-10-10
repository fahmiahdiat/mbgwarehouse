<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $items = Location::withCount('products')->orderBy('name')->paginate(15);
        return view('master.locations.index', compact('items'));
    }

    public function create()
    {
        return view('master.locations.form', ['item' => new Location]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'nullable|string|max:30',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $location = Location::create($data);

        ActivityLog::record('Tambah Lokasi', "Menambah lokasi: {$location->name}");

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('master.locations.form', ['item' => $location]);
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'code'        => 'nullable|string|max:30',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $location->update($data);

        ActivityLog::record('Edit Lokasi', "Mengubah lokasi: {$location->name}");

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->products()->exists()) {
            return back()->with('error',
                "Lokasi '{$location->name}' tidak bisa dihapus karena masih digunakan produk.");
        }

        $name = $location->name;
        $location->delete();

        ActivityLog::record('Hapus Lokasi', "Menghapus lokasi: {$name}");

        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}