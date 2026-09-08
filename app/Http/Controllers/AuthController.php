<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // 2. Coba autentikasi
        if (Auth::attempt($request->only('email', 'password'))) {
            
            $user = Auth::user();
            
            // 3. Buat Token Sanctum (Pengganti Session Cookie)
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token, // <-- Kirim token ke React
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'store_id' => $user->store_id,
                    'visible_pages' => $user->visible_pages // <-- TAMBAHKAN BARIS INI
                ]
            ]);
        }

        // 4. Jika gagal login
        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Menghapus (mencabut) token yang saat ini digunakan oleh user
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}