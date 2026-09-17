<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->paginate(12);

        return response()->json($wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return response()->json(['message' => 'Đã xóa khỏi danh sách yêu thích', 'status' => 'removed']);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Đã thêm vào danh sách yêu thích', 'status' => 'added']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return response()->json(['message' => 'Đã xóa khỏi danh sách yêu thích', 'status' => 'removed']);
        }

        return response()->json(['message' => 'Không tìm thấy'], 404);
    }
}
