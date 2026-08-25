<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StoreMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Tangkap ID Toko dari Header (dikirim oleh React nanti)
        $storeId = $request->header('X-Store-ID');

        // Jika ada ID Toko, simpan ke dalam memori global aplikasi
        if ($storeId) {
            app()->instance('active_store_id', $storeId);
        }

        return $next($request);
    }
}