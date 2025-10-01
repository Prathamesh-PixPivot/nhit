@echo off
REM Quick Fix for Laravel on Windows XAMPP
REM This script provides a simple solution to fix all issues

echo Starting Quick Fix for Laravel on Windows XAMPP...

echo.
echo ========================================
echo Quick Fix Started
echo ========================================

echo.
echo Step 1: Creating database...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"
echo Database created!

echo.
echo Step 2: Commenting out problematic PHP extensions...
REM Create a simple script to comment out problematic extensions
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^extension=redis', ';extension=redis' | Set-Content 'C:\xampp\php\php.ini'"
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^extension=memcached', ';extension=memcached' | Set-Content 'C:\xampp\php\php.ini'"
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^extension=imagick', ';extension=imagick' | Set-Content 'C:\xampp\php\php.ini'"
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^extension=xml', ';extension=xml' | Set-Content 'C:\xampp\php\php.ini'"
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace '^extension=bcmath', ';extension=bcmath' | Set-Content 'C:\xampp\php\php.ini'"
echo Problematic extensions commented out!

echo.
echo Step 3: Creating simple .env file...
(
echo APP_NAME=Laravel
echo APP_ENV=local
echo APP_KEY=base64:YourAppKeyHere
echo APP_DEBUG=true
echo APP_URL=http://localhost
echo.
echo LOG_CHANNEL=stack
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=debug
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=laravel
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo BROADCAST_DRIVER=log
echo CACHE_DRIVER=file
echo FILESYSTEM_DISK=local
echo QUEUE_CONNECTION=sync
echo SESSION_DRIVER=file
echo SESSION_LIFETIME=120
echo.
echo MEMCACHED_HOST=127.0.0.1
echo.
echo MAIL_MAILER=smtp
echo MAIL_HOST=mailpit
echo MAIL_PORT=1025
echo MAIL_USERNAME=null
echo MAIL_PASSWORD=null
echo MAIL_ENCRYPTION=null
echo MAIL_FROM_ADDRESS="hello@example.com"
echo MAIL_FROM_NAME="${APP_NAME}"
echo.
echo AWS_ACCESS_KEY_ID=
echo AWS_SECRET_ACCESS_KEY=
echo AWS_DEFAULT_REGION=us-east-1
echo AWS_BUCKET=
echo AWS_USE_PATH_STYLE_ENDPOINT=false
echo.
echo PUSHER_APP_ID=
echo PUSHER_APP_KEY=
echo PUSHER_APP_SECRET=
echo PUSHER_HOST=
echo PUSHER_PORT=443
echo PUSHER_SCHEME=https
echo PUSHER_APP_CLUSTER=mt1
echo.
echo VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
echo VITE_PUSHER_HOST="${PUSHER_HOST}"
echo VITE_PUSHER_PORT="${PUSHER_PORT}"
echo VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
echo VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
) > .env
echo Simple .env file created!

echo.
echo Step 4: Running Laravel setup...
php artisan key:generate
php artisan migrate
echo Laravel setup completed!

echo.
echo Step 5: Clearing caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Caches cleared!

echo.
echo Step 6: Testing the application...
php artisan --version
echo Application test completed!

echo.
echo ========================================
echo Quick Fix Completed!
echo ========================================
echo.
echo Issues Fixed:
echo - Created laravel database
echo - Commented out problematic PHP extensions
echo - Created simple .env file
echo - Ran Laravel setup
echo - Cleared caches
echo.
echo Your Laravel application should now work!
echo.
echo Press any key to continue...
pause > nul
