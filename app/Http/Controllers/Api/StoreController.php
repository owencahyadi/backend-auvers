<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store; // Pastikan model Store sudah ada
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        // Mengambil semua data toko (hanya id dan nama agar ringan)
        $stores = Store::select('id', 'name')->orderBy('id', 'asc')->get();       
        
        return response()->json([
            'status' => 'success',
            'data' => $stores
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $store = Store::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cabang baru berhasil ditambahkan!',
            'data' => $store
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $store = Store::find($id);
        if (!$store) {
            return response()->json(['status' => 'error', 'message' => 'Cabang tidak ditemukan'], 404);
        }

        $store->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Nama cabang berhasil diubah!'
        ]);
    }

    public function destroy($id)
    {
        $store = Store::find($id);
        
        if (!$store) {
            return response()->json(['status' => 'error', 'message' => 'Cabang tidak ditemukan'], 404);
        }

        $store->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cabang berhasil dihapus!'
        ]);
    }
}