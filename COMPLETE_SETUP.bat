@echo off
echo ========================================
echo School ERP System - Complete Setup
echo ========================================
echo.

echo [1/6] Installing Composer Dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo ✓ Dependencies installed successfully
echo.

echo [2/6] Generating Application Key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Key generation failed!
    pause
    exit /b 1
)
echo ✓ Application key generated
echo.

echo [3/6] Running Database Migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migration failed! Please check database connection.
    pause
    exit /b 1
)
echo ✓ Database migrated successfully
echo.

echo [4/6] Creating Storage Link...
php artisan storage:link
echo ✓ Storage link created
echo.

echo [5/6] Seeding Database with Sample Data...
php artisan db:seed
if %errorlevel% neq 0 (
    echo WARNING: Seeding failed (optional step)
)
echo ✓ Database seeded
echo.

echo [6/6] Clearing Cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared
echo.

echo ========================================
echo ✓ Setup Complete!
echo ========================================
echo.
echo Your School ERP System is ready to use!
echo.
echo To start the server, run:
echo   php artisan serve
echo.
echo Then visit: http://localhost:8000
echo.
echo Default Login Credentials:
echo   Student: student001 / password
echo   Teacher: teacher001 / password
echo.
pause
