<?php

use App\Http\Controllers\Api\PlaygroundController;
use Illuminate\Support\Facades\Route;

// ルート定義
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello API!']);
});

Route::get('/playground/sample', [PlaygroundController::class, 'sample']);
