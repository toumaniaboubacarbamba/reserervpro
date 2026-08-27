<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EstablishmentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/bookings', [BookingController::class, 'store']);

    Route::post('/establishments', [EstablishmentController::class, 'store']);
    Route::put('/establishments/{establishment}', [EstablishmentController::class, 'update']);
    Route::delete('/establishments/{establishment}', [EstablishmentController::class, 'destroy']);
});
