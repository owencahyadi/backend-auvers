<?php

namespace App\Http\Controllers;

use App\Models\SupplierItem;
use Illuminate\Http\Request;

class SupplierItemController extends Controller
{
    public function index()
    {
        // Mengambil semua barang, diurutkan berdasarkan kategori lalu nama barang
        $items = SupplierItem::orderBy('category')->orderBy('item_name')->get();
        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'item_name' => 'required|string',
            'measurement' => 'nullable|string',
            'price' => 'required|numeric',
            'price_per_item' => 'nullable|numeric',
            'supplier_name' => 'nullable|string',
        ]);

        $item = SupplierItem::create($validated);
        return response()->json(['message' => 'Barang berhasil ditambahkan', 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $item = SupplierItem::findOrFail($id);
        
        $validated = $request->validate([
            'category' => 'required|string',
            'item_name' => 'required|string',
            'measurement' => 'nullable|string',
            'price' => 'required|numeric',
            'price_per_item' => 'nullable|numeric',
            'supplier_name' => 'nullable|string',
        ]);

        $item->update($validated);
        return response()->json(['message' => 'Data barang diperbarui', 'data' => $item]);
    }

    public function destroy($id)
    {
        SupplierItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }

    public function getCategories()
    {
        // Mengambil daftar kategori unik yang ada di tabel supplier_items
        $categories = SupplierItem::select('category')->distinct()->pluck('category');
        
        return response()->json(['data' => $categories]);
    }

    // Update Massal Nama Kategori
    public function updateCategory(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string',
        ]);

        SupplierItem::where('category', $request->old_name)
                    ->update(['category' => $request->new_name]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category renamed for all items!'
        ]);
    }

    // Hapus Massal Kategori dan Seluruh Isinya
    public function destroyCategory($name)
    {
        SupplierItem::where('category', $name)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category and all its items deleted!'
        ]);
    }
}