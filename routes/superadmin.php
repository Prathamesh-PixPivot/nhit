use App\Http\Controllers\Backend\Permissions\PermissionController;

/*
|--------------------------------------------------------------------------
| SuperAdmin Routes
|--------------------------------------------------------------------------
|
| Routes for SuperAdmin CRUD operations on all models
|
*/

Route::middleware(['auth', 'web'])->prefix('backend/superadmin')->name('backend.superadmin.')->group(function () {
    
    // SuperAdmin Dashboard
    Route::get('/', [SuperAdminCrudController::class, 'dashboard'])->name('dashboard');
    // System Statistics
    Route::get('/stats', [SuperAdminCrudController::class, 'systemStats'])->name('stats');

    // Permissions Management
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/update', [PermissionController::class, 'update'])->name('update');
        Route::post('/test', [PermissionController::class, 'test'])->name('test');
        Route::post('/reset', [PermissionController::class, 'reset'])->name('reset');
        Route::get('/roles', [PermissionController::class, 'getRoles'])->name('roles');
    });

    // Payment Notes CRUD
    Route::prefix('payment-notes')->name('payment-notes.')->group(function () {
        Route::get('/', [SuperAdminCrudController::class, 'paymentNotes'])->name('index');
        Route::get('/create', [SuperAdminCrudController::class, 'createPaymentNote'])->name('create');
        Route::post('/', [SuperAdminCrudController::class, 'storePaymentNote'])->name('store');
        Route::get('/{paymentNote}/edit', [SuperAdminCrudController::class, 'editPaymentNote'])->name('edit');
        Route::put('/{paymentNote}', [SuperAdminCrudController::class, 'updatePaymentNote'])->name('update');
        Route::delete('/{paymentNote}', [SuperAdminCrudController::class, 'destroyPaymentNote'])->name('destroy');
    });

    // Green Notes CRUD
    Route::prefix('green-notes')->name('green-notes.')->group(function () {
        Route::get('/', [SuperAdminCrudController::class, 'greenNotes'])->name('index');
        Route::get('/create', [SuperAdminCrudController::class, 'createGreenNote'])->name('create');
        Route::get('/{greenNote}', [SuperAdminCrudController::class, 'showGreenNote'])->name('show');
        Route::get('/{greenNote}/edit', [SuperAdminCrudController::class, 'editGreenNote'])->name('edit');
        Route::delete('/{greenNote}', [SuperAdminCrudController::class, 'destroyGreenNote'])->name('destroy');
    });

    // Vendors CRUD
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/', [SuperAdminCrudController::class, 'vendors'])->name('index');
        Route::get('/create', [SuperAdminCrudController::class, 'createVendor'])->name('create');
        Route::post('/', [SuperAdminCrudController::class, 'storeVendor'])->name('store');
        Route::get('/{vendor}', [SuperAdminCrudController::class, 'showVendor'])->name('show');
        Route::get('/{vendor}/edit', [SuperAdminCrudController::class, 'editVendor'])->name('edit');
        Route::put('/{vendor}', [SuperAdminCrudController::class, 'updateVendor'])->name('update');
        Route::delete('/{vendor}', [SuperAdminCrudController::class, 'destroyVendor'])->name('destroy');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SuperAdminCrudController::class, 'users'])->name('index');
    });
});
