<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PreflightController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/preflight');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/preflight', [PreflightController::class, 'show']);
});
