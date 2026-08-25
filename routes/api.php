<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\PublicRoomController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\LeaseContractController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\PenaltyController;
use App\Http\Controllers\Api\DamageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TenantPortalController;
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
| TENANT PORTAL ROUTES (auth:sanctum + tenant middleware)
|--------------------------------------------------------------------------
| Every route here is scoped to the authenticated user's own tenant record.
| A tenant cannot reach another tenant's data — the tenant is resolved from
| the session, never from a route parameter or request body.
*/

Route::middleware(['auth:sanctum', 'tenant'])->prefix('my')->group(function () {
    Route::get('/account', [TenantPortalController::class, 'me']);
    Route::get('/bills', [TenantPortalController::class, 'myBills']);
    Route::get('/bills/{billingStatement}', [TenantPortalController::class, 'showBill']);
    Route::post('/bills/{billingStatement}/payment-proof', [TenantPortalController::class, 'submitProof']);
    Route::get('/penalties', [TenantPortalController::class, 'myPenalties']);
    Route::get('/payments', [TenantPortalController::class, 'myPayments']);
    Route::get('/payments/{payment}/receipt', [TenantPortalController::class, 'receipt']);
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

    // --- Billing (Week 5, Mon) ---
    Route::get('/billing', [BillingController::class, 'index']);
    Route::get('/billing/{billingStatement}', [BillingController::class, 'show']);
    Route::post('/billing/generate', [BillingController::class, 'generate']);
    Route::post('/billing/contracts/{contract}/generate', [BillingController::class, 'generateForContractEndpoint']);
    Route::post('/billing/{billingStatement}/attach-penalties', [BillingController::class, 'attachPenalties']);

    // --- Payments (Record Cash Payment / review of tenant-submitted proofs) ---
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/billing/{billingStatement}/payments/cash', [PaymentController::class, 'recordCash']);
    Route::get('/billing/{billingStatement}/payments', [PaymentController::class, 'historyForStatement']);
    Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approveProof']);
    Route::patch('/payments/{payment}/reject', [PaymentController::class, 'rejectProof']);

    // --- Penalties (Week 5, Tue + Thu) ---
    Route::get('/penalties', [PenaltyController::class, 'index']);
    Route::get('/penalties/{penalty}', [PenaltyController::class, 'show']);
    Route::post('/penalties', [PenaltyController::class, 'store']);
    Route::patch('/penalties/{penalty}', [PenaltyController::class, 'update']);
    Route::patch('/penalties/{penalty}/waive', [PenaltyController::class, 'waive']);
    Route::patch('/penalties/{penalty}/reinstate', [PenaltyController::class, 'reinstate']);
    Route::delete('/penalties/{penalty}', [PenaltyController::class, 'destroy']);
    Route::get('/tenants/{tenant}/running-total', [PenaltyController::class, 'runningTotal']);

    // --- Damages (Week 5, Wed) ---
    Route::get('/damages', [DamageController::class, 'index']);
    Route::get('/damages/{damage}', [DamageController::class, 'show']);
    Route::post('/damages', [DamageController::class, 'store']);
    Route::post('/damages/{damage}', [DamageController::class, 'update']); // POST for multipart file uploads
    Route::delete('/damages/{damage}', [DamageController::class, 'destroy']);
});