<?php

use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\Auth\RegisterController;
use App\Http\Controllers\Backend\Beneficiary\BeneficiaryController;
use App\Http\Controllers\Backend\Dashboard\DashboardController;
use App\Http\Controllers\Backend\Role\RoleController;
use App\Http\Controllers\Backend\Template\TemplateController;
use App\Http\Controllers\Backend\User\UserController;
use App\Http\Controllers\Backend\Activity\ActivityController;
use App\Http\Controllers\Backend\Payment\PaymentController;
use App\Http\Controllers\Backend\Account\AccountController;
use App\Http\Controllers\Backend\Approval\ApprovalFlowController;
use App\Http\Controllers\Backend\Approval\BankLetterApprovalController;
use App\Http\Controllers\Backend\Approval\PaymentNoteApprovalController;
use App\Http\Controllers\Backend\Departments\DepartmentController;
use App\Http\Controllers\Backend\Designations\DesignationController;
use App\Http\Controllers\Backend\GreenNote\GreenNoteController;
use App\Http\Controllers\Backend\Vendor\VendorController;
use App\Http\Controllers\Backend\Import\Account\AccountController as AccountImportController;
use App\Http\Controllers\Backend\Import\Payment\PaymentController as PaymentImportController;
use App\Http\Controllers\Backend\Import\Vendor\VendorController as VendorImportController;
use App\Http\Controllers\Backend\PaymentNote\PaymentNoteController;
use App\Http\Controllers\Backend\Ratio\RatioController;
use App\Http\Controllers\Backend\Reimbursement\ReimbursementNoteController;
use App\Http\Controllers\Backend\SupportingDoc\SupportingDocController;
use App\Http\Controllers\Backend\Ticket\TicketCommentController;
use App\Http\Controllers\Backend\Ticket\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

