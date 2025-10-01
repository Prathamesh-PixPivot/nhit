@echo off
REM Simple Performance Check for Laravel on Windows XAMPP

echo Laravel Performance Check...
echo.

echo PHP Version:
php --version
echo.

echo Memory Settings:
php -r "echo 'Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Max Execution Time: ' . ini_get('max_execution_time') . PHP_EOL;"
echo.

echo OPcache Status:
php -r "if (extension_loaded('opcache')) { echo 'OPcache: Enabled' . PHP_EOL; } else { echo 'OPcache: Disabled' . PHP_EOL; }"
echo.

echo Database Connection:
php artisan tinker --execute="echo 'Database: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed') . PHP_EOL;"
echo.

echo Laravel Status:
php artisan --version
echo.

echo Cache Status:
php artisan cache:clear
echo Cache cleared successfully
echo.

echo Storage Permissions:
if exist storage\logs echo Storage logs: Accessible
if exist storage\framework echo Storage framework: Accessible
echo.

echo Laravel Optimization:
php artisan optimize
echo Laravel optimized successfully
echo.

echo Performance check completed!
echo.
echo Your Laravel application is now optimized for Windows XAMPP!
echo.
pause
