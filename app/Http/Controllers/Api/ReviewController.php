<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:1000',
            ]);

            $userId = $request->user()->id;

            \Illuminate\Support\Facades\Log::info('Creating review', [
                'user_id' => $userId,
                'product_id' => $product->id
            ]);

            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]);

            return response()->json($review->load('user'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validación fallida', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Review error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al guardar la valoración: ' . $e->getMessage()], 500);
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

    public function update(Request $request, $productId, Review $review)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No estás autorizado para editar esta valoración.'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json($review->load('user'));
    }

    public function destroy(Request $request, $productId, Review $review)
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No estás autorizado para eliminar esta valoración.'], 403);
        }

        $review->delete();

        return response()->noContent();
    }
}
