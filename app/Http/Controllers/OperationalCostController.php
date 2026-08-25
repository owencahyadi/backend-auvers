<?php

namespace App\Http\Controllers;

use App\Models\OperationalCost;
use Illuminate\Http\Request;

class OperationalCostController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationalCost::orderBy('date', 'desc');
        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $cost = OperationalCost::create($validated);
        return response()->json(['message' => 'Biaya operasional berhasil dicatat', 'data' => $cost], 201);
    }

    public function destroy($id)
    {
        OperationalCost::findOrFail($id)->delete();
        return response()->json(['message' => 'Data operasional berhasil dihapus']);
    }
}