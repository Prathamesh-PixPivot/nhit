<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PaymentNote\PaymentNoteController;

/*
|--------------------------------------------------------------------------
| Payment Note Routes
|--------------------------------------------------------------------------
|
| Routes for payment note management
|
*/

// Basic payment note routes are now defined in backend.php
// Route::resource() is commented out to avoid duplicate routes with snake_case parameters
// Route::middleware(['auth'])->prefix('backend')->name('backend.')->group(function () {
//     Route::resource('payment-note', PaymentNoteController::class);
// });

// Additional payment note routes for new features
Route::middleware(['auth'])->prefix('backend')->name('backend.')->group(function () {
    // Create payment note from green note
    Route::get('green-note/{greenNote}/create-payment-note', [\App\Http\Controllers\Backend\GreenNote\GreenNoteController::class, 'createPaymentNote'])
        ->name('green-note.create-payment-note');

    // Draft management
    Route::get('payment-note/drafts', [PaymentNoteController::class, 'drafts'])
        ->name('payment-note.drafts');
    Route::post('payment-note/{paymentNote}/convert-to-active', [PaymentNoteController::class, 'convertDraftToActive'])
        ->name('payment-note.convert-to-active');
    Route::delete('payment-note/{paymentNote}/delete-draft', [PaymentNoteController::class, 'deleteDraft'])
        ->name('payment-note.delete-draft');

    // Hold functionality
    Route::post('payment-note/{paymentNote}/hold', [PaymentNoteController::class, 'putOnHold'])
        ->name('payment-note.hold');
    Route::post('payment-note/{paymentNote}/remove-hold', [PaymentNoteController::class, 'removeFromHold'])
        ->name('payment-note.remove-hold');

    // SuperAdmin routes
    Route::middleware(['role:Super Admin'])->prefix('payment-note')->name('payment-note.')->group(function () {
        Route::get('create-superadmin', [PaymentNoteController::class, 'createForSuperAdmin'])
            ->name('create-superadmin');
        Route::post('store-superadmin', [PaymentNoteController::class, 'storeForSuperAdmin'])
            ->name('store-superadmin');
    });
});
