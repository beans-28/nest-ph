<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantBillingController;
use App\Http\Controllers\Api\TenantPortalController;

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
// Authentication routes
Route::post('/tenant/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'login']);

Route::get('/login/tenant', function () {
    return view('logintenant');
})->name('login.tenant');

Route::get('/login/admin', function () {
    return view('loginadmin');
})->name('login.admin');

// Redirect the default /login to the admin login page
Route::get('/login', function () {
    return redirect()->route('login.admin');
})->name('login');

// Public test route for VR Management view (temporary)
Route::get('/vr-management-test', function () {
    $rooms = \App\Models\Room::all()->map(function($room){
        return [
            'id' => $room->id,
            'room_no' => $room->room_no,
            'floor' => $room->floor,
            'vr_asset_path' => $room->vr_asset_path,
            'vr_url' => $room->vr_asset_path ? asset('storage/'.$room->vr_asset_path) : null,
            'vr_caption' => $room->vr_caption,
            'vr_visibility' => $room->vr_visibility,
            'updated_at' => optional($room->updated_at)->toDateTimeString(),
        ];
    })->toArray();

    return view('vrmanagement', compact('rooms'));
})->name('vr.test');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/add-floor', [VacancyController::class, 'index'])->name('admin.addfloor');
    Route::get('/vacancy-monitoring', [VacancyController::class, 'index'])->name('vacancy.index');
    Route::delete('/vacancy/floors/{floorNumber}', [VacancyController::class, 'destroyFloor'])->name('vacancy.floors.destroy');

    Route::post('/vacancy/rooms', [VacancyController::class, 'storeRoom'])->name('vacancy.rooms.store');
    Route::put('/vacancy/rooms/{room}', [VacancyController::class, 'updateRoom'])->name('vacancy.rooms.update');
    Route::delete('/vacancy/rooms/{room}', [VacancyController::class, 'destroyRoom'])->name('vacancy.rooms.destroy');

    // VR image and info endpoints
    Route::post('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'uploadVrImage'])->name('vacancy.rooms.vr-image.store');
    Route::delete('/vacancy/rooms/{room}/vr-image', [VacancyController::class, 'deleteVrImage'])->name('vacancy.rooms.vr-image.destroy');
    Route::patch('/vacancy/rooms/{room}/vr-info', [VacancyController::class, 'updateVrInfo'])->name('vacancy.rooms.vr-info.update');

    Route::patch('/vacancy/beds/{bed}', [VacancyController::class, 'updateBedStatus'])->name('vacancy.beds.update');

    // VR Management view (admin)
    Route::get('/vr-management', [VacancyController::class, 'vrIndex'])->name('vr.index');
    });


// Tenant portal routes (protected by tenant auth)
Route::middleware(['auth', 'tenant'])->prefix('my')->group(function () {
    Route::get('/billing', [TenantBillingController::class, 'index'])->name('tenant.billing');
    Route::get('/billing/summary', [TenantPortalController::class, 'me'])->name('tenant.billing.summary');
    Route::get('/billing/bills', [TenantPortalController::class, 'myBills'])->name('tenant.billing.bills');
    Route::get('/billing/bills/{billingStatement}', [TenantPortalController::class, 'showBill'])->name('tenant.billing.bills.show');
    Route::post('/billing/bills/{billingStatement}/payment-proof', [TenantPortalController::class, 'submitProof'])->name('tenant.billing.bills.proof');
    Route::get('/billing/payments', [TenantPortalController::class, 'myPayments'])->name('tenant.billing.payments');
    Route::get('/billing/payments/{payment}/receipt', [TenantPortalController::class, 'receipt'])->name('tenant.billing.receipt');
    Route::get('/billing/penalties', [TenantPortalController::class, 'myPenalties'])->name('tenant.billing.penalties');
});

require __DIR__.'/auth.php';

// Temporary public test route for the Tenant Dashboard view
Route::get('/tenant-dashboard-test', function () {
    $tenant = \App\Models\Tenant::first();

    $contract = $tenant?->contracts()
        ->where('status', 'active')
        ->with('bed.room')
        ->first();

    $recentBills = collect();

    return view('tenantdashboard', [
        'tenant' => $tenant,
        'contract' => $contract,
        'balanceDue' => 0,
        'nextDueDate' => null,
        'daysUntilDue' => null,
        'recentBills' => $recentBills,
        'openTicketsCount' => 0,
        'inProgressCount' => 0,
        'recentTickets' => collect(),
    ]);
})->name('tenant.dashboard.test');
