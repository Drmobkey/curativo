<?php

use App\Http\Controllers\API\User\history_injury;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    
    Route::prefix('history')->group(function () {
        Route::get('/history', [history_injury::class, 'index']);
        Route::post('/history', [history_injury::class, 'store']);
        Route::get('/history/{id}', [history_injury::class, 'show']);
        Route::put('/history/{id}', [history_injury::class, 'update']);
        Route::delete('/history/{id}', [history_injury::class, 'destroy']);
    });

    
});
