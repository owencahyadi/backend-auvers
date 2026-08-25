<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\SupplierItem;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplierItem')->orderBy('purchase_date', 'desc');
        
        if ($request->has('month')) {
            $query->whereMonth('purchase_date', $request->month);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'supplier_item_id' => 'required|exists:supplier_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'new_price' => 'nullable|numeric' // Note: This variable now acts as the Custom TOTAL Price
        ]);

        $item = SupplierItem::findOrFail($request->supplier_item_id);

        if ($request->filled('new_price')) {
            // 1. Calculate the real Unit Price (Total Price / Quantity)
            $unitPrice = floatval($request->new_price) / floatval($request->quantity);
            
            // 2. Update the Supplier Catalog with the new Unit Price
            $item->price = $unitPrice;
            $item->save();
            
            // 3. The total price for this transaction is exactly what the user inputted
            $totalPrice = $request->new_price;
        } else {
            // If left blank, calculate normally using the existing catalog price
            $totalPrice = $item->price * $request->quantity;
        }

        $purchase = Purchase::create([
            'purchase_date' => $request->purchase_date,
            'supplier_item_id' => $request->supplier_item_id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
        ]);
        
        $purchase->load('supplierItem');

        return response()->json([
            'status' => 'success', 
            'message' => 'Purchase saved successfully!' . ($request->filled('new_price') ? ' (Catalog unit price updated)' : ''),
            'data' => $purchase
        ], 201);
    }

    public function destroy($id)
    {
        Purchase::findOrFail($id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase record deleted successfully.'
        ]);
    }
}