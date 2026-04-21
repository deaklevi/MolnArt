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
use App\Http\Controllers\UnavailabilityController;

// Nyilvános
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

//Route::post('/login', [AuthController::class, 'login']);


Route::apiResource('/user_public_data', AuthController::class)->only('index');

Route::post('/appointments', [AppointmentController::class, 'store']);
Route::get('/appointments/slots', [AppointmentController::class, 'availableSlots']);


Route::apiResource('services', ServiceController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('products',ProductController::class);


// Védett
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    //Route::get('/customers', [CustomerController::class, 'index']);
    Route::apiResource('appointments', AppointmentController::class);

    Route::apiResource('schedule',ScheduleController::class);
    Route::post('/user/update', [AuthController::class, 'updateProfile']);

    Route::post('/user/services/sync', [AuthController::class, 'syncServices']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('services'));
    });

    Route::apiResource('unavailabilities', UnavailabilityController::class);
});