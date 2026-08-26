<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\PublicRoomController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\LeaseContractController;
use App\Http\Controllers\VacancyController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (no authentication required)
|--------------------------------------------------------------------------
*/

Route::get('/public/rooms', [PublicRoomController::class, 'index']);

// Week 4 — prospective tenants submit these from the public site
Route::post('/inquiries', [InquiryController::class, 'store']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::post('/applications/status-check', [ApplicationController::class, 'checkStatus']);

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('web')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (auth:sanctum + admin middleware)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // --- User / privilege management (Week 2) ---
    Route::patch('/users/{user}/grant-admin', [UserManagementController::class, 'grantAdmin']);
    Route::patch('/users/{user}/revoke-admin', [UserManagementController::class, 'revokeAdmin']);

    // --- VR image upload (Week 3) ---
    Route::post('/rooms/{room}/vr-image', [VacancyController::class, 'uploadVrImage']);

    // --- Inquiries (Week 4, Mon) ---
    Route::get('/inquiries', [InquiryController::class, 'index']);
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show']);
    Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus']);

    // --- Applications (Week 4, Tue + Thu) ---
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{application}/approve', [ApplicationController::class, 'approve']);
    Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject']);
    Route::patch('/applications/{application}/cancel', [ApplicationController::class, 'cancel']);

    // --- Lease contracts (Week 4, Wed) ---
    Route::get('/lease-contracts', [LeaseContractController::class, 'index']);
    Route::get('/lease-contracts/{leaseContract}', [LeaseContractController::class, 'show']);
    Route::post('/lease-contracts/{leaseContract}/sign', [LeaseContractController::class, 'submitSigned']);
    Route::patch('/lease-contracts/{leaseContract}/not-applicable', [LeaseContractController::class, 'markNotApplicable']);
    Route::patch('/lease-contracts/{leaseContract}/terminate', [LeaseContractController::class, 'terminate']);
});