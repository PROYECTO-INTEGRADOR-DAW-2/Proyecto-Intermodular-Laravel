<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;

// ── Google OAuth2 (public, no auth required) ──────────────────────────────────
Route::get('oauth/google/redirect', [SocialAuthController::class , 'redirect']);
Route::get('oauth/google/callback', [SocialAuthController::class , 'callback']);
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/user', function (Request $request) {
    $user = $request->user()->load('roles');
    return response()->json([
    'name' => $user->nombre,
    'email' => $user->email,
    'role' => $user->role,
    'roles' => $user->roles->pluck('name'),
    ]);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile', [AuthController::class , 'updateProfile']);
    Route::get('/user/reviews', [App\Http\Controllers\Api\ReviewController::class , 'userIndex']);
});



Route::post('login', [AuthController::class , 'login']);
Route::post('register', [AuthController::class , 'register']);

Route::name('api.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class , 'logout']);

            // Exemple: protegim els endpoints d'escriptura
            Route::apiResource('products', ProductController::class)
                ->parameters(['products' => 'product'])
                ->except(['index', 'show']);

            Route::apiResource('orders', App\Http\Controllers\Api\OrderController::class)
                ->only(['index', 'store']);

            Route::post('products/{product}/reviews', [App\Http\Controllers\Api\ReviewController::class , 'store']);

            Route::get('wishlist', [App\Http\Controllers\Api\WishlistController::class , 'index']);
            Route::post('wishlist/{product}', [App\Http\Controllers\Api\WishlistController::class , 'toggle']);

            // Admin-only routes
            Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
                    Route::get('users', [App\Http\Controllers\Api\RoleController::class , 'users']);
                    Route::get('roles', [App\Http\Controllers\Api\RoleController::class , 'roles']);
                    Route::post('users/{user}/roles', [App\Http\Controllers\Api\RoleController::class , 'assignRole']);
                    Route::delete('users/{user}/roles/{role}', [App\Http\Controllers\Api\RoleController::class , 'removeRole']);
                    // All orders (admin view)
                    Route::get('orders', [App\Http\Controllers\Api\OrderController::class , 'adminIndex']);
                    Route::patch('orders/{order}/status', [App\Http\Controllers\Api\OrderController::class , 'updateStatus']);

                    // Product management (admin)
                    Route::post('products/import', [\App\Http\Controllers\ProductImportController::class , 'store']);
                    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class)
                        ->parameters(['products' => 'product'])
                        ->only(['store', 'update', 'destroy']);
                }
                );

            }
            );

            // Endpoints públics (lectura)
            Route::get('home-products', [ProductController::class , 'home']);
            Route::post('cart/details', [App\Http\Controllers\Api\CartController::class , 'getDetails']);

            // Products public (read)
            Route::apiResource('products', ProductController::class)
                ->parameters(['products' => 'product'])
                ->only(['index', 'show']);
        });
