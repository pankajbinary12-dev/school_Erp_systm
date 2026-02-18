@echo off
echo ========================================
echo School ERP - Quick Setup
echo ========================================
echo.

echo Step 1: Installing dependencies...
call composer install --no-interaction --quiet
if %errorlevel% neq 0 (
    echo Failed! Check composer.
    pause
    exit /b 1
)
echo Done!
echo.

echo Step 2: Generating key...
php artisan key:generate --force
echo Done!
echo.

echo Step 3: Running migrations...
php artisan migrate:fresh --force --seed
if %errorlevel% neq 0 (
    echo Failed! Check database.
    pause
    exit /b 1
)
echo Done!
echo.

echo Step 4: Clearing cache...
php artisan cache:clear
php artisan config:clear
echo Done!
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Credentials:
echo Admin: admin / admin123
echo Student: student001 / password
echo Teacher: teacher001 / password
echo.
echo Run: php artisan serve
echo Visit: http://localhost:8000
echo.
pause
