<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request, $id)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $id)
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);
            
        return response()->json($reviews);
    }
}
