<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\GreenNote\GreenNoteController;
use App\Http\Controllers\Backend\PaymentNote\PaymentNoteController;
use App\Http\Controllers\Backend\VendorAccount\VendorAccountController;
use App\Http\Controllers\Backend\Vendor\VendorController;

/*
|--------------------------------------------------------------------------
| New Features Routes
|--------------------------------------------------------------------------
|
| Routes for the new features implemented:
| 1. Multiple Invoices for Green Notes
| 2. Hold functionality for Green Notes and Payment Notes
| 3. Draft Payment Notes with auto-creation
| 4. Multiple Vendor Accounts
| 5. Auto Vendor Code Generation
|
*/

Route::middleware(['auth', 'web'])->prefix('backend')->name('backend.')->group(function () {
    
    // Green Note - Multiple Invoices Routes
    Route::prefix('green-note')->name('green-note.')->group(function () {
        Route::get('{greenNote}/multiple-invoices', [GreenNoteController::class, 'showMultipleInvoices'])
            ->name('multiple-invoices.show');
        Route::put('{greenNote}/multiple-invoices', [GreenNoteController::class, 'updateMultipleInvoices'])
            ->name('multiple-invoices.update');
        Route::get('{greenNote}/invoice-summary', [GreenNoteController::class, 'getInvoiceSummary'])
            ->name('invoice-summary');
    });

    // Green Note - Hold functionality
    Route::prefix('green-note')->name('green-note.')->group(function () {
        Route::post('{greenNote}/hold', [GreenNoteController::class, 'putOnHold'])
            ->name('hold');
        Route::post('{greenNote}/remove-hold', [GreenNoteController::class, 'removeFromHold'])
            ->name('remove-hold');
        Route::post('{greenNote}/approve-with-payment', [GreenNoteController::class, 'approveWithPaymentNote'])
            ->name('approve-with-payment');
    });

    // Payment Note - Draft Management Routes
    Route::prefix('payment-note')->name('payment-note.')->group(function () {
        Route::get('drafts', [PaymentNoteController::class, 'drafts'])
            ->name('drafts');
        Route::post('{paymentNote}/convert-to-active', [PaymentNoteController::class, 'convertDraftToActive'])
            ->name('convert-to-active');
        Route::delete('{paymentNote}/delete-draft', [PaymentNoteController::class, 'deleteDraft'])
            ->name('delete-draft');
    });

    // Payment Note - Hold functionality
    Route::prefix('payment-note')->name('payment-note.')->group(function () {
        Route::post('{paymentNote}/hold', [PaymentNoteController::class, 'putOnHold'])
            ->name('hold');
        Route::post('{paymentNote}/remove-hold', [PaymentNoteController::class, 'removeFromHold'])
            ->name('remove-hold');
    });

    // Payment Note - SuperAdmin CRUD
    Route::prefix('payment-note')->name('payment-note.')->middleware('role:Super Admin')->group(function () {
        Route::get('create-superadmin', [PaymentNoteController::class, 'createForSuperAdmin'])
            ->name('create-superadmin');
        Route::post('store-superadmin', [PaymentNoteController::class, 'storeForSuperAdmin'])
            ->name('store-superadmin');
    });

    // Vendor Accounts Management Routes
    Route::prefix('vendor/{vendor}/accounts')->name('vendor.accounts.')->group(function () {
        Route::get('/', [VendorAccountController::class, 'index'])
            ->name('index');
        Route::get('create', [VendorAccountController::class, 'create'])
            ->name('create');
        Route::post('/', [VendorAccountController::class, 'store'])
            ->name('store');
        Route::get('{account}', [VendorAccountController::class, 'show'])
            ->name('show');
        Route::get('{account}/edit', [VendorAccountController::class, 'edit'])
            ->name('edit');
        Route::put('{account}', [VendorAccountController::class, 'update'])
            ->name('update');
        Route::delete('{account}', [VendorAccountController::class, 'destroy'])
            ->name('destroy');
        Route::post('{account}/toggle-status', [VendorAccountController::class, 'toggleStatus'])
            ->name('toggle-status');
        Route::post('{account}/set-primary', [VendorAccountController::class, 'setPrimary'])
            ->name('set-primary');
    });

    // Vendor Banking Details API Route
    Route::get('vendor/{vendor}/banking-details', [VendorAccountController::class, 'getBankingDetails'])
        ->name('vendor.banking-details');

    // Vendor Code Generation Routes
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::post('{vendor}/regenerate-code', [VendorController::class, 'regenerateCode'])
            ->name('regenerate-code');
        Route::post('generate-code', [VendorController::class, 'generateCode'])
            ->name('generate-code');
    });
});

// API Routes for AJAX calls
Route::middleware(['auth:sanctum'])->prefix('api/backend')->name('api.backend.')->group(function () {
    
    // Green Note APIs
    Route::prefix('green-note')->name('green-note.')->group(function () {
        Route::get('{greenNote}/invoice-summary', [GreenNoteController::class, 'getInvoiceSummary'])
            ->name('invoice-summary');
    });

    // Vendor APIs
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('{vendor}/banking-details', [VendorAccountController::class, 'getBankingDetails'])
            ->name('banking-details');
        Route::get('{vendor}/accounts', [VendorAccountController::class, 'index'])
            ->name('accounts');
    });
});
