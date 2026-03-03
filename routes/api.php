<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\AuthController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::name('api.')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // Exemple: protegim els endpoints d'escriptura
        Route::apiResource('products', ApiProductController::class)
            ->parameters(['products' => 'product'])
            ->except(['index', 'show']);

    });

    // En lugar de Route::get('/', ...)
    Route::get('/health', function() {
        return response()->json([
            'status' => 'ok',
            'message' => 'Api esta funcionando, tt'
        ]);
    })->name('health');

    // Endpoints públics (lectura)
    Route::apiResource('products', ApiProductController::class)
        ->parameters(['products' => 'product'])
        ->only(['index', 'show']);

});

