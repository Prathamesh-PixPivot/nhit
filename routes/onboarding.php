<?php

use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Onboarding Routes
|--------------------------------------------------------------------------
|
| These routes handle the initial system setup and organization creation
|
*/

Route::middleware(['web'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'welcome'])->name('welcome');
    Route::get('/setup-organization', [OnboardingController::class, 'setupOrganization'])->name('setup-organization');
    Route::post('/setup-organization', [OnboardingController::class, 'storeOrganization'])->name('store-organization');
    Route::get('/setup-superadmin', [OnboardingController::class, 'setupSuperAdmin'])->name('setup-superadmin');
    Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
});
