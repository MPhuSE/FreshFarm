<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $userId = $request->user()->id;

        $orderItem = OrderItem::with('order')->findOrFail($request->order_item_id);

        if ($orderItem->order->user_id !== $userId) {
            return response()->json(['message' => 'Bạn không có quyền đánh giá sản phẩm này.'], 403);
        }

        if ($orderItem->order->status !== 'completed') {
            return response()->json(['message' => 'Bạn cần nhận hàng thành công trước khi có thể đánh giá.'], 403);
        }

        // Ensure user hasn't already reviewed this item
        $existingReview = Review::where('order_item_id', $orderItem->id)->first();

        if ($existingReview) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 400);
        }

        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $orderItem->product_id,
            'order_item_id' => $orderItem->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Đánh giá đã được gửi và đang chờ duyệt.', 'data' => $review], 201);
    }
}
