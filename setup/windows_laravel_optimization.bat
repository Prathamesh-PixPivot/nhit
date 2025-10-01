@echo off
REM Laravel Optimization Script for Windows XAMPP
REM This script optimizes Laravel application for better performance

echo Starting Laravel optimization for Windows XAMPP...

REM Set variables
set PROJECT_PATH=%CD%
set XAMPP_PATH=C:\xampp
set PHP_PATH=%XAMPP_PATH%\php
set APACHE_PATH=%XAMPP_PATH%\apache
set MYSQL_PATH=%XAMPP_PATH%\mysql

echo Project Path: %PROJECT_PATH%
echo XAMPP Path: %XAMPP_PATH%

echo.
echo ========================================
echo Laravel Optimization Started
echo ========================================

echo.
echo Step 1: Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan queue:clear
echo Laravel caches cleared!

echo.
echo Step 2: Optimizing Composer autoloader...
composer dump-autoload --optimize --no-dev
echo Composer autoloader optimized!

echo.
echo Step 3: Building Laravel caches...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo Laravel caches built!

echo.
echo Step 4: Optimizing database...
php artisan migrate --force
echo Database optimized!

echo.
echo Step 5: Setting proper permissions...
REM Set storage permissions
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
icacls public\uploads /grant Everyone:F /T
echo Permissions set!

echo.
echo Step 6: Optimizing assets...
REM Install NPM dependencies if package.json exists
if exist package.json (
    echo Installing NPM dependencies...
    npm install --production
    echo NPM dependencies installed!
    
    echo Building production assets...
    npm run build
    echo Production assets built!
) else (
    echo No package.json found, skipping NPM optimization...
)

echo.
echo Step 7: Creating optimized .env file...
REM Create optimized .env file
(
echo APP_NAME=Laravel
echo APP_ENV=production
echo APP_KEY=base64:YourAppKeyHere
echo APP_DEBUG=false
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
echo REDIS_HOST=127.0.0.1
echo REDIS_PASSWORD=null
echo REDIS_PORT=6379
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
) > .env.production
echo Optimized .env file created!

echo.
echo Step 8: Creating performance monitoring script...
REM Create performance monitoring script
(
echo @echo off
echo echo Laravel Performance Monitoring...
echo.
echo REM Check PHP version
echo php --version
echo.
echo REM Check memory usage
echo php -r "echo 'Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;"
echo php -r "echo 'Max Execution Time: ' . ini_get('max_execution_time') . PHP_EOL;"
echo.
echo REM Check OPcache status
echo php -r "if (extension_loaded('opcache')) { echo 'OPcache: Enabled' . PHP_EOL; } else { echo 'OPcache: Disabled' . PHP_EOL; }"
echo.
echo REM Check database connection
echo php artisan tinker --execute="echo 'Database: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed') . PHP_EOL;"
echo.
echo REM Check cache status
echo php artisan cache:clear
echo echo Cache cleared successfully
echo.
echo REM Check storage permissions
echo if exist storage\logs echo Storage logs: Accessible
echo if exist storage\framework echo Storage framework: Accessible
echo.
echo REM Check Laravel optimization
echo php artisan optimize
echo echo Laravel optimized successfully
echo.
echo echo Monitoring completed!
echo pause
) > monitor_performance.bat
echo Performance monitoring script created!

echo.
echo Step 9: Creating development setup script...
REM Create development setup script
(
echo @echo off
echo echo Setting up Laravel development environment...
echo.
echo REM Set development environment
echo set APP_ENV=local
echo set APP_DEBUG=true
echo.
echo REM Clear caches
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo.
echo REM Install dependencies
echo composer install
echo npm install
echo.
echo REM Build development assets
echo npm run dev
echo.
echo REM Run migrations
echo php artisan migrate
echo.
echo REM Seed database
echo php artisan db:seed
echo.
echo echo Development environment setup completed!
echo pause
) > setup_development.bat
echo Development setup script created!

