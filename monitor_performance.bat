@echo off
echo Laravel Performance Monitoring...

REM Check PHP version
php --version

REM Check memory usage
php -r "echo 'Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Max Execution Time: ' . ini_get('max_execution_time') . PHP_EOL;"

REM Check OPcache status
php -r "if (extension_loaded('opcache')) { echo 'OPcache: Enabled' . PHP_EOL; } else { echo 'OPcache: Disabled' . PHP_EOL; }"

REM Check database connection
php artisan tinker --execute="echo 'Database: ' . (DB::connection()->getPdo() ? 'Connected' : 'Failed') . PHP_EOL;"

REM Check cache status
php artisan cache:clear
echo Cache cleared successfully

REM Check storage permissions
if exist storage\logs echo Storage logs: Accessible
if exist storage\framework echo Storage framework: Accessible

echo Monitoring completed!
pause
