<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organization Routes
|--------------------------------------------------------------------------
|
| Here are routes for organization management and switching functionality.
| These routes are protected by authentication and role-based permissions.
|
*/

Route::middleware(['auth', 'verified'])->prefix('backend')->name('backend.')->group(function () {
    
    // Organization management routes (SuperAdmin only)
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('organizations', OrganizationController::class);
        Route::patch('organizations/{organization}/toggle-status', [OrganizationController::class, 'toggleStatus'])
            ->name('organizations.toggle-status');
    });
    
    // Organization switching routes (accessible by all authenticated users)
    Route::post('organizations/switch', [OrganizationController::class, 'switch'])
        ->name('organizations.switch');
    Route::get('organizations/current', [OrganizationController::class, 'getCurrentOrganizations'])
        ->name('organizations.current');
});
