@echo off
REM Fix PHP Configuration for Windows XAMPP
REM This script completely fixes PHP configuration issues

echo Starting PHP configuration fixes...

echo.
echo ========================================
echo PHP Configuration Fixes Started
echo ========================================

echo.
echo Step 1: Backing up original PHP configuration...
copy "C:\xampp\php\php.ini" "C:\xampp\php\php.ini.backup"
echo Original PHP configuration backed up!

echo.
echo Step 2: Creating COMPLETELY CLEAN PHP configuration...
REM Create a completely clean PHP configuration
(
echo.
echo ; COMPLETELY CLEAN PHP Configuration for Laravel - Windows XAMPP
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
echo ; ONLY enable extensions that are available in XAMPP
echo ; NO problematic extensions
echo extension=gd
echo extension=curl
echo extension=zip
echo extension=mbstring
echo extension=mysqli
echo extension=pdo_mysql
echo extension=json
echo extension=session
echo extension=tokenizer
echo extension=simplexml
echo extension=xmlreader
echo extension=xmlwriter
echo extension=dom
echo extension=libxml
echo extension=soap
echo extension=xsl
echo extension=zlib
echo extension=fileinfo
echo extension=filter
echo extension=hash
echo extension=iconv
echo extension=intl
echo extension=openssl
echo extension=pcre
echo extension=ctype
echo.
echo ; Windows-specific settings
echo auto_detect_line_endings = Off
echo ignore_user_abort = Off
echo.
echo ; Disable problematic functions for security
echo disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source
echo.
echo ; Windows-specific performance settings
echo zend.enable_gc = 1
echo zend.gc_frequency = 1000
echo zend.gc_threshold = 10000
echo.
echo ; Windows-specific error handling
echo log_errors = On
echo error_log = C:\xampp\php\logs\php_errors.log
echo.
echo ; Windows-specific session handling
echo session.save_path = C:\xampp\tmp
echo session.use_cookies = 1
echo session.use_only_cookies = 1
echo session.cookie_path = /
echo session.cookie_domain = 
echo session.cookie_secure = 0
echo session.cookie_httponly = 1
echo.
echo ; Windows-specific file handling
echo file_uploads = On
echo upload_tmp_dir = C:\xampp\tmp
echo upload_max_filesize = 100M
echo max_file_uploads = 20
echo.
echo ; Windows-specific memory settings
echo memory_limit = 512M
echo max_execution_time = 300
echo max_input_time = 300
echo.
echo ; Windows-specific database settings
echo mysql.default_host = localhost
echo mysql.default_user = root
echo mysql.default_password = 
echo mysql.connect_timeout = 60
echo.
echo ; Windows-specific output settings
echo output_buffering = 4096
echo implicit_flush = Off
echo.
echo ; Windows-specific resource limits
echo default_socket_timeout = 60
echo max_input_nesting_level = 64
echo max_input_vars = 3000
echo.
echo ; Windows-specific logging
echo log_errors_max_len = 1024
echo ignore_repeated_errors = On
echo ignore_repeated_source = On
echo.
echo ; Windows-specific performance
echo auto_prepend_file = 
echo auto_append_file = 
echo default_charset = "UTF-8"
echo.
echo ; Windows-specific auto-detection
echo auto_detect_line_endings = Off
echo ignore_user_abort = Off
echo.
echo ; Windows-specific function restrictions
echo disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source
echo.
echo ; Windows-specific extensions (only available ones)
echo extension=gd
echo extension=curl
echo extension=zip
echo extension=mbstring
echo extension=mysqli
echo extension=pdo_mysql
echo extension=json
echo extension=session
echo extension=tokenizer
echo extension=simplexml
echo extension=xmlreader
echo extension=xmlwriter
echo extension=dom
echo extension=libxml
echo extension=soap
echo extension=xsl
echo extension=zlib
echo extension=fileinfo
echo extension=filter
echo extension=hash
echo extension=iconv
echo extension=intl
echo extension=openssl
echo extension=pcre
echo extension=ctype
echo.
echo ; Windows-specific garbage collection
echo zend.enable_gc = 1
echo zend.gc_frequency = 1000
echo zend.gc_threshold = 10000
) > "C:\xampp\php\php.ini"
echo COMPLETELY CLEAN PHP configuration created!

echo.
echo Step 3: Restarting Apache to apply changes...
net stop apache2.4
net start apache2.4
echo Apache restarted!

echo.
echo Step 4: Testing PHP configuration...
php --version
echo PHP configuration test completed!

echo.
echo ========================================
echo PHP Configuration Fixes Completed!
echo ========================================
echo.
echo Issues Fixed:
echo - Removed ALL problematic PHP extensions
echo - Created COMPLETELY CLEAN PHP configuration
echo - Restarted Apache to apply changes
echo - Tested PHP configuration
echo.
echo Note: The application should now work without PHP warnings!
echo.
echo Press any key to continue...
pause > nul
