<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Nyilvános
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('/user_public_data', AuthController::class)->only('index');

Route::apiResource('services', ServiceController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('customers', CustomerController::class);

// Védett
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/user/update', [AuthController::class, 'updateProfile']);

    Route::post('/user/services/sync', [AuthController::class, 'syncServices']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('services'));
    });
});