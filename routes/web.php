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
    // Dashboard dengan data real
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Account Settings
    Route::get('/settings', [App\Http\Controllers\AccountSettingsController::class, 'index'])
        ->name('settings');
    Route::post('/settings/update', [App\Http\Controllers\AccountSettingsController::class, 'update'])
        ->name('settings.update');
    
    // Budgeting
    Route::get('/budgeting', function () {
        return view('budget');
    })->name('budgeting');
    
    // Laporan
    Route::get('/laporan', function () {
        return view('laporan');
    })->name('laporan');
     // Voice Transaction API
    Route::post('/api/voice-transaction', [App\Http\Controllers\VoiceTransactionController::class, 'store'])
        ->name('voice.transaction.store');
    
    // NEW: Parse voice text (Web Speech API)
    Route::post('/api/parse-voice-text', [App\Http\Controllers\VoiceTransactionController::class, 'parseVoiceText'])
        ->name('voice.parse.text');
    
    Route::get('/api/budgets', [App\Http\Controllers\VoiceTransactionController::class, 'getBudgets'])
        ->name('api.budgets');
    
    Route::get('/api/goals', [App\Http\Controllers\VoiceTransactionController::class, 'getGoals'])
        ->name('api.goals');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});