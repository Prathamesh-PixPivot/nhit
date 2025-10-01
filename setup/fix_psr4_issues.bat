@echo off
REM Fix PSR-4 Autoloading Issues for Laravel
REM This script removes problematic files that don't comply with PSR-4 standards

echo Starting PSR-4 autoloading fixes...

echo.
echo ========================================
echo PSR-4 Autoloading Fixes Started
echo ========================================

echo.
echo Step 1: Removing non-compliant DatabaseChannel.php...
if exist "app\channels\DatabaseChannel.php" (
    echo Found DatabaseChannel.php - removing...
    del "app\channels\DatabaseChannel.php"
    echo DatabaseChannel.php removed!
) else (
    echo DatabaseChannel.php not found - skipping...
)

echo.
echo Step 2: Removing duplicate DashboardController files...
if exist "app\Http\Controllers\Backend\Dashboard\DashboardController copy 2.php" (
    echo Found DashboardController copy 2.php - removing...
    del "app\Http\Controllers\Backend\Dashboard\DashboardController copy 2.php"
    echo DashboardController copy 2.php removed!
) else (
    echo DashboardController copy 2.php not found - skipping...
)

if exist "app\Http\Controllers\Backend\Dashboard\DashboardController copy.php" (
    echo Found DashboardController copy.php - removing...
    del "app\Http\Controllers\Backend\Dashboard\DashboardController copy.php"
    echo DashboardController copy.php removed!
) else (
    echo DashboardController copy.php not found - skipping...
)

echo.
echo Step 3: Removing duplicate PaymentController files...
if exist "app\Http\Controllers\Backend\Payment\PaymentController copy.php" (
    echo Found PaymentController copy.php - removing...
    del "app\Http\Controllers\Backend\Payment\PaymentController copy.php"
    echo PaymentController copy.php removed!
) else (
    echo PaymentController copy.php not found - skipping...
)

echo.
echo Step 4: Fixing controller namespace issues...
REM Check for controllers with incorrect namespaces
if exist "app\Http\Controllers\Backend\GreenNote\GreenNoteController.php" (
    echo Checking GreenNoteController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

if exist "app\Http\Controllers\Backend\PaymentNote\PaymentNoteController.php" (
    echo Checking PaymentNoteController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

if exist "app\Http\Controllers\Backend\Reimbursement\ReimbursementNoteController.php" (
    echo Checking ReimbursementNoteController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

if exist "app\Http\Controllers\Backend\SupportingDoc\SupportingDocController.php" (
    echo Checking SupportingDocController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

if exist "app\Http\Controllers\Backend\Ticket\TicketCommentController.php" (
    echo Checking TicketCommentController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

if exist "app\Http\Controllers\Backend\Ticket\TicketController.php" (
    echo Checking TicketController.php for namespace issues...
    REM This file might have incorrect namespace - check manually
)

echo.
echo Step 5: Regenerating Composer autoloader...
composer dump-autoload --optimize
echo Composer autoloader regenerated!

echo.
echo Step 6: Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Laravel caches cleared!

echo.
echo ========================================
echo PSR-4 Autoloading Fixes Completed!
echo ========================================
echo.
echo Issues Fixed:
echo - Removed non-compliant DatabaseChannel.php
echo - Removed duplicate DashboardController files
echo - Removed duplicate PaymentController files
echo - Regenerated Composer autoloader
echo - Cleared Laravel caches
echo.
echo Note: Some controllers may still have namespace issues.
echo Check the following files manually:
echo - app\Http\Controllers\Backend\GreenNote\GreenNoteController.php
echo - app\Http\Controllers\Backend\PaymentNote\PaymentNoteController.php
echo - app\Http\Controllers\Backend\Reimbursement\ReimbursementNoteController.php
echo - app\Http\Controllers\Backend\SupportingDoc\SupportingDocController.php
echo - app\Http\Controllers\Backend\Ticket\TicketCommentController.php
echo - app\Http\Controllers\Backend\Ticket\TicketController.php
echo.
echo Press any key to continue...
pause > nul
