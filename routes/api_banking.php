<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankingDetailsController;

/*
|--------------------------------------------------------------------------
| Banking Details API Routes
|--------------------------------------------------------------------------
|
| API routes for banking details auto-population functionality
|
*/

Route::middleware(['auth:sanctum'])->prefix('backend')->name('api.backend.')->group(function () {
    
    // Banking Details Routes
    Route::prefix('banking-details')->name('banking-details.')->group(function () {
        
        // Get banking details for auto-population
        Route::get('/', [BankingDetailsController::class, 'getBankingDetails'])
            ->name('get');
        
        // Validate banking details
        Route::post('/validate', [BankingDetailsController::class, 'validateBankingDetails'])
            ->name('validate');
        
        // Get IFSC code details
        Route::get('/ifsc/{ifscCode}', [BankingDetailsController::class, 'getIFSCDetails'])
            ->name('ifsc-details');
        
        // Get user's banking details
        Route::get('/user', [BankingDetailsController::class, 'getUserBankingDetails'])
            ->name('user');
    });
    
    // Vendor Banking Routes
    Route::prefix('vendor')->name('vendor.')->group(function () {
        
        // Get vendor's all banking accounts
        Route::get('{vendorId}/accounts', [BankingDetailsController::class, 'getVendorAccounts'])
            ->name('accounts');
    });
});

// Public API routes (if needed for external integrations)
Route::prefix('public/banking')->name('api.public.banking.')->group(function () {
    
    // IFSC code lookup (public endpoint)
    Route::get('/ifsc/{ifscCode}', [BankingDetailsController::class, 'getIFSCDetails'])
        ->name('ifsc-details');
});
