<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperationalNote;
use Illuminate\Http\Request;

class OperationalNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationalNote::query()->orderBy('id', 'asc');
        
        if ($request->filled('store_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('store_id', $request->store_id)
                  ->orWhereNull('store_id');
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'item' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'cut_off_time' => 'nullable|string|max:255',
            'delivery_day' => 'nullable|string|max:255',
            'minimum_order' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'noted' => 'nullable|string',
        ]);

        $note = OperationalNote::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully!',
            'data' => $note
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $note = OperationalNote::findOrFail($id);

        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'cut_off_time' => 'nullable|string|max:255',
            'delivery_day' => 'nullable|string|max:255',
            'minimum_order' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'noted' => 'nullable|string',
        ]);

        $note->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Note updated successfully!',
            'data' => $note
        ]);
    }

    public function destroy($id)
    {
        $note = OperationalNote::findOrFail($id);
        $note->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Note deleted successfully!'
        ]);
    }
}