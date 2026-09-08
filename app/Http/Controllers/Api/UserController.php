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
        $users = User::with('store')->orderBy('id', 'asc')->get();

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
            'visible_pages' => 'nullable|array' 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'store_id' => $request->role === 'manager' ? $request->store_id : null,
            'visible_pages' => $request->role === 'manager' ? $request->visible_pages : null,
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'New user account created successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User account not found.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, 
            'role' => 'required|in:admin,manager',
            'store_id' => 'nullable|exists:stores,id',
            'visible_pages' => 'nullable|array'
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'store_id' => $request->role === 'manager' ? $request->store_id : null,
            'visible_pages' => $request->role === 'manager' ? $request->visible_pages : null,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $dataToUpdate['password'] = bcrypt($request->password);
        }

        $user->update($dataToUpdate);

        return response()->json([
            'status' => 'success', 
            'message' => 'User account and permissions updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User account not found.'], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success', 
            'message' => 'User account deleted successfully!'
        ]);
    }
}