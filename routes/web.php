<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VacancyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Named route used by some views: show password request form
Route::view('/passwords', 'auth.forgot-password')->name('passwords');

Route::get('/admin/add-floor', [VacancyController::class, 'index'])
    ->name('admin.addfloor');

Route::get('/vacancy-monitoring', [VacancyController::class, 'index'])
    ->name('vacancy.index');

// Rooms
Route::post('/vacancy/rooms', [VacancyController::class, 'storeRoom'])
    ->name('vacancy.rooms.store');
Route::put('/vacancy/rooms/{room}', [VacancyController::class, 'updateRoom'])
    ->name('vacancy.rooms.update');
Route::delete('/vacancy/rooms/{room}', [VacancyController::class, 'destroyRoom'])
    ->name('vacancy.rooms.destroy');

// Beds
Route::patch('/vacancy/beds/{bed}', [VacancyController::class, 'updateBedStatus'])
    ->name('vacancy.beds.update');
