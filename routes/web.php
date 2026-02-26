<?php

use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | This project is strictly an API. All logic resides in routes/api.php. | */

Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});
