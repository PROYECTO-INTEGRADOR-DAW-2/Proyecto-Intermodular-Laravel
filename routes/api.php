<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ReviewController;

use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ProductImportController;

use App\Http\Controllers\Api\ProfileController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::name('api.')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class , 'logout']);

            // Endpoints d'escriptura protegits
            Route::apiResource('products', ApiProductController::class)
                ->parameters(['products' => 'product'])
                ->except(['index', 'show']);

            Route::post('products/import', [ProductImportController::class , 'store'])->name('products.import');

            // Endpoints de actualizacion de perfil de usuario
            Route::put('/update-profile', [ProfileController::class, 'update'])->name('user.update-profile');
            Route::put('/update-password', [PasswordController::class, 'update'])->name('user.update-password');

            
            Route::post('/products/{product}/reviews', [ReviewController::class, 'addReview'])->name('reviews.add');
            Route::put('/products/{product}/reviews/{review}', [ReviewController::class, 'updateReview'])->name('reviews.add');
            Route::delete('/products/{product}/reviews/{review}', [ReviewController::class, 'deleteReview'])->name('reviews.delete');
            
        }
        );

        Route::get('/products/{product}/reviews', [ReviewController::class, 'getReviewsFromProduct'])->name('reviews.from-product');

        Route::get('/health', function () {
            return response()->json([
            'status' => 'ok',
            'message' => 'Api esta funcionando, tt'
            ]);
        }
        )->name('health');

        // Endpoints públics (lectura)
        Route::apiResource('products', ApiProductController::class)
            ->parameters(['products' => 'product'])
            ->only(['index', 'show']);

        Route::post('/login', [AuthController::class , 'login']);
        Route::post('/register', [AuthController::class , 'register']);    });
