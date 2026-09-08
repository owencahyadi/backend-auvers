<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Mengambil daftar seluruh user beserta data tokonya
    public function index()
    {
        $users = User::with('store')
                     ->select('id', 'name', 'email', 'role', 'store_id')
                     ->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    // Menyimpan user baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,manager',
            'store_id' => 'nullable|exists:stores,id',
            'visible_pages' => 'nullable|array' // Validasi input array
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'store_id' => $request->role === 'manager' ? $request->store_id : null,
            // Simpan array jika manager, jika admin biarkan null (karena admin bisa akses semua)
            'visible_pages' => $request->role === 'manager' ? $request->visible_pages : null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'User berhasil dibuat']);
    }

    // Menghapus user
// Menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // PERBAIKAN: Gunakan Auth::id() atau Auth::user()->id
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own active account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.'
        ]);
    }
}