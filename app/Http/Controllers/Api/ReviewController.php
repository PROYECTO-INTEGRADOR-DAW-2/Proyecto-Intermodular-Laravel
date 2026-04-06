<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;

use App\Http\Resources\ReviewResource;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;

use App\Models\Product;
use App\Models\Review;

use Illuminate\Support\Facades\Log;

class ReviewController extends BaseController {


    public function getReviewsFromProduct(Request $request, $product) {

        $productFinded = Product::find($product);

        if (!$productFinded) {
            return $this->sendError('Producto no encontrado', [], 404);
        }

        // Forma 1 de hacerlo | $reviews = Review::where('product_id', $product)->get();

        $reviews = $productFinded->reviews()->with('user')->get();

        $result = ReviewResource::collection($reviews);

        return $this->sendResponse($result, 'Reseñas obtenidas con exito');
    }


    public function deleteReview($product, $review) {

        $productFinded = Product::find($product);
        $reviewFinded = Review::find($review);

        if(!$reviewFinded && !$productFinded) {
            return $this->sendError('Producto o review no encontrados', [], 404);
        }

        $reviewFinded->delete();

        return $this->sendMessage('Se ha eliminado la valoracion correctamente', 200);

    }

    public function addReview(ReviewRequest $request, $product) {
        $productFinded = Product::find($product);

        if (!$productFinded) {
            return $this->sendError('Producto no encontrado', [], 404);
        }

        $validated = $request->validated();
        $validated['product_id'] = $product;
        $validated['user_id'] = $request->user()->id;

        Review::create($validated);

        return $this->sendMessage('Se ha añadido la valoracion correctamente', 200);

    }

    public function updateReview(ReviewRequest $request, $product, $review) {
        $productFinded = Product::find($product);
        $reviewFinded = Review::find($review);

        if (!$productFinded && !$reviewFinded) {
            return $this->sendError('Producto o review no encontrados', [], 404);
        }

        $validated = $request->validated();

        $reviewFinded->update($validated);

        return $this->sendMessage('Se ha actualizado la valoracion correctamente', 200);

    }

}

