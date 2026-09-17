<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::with('product')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return response()->json($inventories);
    }

    public function show($id)
    {
        $inventory = Inventory::with('product')->where('product_id', $id)->firstOrFail();

        return response()->json($inventory);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity_on_hand' => 'required|numeric|min:0',
        ]);

        $inventory = Inventory::where('product_id', $id)->firstOrFail();

        $inventory->quantity_on_hand = $request->quantity_on_hand;
        $inventory->save();

        return response()->json(['message' => 'Cập nhật tồn kho thành công', 'data' => $inventory]);
    }
}