echo.
echo Step 10: Creating production deployment script...
REM Create production deployment script
(
echo @echo off
echo echo Deploying Laravel to production...
echo.
echo REM Set production environment
echo set APP_ENV=production
echo set APP_DEBUG=false
echo.
echo REM Clear all caches
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo php artisan event:clear
echo.
echo REM Rebuild optimized caches
echo php artisan config:cache
echo php artisan route:cache
echo php artisan view:cache
echo php artisan event:cache
echo.
echo REM Optimize autoloader
echo composer dump-autoload --optimize --no-dev
echo.
echo REM Build production assets
echo npm run build
echo.
echo REM Set proper permissions
echo icacls storage /grant Everyone:F /T
echo icacls bootstrap\cache /grant Everyone:F /T
echo.
echo REM Run migrations
echo php artisan migrate --force
echo.
echo REM Optimize Laravel
echo php artisan optimize
echo.
echo echo Production deployment completed!
echo pause
) > deploy_production.bat
echo Production deployment script created!

echo.
echo Step 11: Creating backup script...
REM Create backup script
(
echo @echo off
echo echo Creating Laravel backup...
echo.
echo REM Create backup directory
echo mkdir backups 2>nul
echo.
echo REM Create timestamp
echo for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
echo set "YY=%dt:~2,2%" ^& set "YYYY=%dt:~0,4%" ^& set "MM=%dt:~4,2%" ^& set "DD=%dt:~6,2%"
echo set "HH=%dt:~8,2%" ^& set "Min=%dt:~10,2%" ^& set "Sec=%dt:~12,2%"
echo set "timestamp=%YYYY%%MM%%DD%_%HH%%Min%%Sec%"
echo.
echo REM Backup database
echo mysqldump -u root -p laravel > backups\database_%timestamp%.sql
echo.
echo REM Backup application files
echo xcopy /E /I /Y storage backups\storage_%timestamp%
echo xcopy /E /I /Y public\uploads backups\uploads_%timestamp%
echo.
echo REM Create archive
echo powershell Compress-Archive -Path backups\* -DestinationPath backups\laravel_backup_%timestamp%.zip
echo.
echo echo Backup completed: backups\laravel_backup_%timestamp%.zip
echo pause
) > backup_laravel.bat
echo Backup script created!

echo.
echo Step 12: Creating maintenance script...
REM Create maintenance script
(
echo @echo off
echo echo Laravel Maintenance Script...
echo.
echo REM Put site in maintenance mode
echo php artisan down
echo.
echo REM Clear all caches
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo php artisan event:clear
echo.
echo REM Optimize Laravel
echo php artisan optimize
echo.
echo REM Clear old logs
echo del /Q storage\logs\*.log
echo.
echo REM Clear old cache files
echo del /Q storage\framework\cache\data\*
echo del /Q storage\framework\sessions\*
echo del /Q storage\framework\views\*
echo.
echo REM Restart services
echo net stop mysql
echo net start mysql
echo net stop apache2.4
echo net start apache2.4
echo.
echo REM Take site out of maintenance mode
echo php artisan up
echo.
echo echo Maintenance completed!
echo pause
) > maintenance_laravel.bat
echo Maintenance script created!

echo.
echo ========================================
echo Laravel Optimization Completed!
echo ========================================
echo.
echo Created files:
echo - monitor_performance.bat (Performance monitoring)
echo - setup_development.bat (Development setup)
echo - deploy_production.bat (Production deployment)
echo - backup_laravel.bat (Backup script)
echo - maintenance_laravel.bat (Maintenance script)
echo - .env.production (Optimized environment file)
echo.
echo Next steps:
echo 1. Run monitor_performance.bat to check performance
echo 2. Run setup_development.bat for development
echo 3. Run deploy_production.bat for production
echo 4. Test your application
echo.
echo Press any key to continue...
pause > nul
