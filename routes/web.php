<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalsController;

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
//Route::middleware('auth')->group(function () {
    // Dashboard (placeholder)
    Route::get('/dashboard', [DashboardController::class, 'index']
    )->name('dashboard');
    
    // Budgeting
    Route::get('/budgeting', function () {
        return view('budget');
    })->name('budgeting');

    // Goals
    Route::get('/goals', [GoalsController::class, 'index'])->name('goals');
    Route::post('/goals', [GoalsController::class, 'store'])->name('goals.store');
    Route::put('/goals/{id}', [GoalsController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{id}', [GoalsController::class, 'destroy'])->name('goals.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});