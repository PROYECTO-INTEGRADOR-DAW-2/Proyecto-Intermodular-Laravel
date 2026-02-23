<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:1000',
            ]);

            \Illuminate\Support\Facades\Log::info('Creating review', ['user' => $request->user()->id, 'product' => $productId]);

            $review = Review::create([
                'user_id' => $request->user()->id,
                'product_id' => $productId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            return response()->json($review->load('user'), 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Review error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function userIndex(Request $request)
    {
        try {
            $reviews = $request->user()->reviews()->with('product')->latest()->get();
            return response()->json($reviews);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching reviews: ' . $e->getMessage()], 500);
        }
    }
}
