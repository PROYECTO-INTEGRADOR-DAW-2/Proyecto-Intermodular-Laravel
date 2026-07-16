<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ReviewController;

use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ProductImportController;

use App\Http\Controllers\Api\ProfileController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;

use App\Http\Controllers\Api\WishlistController;

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\UserImportController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

Route::get('/user', function (Request $request) {
    return $request->user()->load('role');
})->middleware('auth:sanctum');


Route::name('api.')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class , 'logout']);

            // Endpoints d'escriptura protegits
            Route::apiResource('products', ApiProductController::class)
                ->parameters(['products' => 'product'])
                ->except(['index', 'show']);

            Route::post('products/import', [ProductImportController::class , 'store'])->name('products.import');

            Route::get('/profile/reviews', [ProfileController::class, 'getReviews'])->name('profile.reviews');

            // Endpoints de actualizacion de perfil de usuario
            Route::put('/update-profile', [ProfileController::class, 'update'])->name('user.update-profile');
            Route::put('/update-password', [PasswordController::class, 'update'])->name('user.update-password');

            Route::get('/wishlist', [WishlistController::class, 'getWishlistFromUser'])->name('wishlist.get');
            Route::post('/wishlist', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
            
            Route::post('/products/{product}/reviews', [ReviewController::class, 'addReview'])->name('reviews.add');
            Route::put('/products/{product}/reviews/{review}', [ReviewController::class, 'updateReview'])->name('reviews.add');
            Route::delete('/products/{product}/reviews/{review}', [ReviewController::class, 'deleteReview'])->name('reviews.delete');

            Route::middleware('check.admin')->group(function() {
                Route::get('/users', [AdminController::class, 'getAllUsers'])->name('admin.get-users');
                Route::post('/users', [AdminController::class, 'createUser'])->name('admin.add-user');
                Route::post('/users/import', [UserImportController::class, 'store'])->name('admin.import-users');
                Route::get('/users/import/logs', [UserImportController::class, 'getLogs'])->name('admin.import-users-logs');
                Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.update-user');
                Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');

                Route::get('/products', [AdminController::class, 'getAllProducts'])->name('admin.get-products');
                Route::post('/products', [AdminController::class, 'createProduct'])->name('admin.add-product');
                Route::post('/products/import', [UserImportController::class, 'store'])->name('admin.import-products');
                Route::get('/products/import/logs', [UserImportController::class, 'getLogs'])->name('admin.import-products-logs');
                Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.update-product');
                Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.delete-product');

                Route::get('/roles', [AdminController::class, 'getAllRoles'])->name('admin.get-roles');
                Route::post('/roles', [AdminController::class, 'addRole'])->name('admin.add-role');
                Route::delete('/roles/{role}', [AdminController::class, 'deleteRole'])->name('admin.delete-role');
                Route::put('/roles/{role}', [AdminController::class, 'updateRole'])->name('admin.update-role');

                Route::get('/sizes', [AdminController::class, 'getSizes'])->name('admin.get-sizes');
            });

            
            
            
            
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

        Route::get('/productos/imagen/{marca}/{categoria}/{nombre}', function ($marca, $categoria, $nombre) {
                // Reconstruimos la ruta física del archivo dentro de la carpeta public
                $path = public_path("img/img{$marca}/{$categoria}/{$nombre}");

                // Si el archivo no existe, escupimos un 404
                if (!File::exists($path)) {
                    abort(404);
                }

                // Leemos el archivo
                $file = File::get($path);
                $type = File::mimeType($path);

                // 🎯 LA CLAVE: Devolvemos el archivo a través de una respuesta de Laravel.
                // Esto hace que pase por los Middlewares de la API y le meta las cabeceras de CORS automáticamente.
                return response($file, 200)->header("Content-Type", $type);
            });

        // Endpoints públics (lectura)
        Route::apiResource('products', ApiProductController::class)
            ->parameters(['products' => 'product'])
            ->only(['index', 'show']);

        Route::post('/login', [AuthController::class , 'login']);
        Route::post('/register', [AuthController::class , 'register']);    });
