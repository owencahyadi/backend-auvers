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
}