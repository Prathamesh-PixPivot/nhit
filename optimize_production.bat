@echo off
echo Optimizing Laravel for production...

REM Set production environment
set APP_ENV=production
set APP_DEBUG=false

REM Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

REM Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

REM Optimize autoloader
composer dump-autoload --optimize --no-dev

REM Build production assets
npm run build

REM Set proper permissions
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T

echo Production optimization completed!
pause
