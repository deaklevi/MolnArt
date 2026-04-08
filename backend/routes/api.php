<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScheduleController;


// Nyilvános
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/appointments', [AppointmentController::class, 'store']);

Route::apiResource('/user_public_data', AuthController::class)->only('index');

Route::apiResource('appointments', AppointmentController::class);

Route::apiResource('services', ServiceController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('schedule',ScheduleController::class);
Route::apiResource('products',ProductController::class);

// Védett
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/user/update', [AuthController::class, 'updateProfile']);

    Route::post('/user/services/sync', [AuthController::class, 'syncServices']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('services'));
    });
});