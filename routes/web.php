<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

require_once 'backend.php';
Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
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
