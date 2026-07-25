<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

require __DIR__.'/auth.php';