// Auth::routes();
// Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::group(['prefix' => 'backend', 'as' => 'backend.'], function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login.attempt');
        Route::post('logout', 'logout')->name('logout');
    });
    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'showRegisterForm')->name('login');
        Route::post('register', 'register')->name('register');
    });

    // , 'prevent-back-history', 'secure_headers'
    Route::group(['middleware' => ['auth', 'throttle:30,1', 'organization.context']], function () {
        Route::controller(ActivityController::class)
            ->prefix('activity')
            ->name('activity.')
            ->group(function () {
                Route::match(['get', 'post'], '/', 'index')->name('index');
                Route::match(['get', 'post'], '/login-history/{user?}', 'loginHistory')->name('loginHistory');
            });
        Route::controller(DashboardController::class)
            ->prefix('dashboard')
            ->name('dashboard.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/filter/{id}', 'filter')->name('filter');
                Route::post('/user/green-notes', 'showUserNotes')->name('user.green.notes');
                Route::post('/user/payment-notes', 'showPaymentNotes')->name('user.payment.notes');
                Route::post('/user/reimbursement-notes', 'showReimbursementNotes')->name('user.reimbursement.notes');
                Route::post('/user/bank-letter-notes', 'showBankLetterNotes')->name('user.bank.letter.notes');
                // Fast AJAX APIs for tabs
                Route::get('/api/expense', 'apiExpense')->name('api.expense');
                Route::get('/api/payment-notes', 'apiPaymentNotes')->name('api.paymentNotes');
                Route::get('/api/reimbursements', 'apiReimbursements')->name('api.reimbursements');
                Route::get('/api/bank-rtgs', 'apiBank')->name('api.bank');
            });
        Route::controller(TemplateController::class)
            ->prefix('templates')
            ->name('templates.')
            ->group(function () {
                Route::match(['get', 'post'], '/{tpl}/{slno?}', 'templateCommon')->name('templateCommon');
                Route::match(['get', 'post'], '/generate/{tpl}/pdf', 'templateCommonGenPdf')->name('templateCommonGenPdf');
                /* Route::get('/bulk-rtgs/{slno?}', 'templateBulkrtgs')->name('bulk-rtgs');
            Route::get('/mf-axis/{slno?}', 'templateMFAxis')->name('mf-axis');
            Route::get('/mf-kotak/{slno?}', 'templateMFKotak')->name('mf-kotak');
            Route::get('/mf-sbi/{slno?}', 'templateMFSbi')->name('mf-sbi');
            Route::get('/rtgs/{slno?}', 'templateRTGS')->name('rtgs');
            Route::get('/salary/{slno?}', 'templateSalary')->name('salary');
            Route::match(['get', 'post'], '/sbi/{slno?}', 'templateSBI')->name('sbi'); */

                Route::post('/bulk-rtgs/generate/pdf/{slno?}', 'templateBulkrtgsGeneratePdf')->name('bulk-rtgs-generate-pdf');
                Route::post('/mf-axis/generate/pdf/{slno?}', 'templateMFAxisGeneratePdf')->name('mf-axis-generate-pdf');
                Route::post('/mf-kotak/generate/pdf/{slno?}', 'templateMFKotakGeneratePdf')->name('mf-kotak-generate-pdf');
                Route::post('/mf-sbi/generate/pdf/{slno?}', 'templateMFSbiGeneratePdf')->name('mf-sbi-generate-pdf');
                Route::post('/rtgs/generate/pdf/{slno?}', 'templateRTGSGeneratePdf')->name('rtgs-generate-pdf');
                Route::post('/salary/generate/pdf/{slno?}', 'templateSalaryGeneratePdf')->name('salary-generate-pdf');
                Route::post('/sbi/generate/pdf/{slno?}', 'templateSBIGeneratePdf')->name('sbi-generate-pdf');
            });
        Route::controller(RoleController::class)
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                // returns the home page with all roles
                Route::get('/', 'index')->name('index')->middleware('can:view-role');
                // returns the form for adding a role
                Route::get('/create', 'create')->name('create')->middleware('can:create-role');
                // adds a role to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-role');
                // returns a page that shows a full role
                Route::get('/{role}', 'show')->name('show')->middleware('can:view-role');
                // returns the form for editing a role
                Route::get('/{role}/edit', 'edit')->name('edit')->middleware('can:edit-role');

                // updates a role
                Route::put('/{role}', 'update')->name('update')->middleware('can:edit-role');
                // deletes a role
                Route::delete('/{role}', 'destroy')->name('destroy')->middleware('can:delete-role');
            });
        Route::controller(TicketCommentController::class)
            ->prefix('ticketComments')
            ->name('ticketComments.')
            ->group(function () {
                // returns the home page with all roles
                Route::get('/', 'index')->name('index');
                // returns the form for adding a role
                Route::get('/create', 'create')->name('create');
                // adds a role to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full role
                Route::get('/{ticketComment}', 'show')->name('show');
                // returns the form for editing a ticketComment
                Route::get('/{ticketComment}/edit', 'edit')->name('edit');

                // updates a ticketComment
                Route::put('/{ticketComment}', 'update')->name('update');
                // deletes a ticketComment
                Route::delete('/{ticketComment}', 'destroy')->name('destroy');
            });
        Route::controller(TicketController::class)
            ->prefix('tickets')
            ->name('tickets.')
            ->group(function () {
                // returns the home page with all roles
                Route::get('/', 'index')->name('index');
                // returns the form for adding a role
                Route::get('/create', 'create')->name('create');
                // adds a role to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full role
                Route::get('/{ticket}', 'show')->name('show');
                // returns the form for editing a role
                Route::get('/{ticket}/edit', 'edit')->name('edit');
                // returns the form for editing a ticket
                // updates a role
                Route::put('/{ticket}', 'update')->name('update');
                // deletes a role
                Route::delete('/{ticket}', 'destroy')->name('destroy');
                Route::get('/{ticket}/downloadAll', 'downloadAll')->name('downloadAll');
            });
        Route::controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                // returns the home page with all users
                Route::get('/', 'index')->name('index')->middleware('can:view-user');
                // returns the form for adding a role
                Route::get('/create', 'create')->name('create')->middleware('can:create-user');
                // adds a user to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-user');
                // returns a page that shows a full user
                Route::get('/{user}', 'show')->name('show')->middleware('can:edit-user');
                // returns the form for editing a user
                Route::get('/{user}/edit', 'edit')->name('edit')->middleware('can:edit-user');
                // updates a user
                Route::put('/{user}', 'update')->name('update')->middleware('can:edit-user');
                // deletes a user
                Route::delete('/{user}', 'destroy')->name('destroy')->middleware('can:delete-user');
                // profile
                Route::get('/{user}/profile', 'profile')->name('profile');
                Route::put('/{user}/my', 'updateProfile')->name('my.profile');
            });
        Route::controller(BeneficiaryController::class)
            ->prefix('beneficiaries')
            ->name('beneficiaries.')
            ->group(function () {
                // returns the home page with all beneficiarys
                Route::get('/', 'index')->name('index');
                // returns the form for adding a role
                Route::get('/create', 'create')->name('create');
                // adds a beneficiary to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full beneficiary
                Route::get('/{beneficiary}', 'show')->name('show');
                // returns the form for editing a beneficiary
                Route::get('/{beneficiary}/edit', 'edit')->name('edit');
                // updates a beneficiary
                Route::put('/{beneficiary}', 'update')->name('update');
                // deletes a beneficiary
                Route::delete('/{beneficiary}', 'destroy')->name('destroy');
            });

        Route::controller(PaymentController::class)
            ->prefix('payments')
            ->name('payments.')
            ->group(function () {
                // returns the home page with all payments
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-payment');

                // returns the home page with all payments
                Route::get('/getPayments', 'getPayments')->name('getPayments')->middleware('can:view-payment');
                // returns the form for adding a payment
                Route::get('/create', 'create')->name('create')->middleware('can:create-payment');
                // adds a payment to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-payment');
                // returns a page that shows a full payment
                Route::get('/{payment}', 'show')->name('show')->middleware('can:view-payment');

                // returns the form for editing a payment
                Route::get('/{payment}/edit', 'edit')->name('edit')->middleware('can:edit-payment');
                // updates a payment
                Route::put('/{payment}', 'update')->name('update')->middleware('can:edit-payment');
                // deletes a payment
                Route::delete('/{payment}', 'destroy')->name('destroy')->middleware('can:delete-payment');
                Route::post('/create-bank-letter', 'bankLetter')->name('createBankLetter');
                Route::post('/log-update', 'logUpdate')->name('logUpdate');

                Route::get('/template/{temp_type}', 'getFromAccount')->name('getFromAccount');
                Route::get('/shortcut/{id}', 'shortcut')->name('shortcut');
                Route::post('/searchVendor', 'searchVendor')->name('searchVendor');
                Route::post('/ratio', 'ratio')->name('ratio');
                Route::post('/searchFromVendor', 'searchFromVendor')->name('searchFromVendor');
                Route::post('/searchProject', 'searchProject')->name('searchProject');
                Route::post('/addRequestInQueue', 'addRequestInQueue')->name('addRequestInQueue');
                Route::post('/delete/request/in/queue', 'deleteRequestInQueue')->name('deleteRequestInQueue');

                Route::get('/edit/{slno}/request', 'editPaymentRequest')->name('editPaymentRequest');
                Route::put('/update/{slno}/request', 'updatePaymentRequest')->name('updatePaymentRequest');
                Route::get('/delete/{slno}/request/{id}', 'deleteRequestItem')->name('deleteRequestItem');
            });
        Route::controller(BankLetterApprovalController::class)
            ->prefix('bank-letter')
            ->name('bank-letter.')
            ->group(function () {
                // returns the home page with all payments
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a payment
                Route::get('/create', 'create')->name('create');
                // adds a payment to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full payment
                Route::get('/{payment}', 'show')->name('show');

                // returns the form for editing a payment
                Route::get('/{payment}/edit', 'edit')->name('edit');
                // updates a payment
                Route::put('/{payment}', 'update')->name('update');
                // deletes a payment
                Route::delete('/{payment}', 'destroy')->name('destroy');
            });
        Route::controller(AccountController::class)
            ->prefix('accounts')
            ->name('accounts.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full account
                Route::get('/{account}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('/{account}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{account}', 'update')->name('update');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy');
            });
        Route::controller(RatioController::class)
            ->prefix('ratio')
            ->name('ratio.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-ratio');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-ratio');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-ratio');
                // returns a page that shows a full account
                Route::get('/{account}', 'show')->name('show')->middleware('can:view-ratio');
                // returns the form for editing a account
                Route::get('/edit', 'edit')->name('edit')->middleware('can:edit-ratio');
                // updates a account
                Route::put('/{account}', 'update')->name('update')->middleware('can:view-ratio');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy')->middleware('can:delete-ratio');
                Route::get('/amount', 'amount')->name('amount')->middleware('can:view-ratio');
            });
        Route::controller(GreenNoteController::class)
            ->prefix('note')
            ->name('note.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-note');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-note');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-note');
                // returns a page that shows a full account
                Route::get('show/{account}', 'show')->name('show')->middleware('can:view-note');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit')->middleware('can:edit-note');
                // updates a account
                Route::put('/{account}', 'update')->name('update')->middleware('can:edit-note');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy')->middleware('can:delete-note');
                Route::get('/rule', 'rule')->name('rule')->middleware('can:view-rule');
                Route::get('/get-projects', 'getProjects')->name('getProjects');
                Route::get('/export-note', 'exportNoteExcel')->name('export.note.excel');
                Route::get('/get-vendors', 'getVendorsByProject')->name('getVendors');
                Route::get('/download-green-note/{id}', 'downloadGreenNotePdf')->name('download');
                Route::get('/view-green-note/{id}', 'viewGreenNotePdf')->name('view.pdf');
                Route::get('/create-payment-note/{id}', 'paymentNote')->name('create.payment.note');
                Route::post('/import-users', 'importUsers')->name('import.users');
            });
        Route::controller(PaymentNoteController::class)
            ->prefix('payment-note')
            ->name('payment-note.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-payment-note');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-payment-note');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-payment-note');
                // returns a page that shows a full account
                Route::get('show/{account}', 'show')->name('show')->middleware('can:view-payment-note');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit')->middleware('can:edit-payment-note');
                // updates a account
                Route::put('/{account}', 'update')->name('update')->middleware('can:edit-payment-note');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy')->middleware('can:delete-payment-note');
                Route::get('/rule', 'rule')->name('rule')->middleware('can:payment-note-view-rule');
                Route::get('/download-payment-note/{id}', 'downloadGreenNotePdf')->name('download');
                Route::post('/updateUtr', 'updateUtr')->name('updateUtr');
            });
        Route::controller(ReimbursementNoteController::class)
            ->prefix('reimbursement-note')
            ->name('reimbursement-note.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-reimbursement-note');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-reimbursement-note');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-reimbursement-note');
                // returns a page that shows a full account
                Route::get('show/{account}', 'show')->name('show')->middleware('can:view-reimbursement-note');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit')->middleware('can:edit-reimbursement-note');
                // updates a account
                Route::put('/{account}', 'update')->name('update')->middleware('can:edit-reimbursement-note');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy')->middleware('can:delete-reimbursement-note');
                Route::get('/rule', 'rule')->name('rule')->middleware('can:reimbursement-note-create-rule');
                Route::get('/download-note/{id}', 'downloadNotePdf')->name('download');
                Route::get('/view-note/{id}', 'viewNotePdf')->name('view.pdf');
                Route::delete('/delete-file/{id}/file/{filename}', 'deleteFile')->name('file.delete');
                Route::post('/update/approval-log/{id}', 'approvalLogUpdate')->name('approvalLogUpdate');
                Route::get('/create-payment-note/{id}', 'paymentNote')->name('create.payment.note');
                Route::get('/user-selection', 'userSelection')->name('create.user.selection');
                Route::post('/select-user', 'selectUser')->name('create.user.select');
            });
        Route::controller(PaymentNoteApprovalController::class)
            ->prefix('payment-note-approval')
            ->name('payment-note-approval.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full account
                Route::get('show/{account}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{account}', 'update')->name('update');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy');
                Route::post('/update/approval-log/{id}', 'approvalLogUpdate')->name('approvalLogUpdate');
            });
        Route::controller(ApprovalFlowController::class)
            ->prefix('approval')
            ->name('approval.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                Route::post('/approval-step', 'storeApprovalStep')->name('storeApprovalStep');
                // returns a page that shows a full account
                Route::get('show/{account}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{account}', 'update')->name('update');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy');
                Route::delete('approvalStep/{id}', 'approvalStepDestroy')->name('approvalStepDestroy');
                Route::post('/update/approval-log/{id}', 'approvalLogUpdate')->name('approvalLogUpdate');
                Route::post('/send/approval/{id}', 'sendApproval')->name('sendApproval');
            });
        Route::controller(CommentController::class)
            ->prefix('comments')
            ->name('comments.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full account
                Route::get('/{account}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{account}', 'update')->name('update');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy');
            });
        Route::controller(DesignationController::class)
            ->prefix('designations')
            ->name('designations.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-designation');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-designation');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-designation');
                // returns a page that shows a full account
                Route::get('/{id}', 'show')->name('show')->middleware('can:view-designation');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit')->middleware('can:edit-designation');
                // updates a account
                Route::put('/{id}', 'update')->name('update')->middleware('can:edit-designation');
                // deletes a account
                Route::delete('/{id}', 'destroy')->name('destroy')->middleware('can:delete-designation');
                Route::get('/amount', 'amount')->name('amount');
            });
        Route::controller(DepartmentController::class)
            ->prefix('departments')
            ->name('departments.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')
                    ->name('index')
                    ->middleware('can:view-department');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create')->middleware('can:create-department');
                // adds a account to the database
                Route::post('/', 'store')->name('store')->middleware('can:create-department');
                // returns a page that shows a full account
                Route::get('/{id}', 'show')->name('show')->middleware('can:edit-department');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit')->middleware('can:edit-department');
                // updates a account
                Route::put('/{id}', 'update')->name('update')->middleware('can:edit-department');
                // deletes a account
                Route::delete('/{id}', 'destroy')->name('destroy')->middleware('can:delete-department');
                Route::get('/amount', 'amount')->name('amount');
            });
        Route::controller(SupportingDocController::class)
            ->prefix('documents')
            ->name('documents.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full account
                Route::get('/{id}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('{id}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{id}', 'update')->name('update');
                // deletes a account
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/amount', 'amount')->name('amount');
            });
        Route::controller(VendorController::class)
            ->prefix('vendors')
            ->name('vendors.')
            ->group(function () {
                // returns the home page with all vendors
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a vendor
                Route::get('/create', 'create')->name('create');
                // adds a vendor to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full vendor
                Route::get('/{vendor}', 'show')->name('show');
                // returns the form for editing a vendor
                Route::get('/{vendor}/edit', 'edit')->name('edit');
                // updates a vendor
                Route::put('/{vendor}', 'update')->name('update');
                // deletes a vendor
                Route::delete('/{vendor}', 'destroy')->name('destroy');
            });
        Route::controller(PaymentImportController::class)
            ->prefix('import/payments')
            ->name('import.payments.')
            ->group(function () {
                // returns the home page with all payments
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a payment
                Route::get('/create', 'create')->name('create');
                // adds a payment to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full payment
                Route::get('/{payment}', 'show')->name('show');
                // returns the form for editing a payment
                Route::get('/{payment}/edit', 'edit')->name('edit');
                // updates a payment
                Route::put('/{payment}', 'update')->name('update');
                // deletes a payment
                Route::delete('/{payment}', 'destroy')->name('destroy');
            });
        Route::controller(AccountImportController::class)
            ->prefix('import/accounts')
            ->name('import.accounts.')
            ->group(function () {
                // returns the home page with all accounts
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a account
                Route::get('/create', 'create')->name('create');
                // adds a account to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full account
                Route::get('/{account}', 'show')->name('show');
                // returns the form for editing a account
                Route::get('/{account}/edit', 'edit')->name('edit');
                // updates a account
                Route::put('/{account}', 'update')->name('update');
                // deletes a account
                Route::delete('/{account}', 'destroy')->name('destroy');
            });
        Route::controller(VendorImportController::class)
            ->prefix('import/vendors')
            ->name('import.vendors.')
            ->group(function () {
                // returns the home page with all vendors
                Route::match(['get', 'post'], '/', 'index')->name('index');
                // returns the form for adding a vendor
                Route::get('/create', 'create')->name('create');
                // adds a vendor to the database
                Route::post('/', 'store')->name('store');
                // returns a page that shows a full vendor
                Route::get('/{vendor}', 'show')->name('show');
                // returns the form for editing a vendor
                Route::get('/{vendor}/edit', 'edit')->name('edit');
                // updates a vendor
                Route::put('/{vendor}', 'update')->name('update');
                // deletes a vendor
                Route::delete('/{vendor}', 'destroy')->name('destroy');
            });

        // Organization management routes
        Route::controller(OrganizationController::class)
            ->prefix('organizations')
            ->name('organizations.')
            ->group(function () {
                // Organization switching routes (accessible by all authenticated users)
                Route::post('/switch', 'switch')->name('switch');
                Route::get('/current', 'getCurrentOrganizations')->name('current');
                
                // Debug route for testing
                Route::get('/debug-switch/{id}', function($id) {
                    try {
                        $user = auth()->user();
                        \Log::info("Debug switch: User {$user->id} switching to org {$id}");
                        
                        $result = $user->switchToOrganization($id);
                        
                        return response()->json([
                            'success' => $result,
                            'user_id' => $user->id,
                            'current_org_id' => $user->current_organization_id,
                            'logs' => 'Check storage/logs/laravel.log for detailed logs'
                        ]);
                    } catch (\Exception $e) {
                        \Log::error("Debug switch error: " . $e->getMessage());
                        return response()->json([
                            'success' => false,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ], 500);
                    }
                })->name('debug-switch');
                
                // Test migration directly
                Route::get('/test-migration/{id}', function($id) {
                    try {
                        $user = auth()->user();
                        $org = \App\Models\Organization::on('mysql')->find($id);
                        
                        if (!$org) {
                            return response()->json(['error' => 'Organization not found'], 404);
                        }
                        
                        \Log::info("Testing migration to: {$org->database_name}");
                        
                        // Test database connection
                        config(['database.connections.organization.database' => $org->database_name]);
                        \DB::purge('organization');
                        \DB::reconnect('organization');
                        
                        $canConnect = \DB::connection('organization')->getPdo() ? true : false;
                        $userExists = \DB::connection('organization')->table('users')->where('email', $user->email)->exists();
                        
                        return response()->json([
                            'organization' => $org->name,
                            'database' => $org->database_name,
                            'can_connect' => $canConnect,
                            'user_exists' => $userExists,
                            'user_email' => $user->email
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ], 500);
                    }
                })->name('test-migration');
                
                // Organization management routes (SuperAdmin only)
                Route::middleware('role:superadmin')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{organization}', 'show')->name('show');
                    Route::get('/{organization}/edit', 'edit')->name('edit');
                    Route::put('/{organization}', 'update')->name('update');
                    Route::delete('/{organization}', 'destroy')->name('destroy');
                    Route::patch('/{organization}/toggle-status', 'toggleStatus')->name('toggle-status');
                });
            });
    });
});
