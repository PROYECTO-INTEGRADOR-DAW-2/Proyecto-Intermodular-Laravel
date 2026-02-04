<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
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
        Route::apiResource('products', ProductController::class)
            ->parameters(['products' => 'product'])
            ->except(['index', 'show']);

    });

    // Endpoints públics (lectura)
    Route::apiResource('products', ProductController::class)
        ->parameters(['products' => 'product'])
        ->only(['index', 'show']);

});

