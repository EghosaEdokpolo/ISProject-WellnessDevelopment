<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Routes for logged-out users (Guests)
Route::middleware('guest')->group(function () {
    
    // 1. Show your Login Page
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // 2. Submit the Login Form credentials
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Routes for logged-in users (Authenticated)
Route::middleware('auth')->group(function () {
    
    // 3. Log out of the system
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
