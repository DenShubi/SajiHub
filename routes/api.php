<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/menus', [MenuController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('admin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{id}', [MenuController::class, 'update']);
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    });
});
