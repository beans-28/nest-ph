<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\FloorController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BedController;
use App\Http\Controllers\Api\PublicRoomController;

Route::get('/public/rooms', [PublicRoomController::class, 'index']);

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

    Route::get('/floors/{floor}/rooms', [RoomController::class, 'index']);
    Route::post('/floors/{floor}/rooms', [RoomController::class, 'store']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);
    Route::patch('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    Route::get('/rooms/{room}/beds', [BedController::class, 'index']);
    Route::post('/rooms/{room}/beds', [BedController::class, 'store']);
    Route::patch('/beds/{bed}', [BedController::class, 'update']);
    Route::delete('/beds/{bed}', [BedController::class, 'destroy']);

    Route::post('/rooms/{room}/vr-image', [RoomController::class, 'uploadVrImage']);
});