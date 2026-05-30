<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'SajiHub API Backend',
        'status' => 'online'
    ]);
});
