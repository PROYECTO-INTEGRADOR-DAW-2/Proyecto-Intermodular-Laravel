<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::post('/chat', [ChatController::class, 'chat'])->name('chat');

Route::get('/', [App\Http\Controllers\ProductController::class, 'home'])->name('home');

Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::match(['get', 'post'], '/add-to-cart/{id}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::patch('/update-cart/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/remove-from-cart/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/clear-cart', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/import', [ProductImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ProductImportController::class, 'store'])->name('import.store');
});

require __DIR__.'/auth.php';
