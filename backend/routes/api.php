<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Nyilvános
Route::post('/login', [AuthController::class, 'login']);

// Védett
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::apiResource('services', ServiceController::class);
    Route::apiResource('customers', CustomerController::class);
});