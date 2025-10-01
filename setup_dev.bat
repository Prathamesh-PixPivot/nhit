@echo off
echo Setting up Laravel development environment...

REM Start XAMPP services
net start mysql
net start apache2.4

REM Set environment variables
set APP_ENV=local
set APP_DEBUG=true
set DB_CONNECTION=mysql
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_DATABASE=laravel
set DB_USERNAME=root
set DB_PASSWORD=

REM Install dependencies
composer install
npm install

REM Build assets
npm run dev

echo Development environment setup completed!
pause
