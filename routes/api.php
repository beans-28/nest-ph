<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\FloorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('web')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::patch('/users/{user}/grant-admin', [UserManagementController::class, 'grantAdmin']);
    Route::patch('/users/{user}/revoke-admin', [UserManagementController::class, 'revokeAdmin']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/floors', [FloorController::class, 'index']);
    Route::post('/floors', [FloorController::class, 'store']);
    Route::get('/floors/{floor}', [FloorController::class, 'show']);
    Route::patch('/floors/{floor}', [FloorController::class, 'update']);
    Route::delete('/floors/{floor}', [FloorController::class, 'destroy']);
});