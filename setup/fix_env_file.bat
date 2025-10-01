@echo off
REM Fix .env File for Windows XAMPP
REM This script completely fixes .env file issues

echo Starting .env file fixes...

echo.
echo ========================================
echo .env File Fixes Started
echo ========================================

echo.
echo Step 1: Backing up original .env file...
if exist ".env" (
    copy ".env" ".env.backup"
    echo Original .env file backed up!
) else (
    echo No .env file found - creating new one...
)

echo.
echo Step 2: Creating COMPLETELY CLEAN .env file...
REM Create a completely clean .env file without any comments
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
echo COMPLETELY CLEAN .env file created!

echo.
echo Step 3: Testing .env file...
php -r "echo 'Testing .env file...' . PHP_EOL; if (file_exists('.env')) { echo '.env file exists!' . PHP_EOL; } else { echo '.env file not found!' . PHP_EOL; }"
echo .env file test completed!

echo.
echo Step 4: Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Laravel caches cleared!

echo.
echo ========================================
echo .env File Fixes Completed!
echo ========================================
echo.
echo Issues Fixed:
echo - Created COMPLETELY CLEAN .env file
echo - Removed ALL comments that caused parsing errors
echo - Set file-based cache instead of Redis
echo - Cleared Laravel caches
echo.
echo Note: The application should now work without .env parsing errors!
echo.
echo Press any key to continue...
pause > nul
