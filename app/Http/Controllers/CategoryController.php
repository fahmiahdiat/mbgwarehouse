<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $items = Category::query()
            ->withCount('products')
            ->search($request->search)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('master.categories.index', compact('items'));
    }

    public function create()
    {
        return view('master.categories.form', ['item' => new Category]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($data);

        ActivityLog::record(
            'Tambah Kategori',
            "Menambah kategori: {$category->name}",
            "CAT-{$category->id}"
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('master.categories.form', ['item' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        ActivityLog::record(
            'Edit Kategori',
            "Mengubah kategori: {$category->name}",
            "CAT-{$category->id}"
        );

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;

        // Kalau kategori punya produk, jangan dihapus
        if ($category->products()->exists()) {
            return back()->with('error',
                "Kategori '{$name}' tidak bisa dihapus karena masih memiliki produk.");
        }

        $category->delete();

        ActivityLog::record('Hapus Kategori', "Menghapus kategori: {$name}");

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}