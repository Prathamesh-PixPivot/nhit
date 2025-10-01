@echo off
REM Windows XAMPP Optimization Script for Laravel Application
REM This script optimizes XAMPP for better Laravel performance

echo Starting Windows XAMPP optimization for Laravel...

REM Set variables
set XAMPP_PATH=C:\xampp
set PHP_PATH=%XAMPP_PATH%\php
set APACHE_PATH=%XAMPP_PATH%\apache
set MYSQL_PATH=%XAMPP_PATH%\mysql
set PROJECT_PATH=%CD%

echo Optimizing PHP configuration...

REM Backup original php.ini
copy "%PHP_PATH%\php.ini" "%PHP_PATH%\php.ini.backup"

REM Create optimized PHP configuration
echo Creating optimized PHP configuration...
(
echo.
echo ; Optimized PHP Configuration for Laravel
echo ; Memory and Performance Settings
echo memory_limit = 512M
echo max_execution_time = 300
echo max_input_time = 300
echo max_input_vars = 3000
echo post_max_size = 100M
echo upload_max_filesize = 100M
echo max_file_uploads = 20
echo.
echo ; OPcache Settings
echo opcache.enable=1
echo opcache.enable_cli=1
echo opcache.memory_consumption=256
echo opcache.interned_strings_buffer=16
echo opcache.max_accelerated_files=10000
echo opcache.revalidate_freq=2
echo opcache.validate_timestamps=0
echo opcache.save_comments=1
echo opcache.fast_shutdown=1
echo.
echo ; Session Settings
echo session.gc_maxlifetime=7200
echo session.cookie_lifetime=0
echo session.cookie_httponly=1
echo session.use_strict_mode=1
echo.
echo ; Error Reporting
echo display_errors=Off
echo display_startup_errors=Off
echo log_errors=On
echo error_reporting=E_ALL ^& ~E_DEPRECATED ^& ~E_STRICT
echo.
echo ; Date/Time
echo date.timezone=UTC
echo.
echo ; Security
echo expose_php=Off
echo allow_url_fopen=Off
echo allow_url_include=Off
echo.
echo ; Extensions
echo extension=redis
echo extension=memcached
echo extension=gd
echo extension=curl
echo extension=zip
echo extension=mbstring
echo extension=xml
echo extension=bcmath
echo extension=intl
echo extension=imagick
) >> "%PHP_PATH%\php.ini"

echo PHP configuration optimized!

echo Optimizing Apache configuration...

REM Backup original httpd.conf
copy "%APACHE_PATH%\conf\httpd.conf" "%APACHE_PATH%\conf\httpd.conf.backup"

REM Create optimized Apache configuration
echo Creating optimized Apache configuration...
(
echo.
echo # Optimized Apache Configuration for Laravel
echo # Enable mod_rewrite
echo LoadModule rewrite_module modules/mod_rewrite.so
echo.
echo # Enable mod_deflate
echo LoadModule deflate_module modules/mod_deflate.so
echo.
echo # Enable mod_expires
echo LoadModule expires_module modules/mod_expires.so
echo.
echo # Enable mod_headers
echo LoadModule headers_module modules/mod_headers.so
echo.
echo # Gzip Compression
echo ^<Location /^>
echo     SetOutputFilter DEFLATE
echo     SetEnvIfNoCase Request_URI \.^(?:gif^|jpe?g^|png^)$ no-gzip dont-vary
echo     SetEnvIfNoCase Request_URI \.^(?:exe^|t?gz^|zip^|bz2^|sit^|rar^)$ no-gzip dont-vary
echo ^</Location^>
echo.
echo # Browser Caching
echo ^<IfModule mod_expires.c^>
echo     ExpiresActive On
echo     ExpiresByType text/css "access plus 1 year"
echo     ExpiresByType application/javascript "access plus 1 year"
echo     ExpiresByType image/png "access plus 1 year"
echo     ExpiresByType image/jpg "access plus 1 year"
echo     ExpiresByType image/jpeg "access plus 1 year"
echo     ExpiresByType image/gif "access plus 1 year"
echo     ExpiresByType image/ico "access plus 1 year"
echo     ExpiresByType text/plain "access plus 1 month"
echo     ExpiresByType application/pdf "access plus 1 month"
echo     ExpiresByType text/html "access plus 1 hour"
echo ^</IfModule^>
echo.
echo # Cache Control Headers
echo ^<IfModule mod_headers.c^>
echo     ^<FilesMatch "\.^(css^|js^|png^|jpg^|jpeg^|gif^|ico^|svg^|woff^|woff2^|ttf^|eot^)$"^>
echo         Header set Cache-Control "max-age=31536000, public, immutable"
echo     ^</FilesMatch^>
echo     ^<FilesMatch "\.^(html^|htm^|php^)$"^>
echo         Header set Cache-Control "max-age=3600, public"
echo     ^</FilesMatch^>
echo ^</IfModule^>
echo.
echo # Security Headers
echo Header always set X-Frame-Options "SAMEORIGIN"
echo Header always set X-XSS-Protection "1; mode=block"
echo Header always set X-Content-Type-Options "nosniff"
echo Header always set Referrer-Policy "no-referrer-when-downgrade"
echo.
echo # Deny access to sensitive files
echo ^<FilesMatch "^\."^>
echo     Order allow,deny
echo     Deny from all
echo ^</FilesMatch^>
echo.
echo ^<FilesMatch "\.^(env^|git^|svn^|hg^|bzr^)$"^>
echo     Order allow,deny
echo     Deny from all
echo ^</FilesMatch^>
echo.
echo # Performance settings
echo KeepAlive On
echo KeepAliveTimeout 5
echo MaxKeepAliveRequests 100
echo Timeout 300
echo.
echo # Disable server signature
echo ServerTokens Prod
echo ServerSignature Off
) >> "%APACHE_PATH%\conf\httpd.conf"

