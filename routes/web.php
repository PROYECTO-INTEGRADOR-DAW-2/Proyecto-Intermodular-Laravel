<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.home');
})->name('home');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/import', [ProductImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ProductImportController::class, 'store'])->name('import.store');

    Route::resource('products', ProductController::class)->except(['index', 'show']);

});

//Rutas publicas
Route::resource('products', ProductController::class)->only(['index', 'show']);


require __DIR__.'/auth.php';
