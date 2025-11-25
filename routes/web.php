<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Guest routes (auth pages)
Route::middleware('guest')->group(function () {
    // Show login form
    Route::get('/login', [AuthController::class, 'showAuthForm'])
        ->name('login');
    
    // Show register form
    Route::get('/register', function () {
        return app(AuthController::class)->showAuthForm('register');
    })->name('register');
    
    // Handle traditional login (password)
    Route::post('/login', [AuthController::class, 'login']);
    
    // Handle voice login
    Route::post('/voice-login', [AuthController::class, 'voiceLogin'])
        ->name('voice.login');
    
    // Handle registration
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard (placeholder)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');