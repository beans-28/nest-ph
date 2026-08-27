<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\TenantPortalController;
use App\Http\Controllers\VacancyController;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/rooms', function () {
    return view('publicrooms');
})->name('public.rooms');

Route::get('/vr-tour', function () {
    return view('publicvr');
})->name('public.vr');

Route::get('/dorm-info', function () {
    return view('publicdorminfo');
})->name('public.dorminfo');

Route::get('/inquire', function () {
    return view('publicinquiry');
})->name('public.inquiry');

Route::get('/apply', function () {
    return view('publicapply');
})->name('public.apply');

Route::prefix('public-api')->group(function () {
    Route::get('/rooms', [PublicController::class, 'rooms']);
    Route::get('/rooms/{room}', [PublicController::class, 'room']);
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
    Route::post('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'uploadVrImage']);
    Route::delete('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'deleteVrImage']);
    Route::patch('/vacancy/rooms/{room}/vr-info', [VacancyController::class, 'updateVrInfo']);
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