<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaseContractController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\TenantPortalController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VrTourController;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/rooms', function () {
    return view('publicrooms');
})->name('public.rooms');

Route::get('/vr-tour', function () {
    return view('publicvr');
})->name('public.vr');

Route::get('/dorm-info', [PublicController::class, 'dormInfoPage'])->name('public.dorminfo');
Route::get('/dorm-info/policies-file', [PublicController::class, 'policiesFileView'])->name('public.dorminfo.file');
Route::get('/dorm-info/policies-file/download', [PublicController::class, 'policiesFileDownload'])->name('public.dorminfo.download');

Route::get('/inquire', function () {
    return view('publicinquiry');
})->name('public.inquiry');

Route::get('/apply', function () {
    return view('publicapply');
})->name('public.apply');

Route::prefix('public-api')->group(function () {
    Route::get('/rooms', [PublicController::class, 'rooms']);
    Route::get('/rooms/{room}', [PublicController::class, 'room']);
    Route::get('/rooms/{room}/beds', [PublicController::class, 'roomBeds']);
    Route::get('/rooms/{room}/vr-tour', [PublicController::class, 'roomVrTour']);
    Route::get('/vr-tours', [PublicController::class, 'vrTours']);
    Route::get('/dorm-info', [PublicController::class, 'dormInfo']);
    Route::get('/filter-options', [PublicController::class, 'filterOptions']);
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login/tenant', function () {
    return view('logintenant');
})->name('login.tenant');

Route::get('/login/admin', function () {
    return view('loginadmin');
})->name('login.admin');

Route::post('/tenant/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'login']);

Route::get('/passwords', function () {
    return view('passwords');
})->name('passwords');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/add-floor', [VacancyController::class, 'index'])->name('admin.addfloor');
    Route::get('/vacancy-monitoring', [VacancyController::class, 'index'])->name('vacancy.index');

    Route::post('/vacancy/rooms', [VacancyController::class, 'storeRoom'])->name('vacancy.rooms.store');
    Route::put('/vacancy/rooms/{room}', [VacancyController::class, 'updateRoom'])->name('vacancy.rooms.update');
    Route::delete('/vacancy/rooms/{room}', [VacancyController::class, 'destroyRoom'])->name('vacancy.rooms.destroy');
    Route::delete('/vacancy/floors/{floorNumber}', [VacancyController::class, 'destroyFloor'])->name('vacancy.floors.destroy');
    Route::patch('/vacancy/beds/{bed}', [VacancyController::class, 'updateBedStatus'])->name('vacancy.beds.update');

    Route::get('/vr-management', [VacancyController::class, 'vrIndex'])->name('vr.index');
    Route::patch('/vacancy/rooms/{room}/vr-info', [VacancyController::class, 'updateVrInfo']);

    // --- Application review (Week 4, Thu) ---
    Route::get('/applications', [ApplicationController::class, 'page'])->name('applications.index');
    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve']);
    Route::post('/applications/{application}/reject', [ApplicationController::class, 'reject']);
    Route::post('/applications/{application}/request-reapplication', [ApplicationController::class, 'requestReapplication']);

    // --- Inquiry management (Week 4, Mon) ---
    Route::get('/inquiries', [InquiryController::class, 'page'])->name('inquiries.index');
    Route::post('/inquiries/{inquiry}/reply', [InquiryController::class, 'reply']);
    Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus']);

    Route::delete('/vacancy/rooms/{room}/photos/{photo}', [VacancyController::class, 'deleteRoomPhoto']);
    Route::post('/vacancy/rooms/{room}/photos/reorder', [VacancyController::class, 'reorderRoomPhotos']);

    // Multi-scene VR tour: panorama scenes + click-to-place hotspots. Driven
    // by the VR Management page's edit view.
    Route::post('/vr-tours/rooms/{room}/scenes', [VrTourController::class, 'storeScene']);
    Route::patch('/vr-tours/scenes/{scene}', [VrTourController::class, 'updateScene']);
    Route::patch('/vr-tours/scenes/{scene}/view', [VrTourController::class, 'updateSceneView']);
    Route::post('/vr-tours/scenes/{scene}/default', [VrTourController::class, 'setDefaultScene']);
    Route::delete('/vr-tours/scenes/{scene}', [VrTourController::class, 'destroyScene']);
    Route::post('/vr-tours/scenes/{scene}/hotspots', [VrTourController::class, 'storeHotspot']);
    Route::delete('/vr-tours/hotspots/{hotspot}', [VrTourController::class, 'destroyHotspot']);

    // --- Lease contracts (Week 4, Wed) ---
    // Mirrors the /api/lease-contracts endpoints but under session auth, so
    // the Blade page can call them directly like the other admin screens do.
    Route::get('/lease-contracts', [LeaseContractController::class, 'page'])->name('contracts.index');
    Route::post('/lease-contracts', [LeaseContractController::class, 'store']);
    Route::get('/lease-contracts/tenants/search', [LeaseContractController::class, 'searchTenants']);
    Route::patch('/lease-contracts/{leaseContract}/renew', [LeaseContractController::class, 'renew']);
    Route::post('/lease-contracts/{leaseContract}/sign', [LeaseContractController::class, 'submitSigned']);
    Route::patch('/lease-contracts/{leaseContract}/not-applicable', [LeaseContractController::class, 'markNotApplicable']);
    Route::patch('/lease-contracts/{leaseContract}/terminate', [LeaseContractController::class, 'terminate']);
});

/*
|--------------------------------------------------------------------------
| TENANT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/billing', function () {
        return view('tenantbilling');
    })->name('tenant.billing');

    Route::prefix('my/billing')->group(function () {
        Route::get('/summary', [TenantPortalController::class, 'me']);
        Route::get('/bills', [TenantPortalController::class, 'myBills']);
        Route::get('/bills/{billingStatement}', [TenantPortalController::class, 'showBill']);
        Route::post('/bills/{billingStatement}/payment-proof', [TenantPortalController::class, 'submitProof']);
    });
});

require __DIR__.'/auth.php';