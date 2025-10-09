<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

require_once 'backend.php';
require_once 'payment_note_fix.php';
require_once 'onboarding.php';

// Authentication Routes
Auth::routes();

// Custom Password Reset Routes with custom views
Route::get('password/reset', function() {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('password/reset/{token}', function($token) {
    return view('auth.reset-password', ['token' => $token, 'email' => request()->email]);
})->name('password.reset');

Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/', function () {
    // Check if system is initialized
    $isInitialized = \App\Models\Organization::exists() && \App\Models\User::role('superadmin')->exists();
    
    if (!$isInitialized) {
        return redirect()->route('onboarding.welcome');
    }
    
    return redirect()->route('backend.login');
});

Route::get('/login', function () {
    return redirect()->intended('/backend/login');
})->name('login');

Route::get('/backend/register', function () {
    return redirect()->intended('/backend/login');
});

// Add this in web.php for quick clearing
Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return 'Cleared!';
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'products' => ProductController::class,
]);
