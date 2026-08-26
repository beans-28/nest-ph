<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantPortalController;
use App\Http\Controllers\VacancyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/passwords', function () {
    return view('passwords');
})->name('passwords');

Route::post('/tenant/login', [AuthController::class, 'login']);

Route::post('/admin/login', [AuthController::class, 'login']);

Route::get('/login/tenant', function () {
    return view('logintenant');
})->name('login.tenant');

Route::get('/login/admin', function () {
    return view('loginadmin');
})->name('login.admin');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/add-floor', [VacancyController::class, 'index'])->name('admin.addfloor');
    Route::get('/vacancy-monitoring', [VacancyController::class, 'index'])->name('vacancy.index');
    Route::delete('/vacancy/floors/{floorNumber}', [VacancyController::class, 'destroyFloor'])->name('vacancy.floors.destroy');

    Route::post('/vacancy/rooms', [VacancyController::class, 'storeRoom'])->name('vacancy.rooms.store');
    Route::put('/vacancy/rooms/{room}', [VacancyController::class, 'updateRoom'])->name('vacancy.rooms.update');
    Route::delete('/vacancy/rooms/{room}', [VacancyController::class, 'destroyRoom'])->name('vacancy.rooms.destroy');

    Route::patch('/vacancy/beds/{bed}', [VacancyController::class, 'updateBedStatus'])->name('vacancy.beds.update');

    // --- VR Management ---
    Route::get('/vr-management', [VacancyController::class, 'vrIndex'])->name('vr.index');
    Route::post('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'uploadVrImage']);
    Route::delete('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'deleteVrImage']);
    Route::patch('/vacancy/rooms/{room}/vr-info', [VacancyController::class, 'updateVrInfo']);
});

/*
|--------------------------------------------------------------------------
| TENANT BILLING PAGE (auth + tenant middleware)
|--------------------------------------------------------------------------
| Paths match the already-built tenantbilling.blade.php / tenantdashboard's
| JS exactly (no /api prefix) — see TenantPortalController for the same
| logic exposed under /api/my/... for any other API consumer.
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