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
        $stores = Store::select('id', 'name')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $stores
        ]);
    }
}