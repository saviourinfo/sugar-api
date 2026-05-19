<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\AuthController;

Route::post('/login',  [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('trips', TripController::class);
    Route::get('stats/dashboard', [TripController::class, 'dashStats']);
    Route::get('trips/next-sr',   [TripController::class, 'nextSr']);
});