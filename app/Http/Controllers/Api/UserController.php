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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // Jika role admin, store_id otomatis diset null
            'store_id' => $request->role === 'manager' ? $request->store_id : null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User account created successfully!',
            'data' => $user
        ]);
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