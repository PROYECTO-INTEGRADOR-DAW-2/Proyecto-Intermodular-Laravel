<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ReviewResource;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;




class ReviewController extends BaseController {


    public function getRewiewsFromProduct(Request $request) {

        $reviews = Review::where('product_id', $request->integer('product_id'));

        return ReviewResource::collection($reviews);
    }


    public function deleteRewiew(Review $review) {


    }

    public function addRewiew(ReviewRequest $request) {
        $validated = $request->validated();

        Review::create($validated);

        return $this->sendMessage('Se ha añadido la valoracion correctamente', 200);

    }

}