echo Apache configuration optimized!

echo Optimizing MySQL configuration...

REM Backup original my.ini
copy "%MYSQL_PATH%\bin\my.ini" "%MYSQL_PATH%\bin\my.ini.backup"

REM Create optimized MySQL configuration
echo Creating optimized MySQL configuration...
(
echo.
echo # Optimized MySQL Configuration for Laravel
echo [mysqld]
echo # Performance Settings
echo innodb_buffer_pool_size = 1G
echo innodb_log_file_size = 256M
echo innodb_flush_log_at_trx_commit = 2
echo innodb_flush_method = O_DIRECT
echo innodb_file_per_table = 1
echo innodb_open_files = 400
echo innodb_io_capacity = 400
echo innodb_read_io_threads = 4
echo innodb_write_io_threads = 4
echo.
echo # Query Cache
echo query_cache_type = 1
echo query_cache_size = 64M
echo query_cache_limit = 2M
echo.
echo # Connection Settings
echo max_connections = 200
echo max_connect_errors = 1000
echo connect_timeout = 60
echo wait_timeout = 28800
echo interactive_timeout = 28800
echo.
echo # Memory Settings
echo key_buffer_size = 256M
echo sort_buffer_size = 2M
echo read_buffer_size = 2M
echo read_rnd_buffer_size = 8M
echo myisam_sort_buffer_size = 64M
echo thread_cache_size = 8
echo.
echo # Logging
echo slow_query_log = 1
echo slow_query_log_file = "%MYSQL_PATH%\data\slow.log"
echo long_query_time = 2
echo.
echo # Character Set
echo character-set-server = utf8mb4
echo collation-server = utf8mb4_unicode_ci
echo.
echo [mysql]
echo default-character-set = utf8mb4
echo.
echo [client]
echo default-character-set = utf8mb4
) >> "%MYSQL_PATH%\bin\my.ini"

echo MySQL configuration optimized!

echo Creating Laravel optimization script...

REM Create Laravel optimization script
echo Creating Laravel optimization script...
(
echo @echo off
echo echo Optimizing Laravel application...
echo.
echo REM Clear all caches
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo php artisan event:clear
echo.
echo REM Rebuild caches
echo php artisan config:cache
echo php artisan route:cache
echo php artisan view:cache
echo php artisan event:cache
echo.
echo REM Optimize autoloader
echo composer dump-autoload --optimize
echo.
echo REM Run database migrations
echo php artisan migrate --force
echo.
echo echo Laravel optimization completed!
echo pause
) > optimize_laravel.bat

echo Laravel optimization script created!

echo Creating development environment setup...

REM Create development environment setup
echo Creating development environment setup...
(
echo @echo off
echo echo Setting up Laravel development environment...
echo.
echo REM Start XAMPP services
echo net start mysql
echo net start apache2.4
echo.
echo REM Set environment variables
echo set APP_ENV=local
echo set APP_DEBUG=true
echo set DB_CONNECTION=mysql
echo set DB_HOST=127.0.0.1
echo set DB_PORT=3306
echo set DB_DATABASE=laravel
echo set DB_USERNAME=root
echo set DB_PASSWORD=
echo.
echo REM Install dependencies
echo composer install
echo npm install
echo.
echo REM Build assets
echo npm run dev
echo.
echo echo Development environment setup completed!
echo pause
) > setup_dev.bat

echo Development environment setup created!

echo Creating production optimization script...

REM Create production optimization script
echo Creating production optimization script...
(
echo @echo off
echo echo Optimizing Laravel for production...
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
echo echo Production optimization completed!
echo pause
) > optimize_production.bat

echo Production optimization script created!

echo Creating monitoring script...

REM Create monitoring script
echo Creating monitoring script...
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
echo echo Monitoring completed!
echo pause
) > monitor_performance.bat

echo Monitoring script created!

echo.
echo ========================================
echo Windows XAMPP Optimization Complete!
echo ========================================
echo.
echo Created files:
echo - optimize_laravel.bat (Laravel optimization)
echo - setup_dev.bat (Development setup)
echo - optimize_production.bat (Production optimization)
echo - monitor_performance.bat (Performance monitoring)
echo.
echo Next steps:
echo 1. Restart XAMPP services
echo 2. Run optimize_laravel.bat
echo 3. Test your application
echo.
echo Press any key to continue...
pause > nul
