<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wishlists = Wishlist::with([
            'product' => function ($query) {
                $query->with(['category', 'inventory', 'primaryImage', 'images'])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews');
            },
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        $wishlists->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'product_id' => $item->product_id,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'product' => $item->product ? (new ProductResource($item->product))->resolve() : null,
            ];
        });

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
