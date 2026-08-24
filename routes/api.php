<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\PublicRoomController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ApplicationController;

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
    Route::post('/rooms/{room}/vr-image', [\App\Http\Controllers\VacancyController::class, 'uploadVrImage']);
});

Route::post('/inquiries', [InquiryController::class, 'store']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::post('/applications/status-check', [ApplicationController::class, 'checkStatus']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/inquiries', [InquiryController::class, 'index']);
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show']);
    Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus']);

    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{application}/cancel', [ApplicationController::class, 'cancel']);
});