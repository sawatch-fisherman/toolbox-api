<?php

use App\Http\Controllers\Api\PlaygroundController;
use Illuminate\Support\Facades\Route;

// ルート定義
Route::prefix('playground')->controller(PlaygroundController::class)->group(function () {
    Route::get('/sample', 'sample');
    Route::get('/buggy-continue-level-example', 'buggyContinueLevelExample');
    Route::get('/correct-continue-level-example', 'correctContinueLevelExample');
});
