<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/menus', [MenuController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/menus/{id}', [MenuController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{id}', [MenuController::class, 'update']);
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    });

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/upload-proof', [OrderController::class, 'uploadProof']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::middleware('admin')->group(function () {
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
