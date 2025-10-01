@echo off
echo Optimizing Laravel application...

REM Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

REM Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

REM Optimize autoloader
composer dump-autoload --optimize

REM Run database migrations
php artisan migrate --force

echo Laravel optimization completed!
pause
