<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($reviews);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,hidden',
        ]);

        $review = Review::findOrFail($id);
        $review->status = $request->status;
        $review->save();

        return response()->json(['message' => 'Cập nhật trạng thái thành công', 'data' => $review]);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Xóa đánh giá thành công']);
    }
}
