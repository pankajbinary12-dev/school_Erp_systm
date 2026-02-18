@echo off
echo ========================================
echo School ERP System - Setup Script
echo ========================================
echo.

echo [1/7] Installing Composer Dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo ✓ Composer dependencies installed
echo.

echo [2/7] Generating Application Key...
call php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Key generation failed!
    pause
    exit /b 1
)
echo ✓ Application key generated
echo.

echo [3/7] Running Database Migrations...
call php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed! Please check your database configuration.
    pause
    exit /b 1
)
echo ✓ Database migrated successfully
echo.

echo [4/7] Seeding Database with Sample Data...
call php artisan db:seed
if %errorlevel% neq 0 (
    echo WARNING: Seeding failed! You can seed manually later.
)
echo ✓ Database seeded
echo.

echo [5/7] Creating Storage Link...
call php artisan storage:link
if %errorlevel% neq 0 (
    echo WARNING: Storage link creation failed!
)
echo ✓ Storage link created
echo.

echo [6/7] Clearing Cache...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
echo ✓ Cache cleared
echo.

echo ========================================
echo ✓ Setup Complete!
echo ========================================
echo.
echo Your School ERP System is ready to use!
echo.
echo Default Login Credentials:
echo --------------------------
echo Student Login:
echo   Username: student001
echo   Password: password
echo   User Type: Student
echo.
echo Teacher Login:
echo   Username: teacher001
echo   Password: password
echo   User Type: Teacher
echo.
echo To start the development server, run:
echo   php artisan serve
echo.
echo Then visit: http://localhost:8000
echo.
pause
