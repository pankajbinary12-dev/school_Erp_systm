@echo off
color 0B
echo ========================================
echo School ERP System - Starting Server
echo ========================================
echo.

echo Checking if setup is complete...
if not exist "vendor\autoload.php" (
    echo ❌ Dependencies not installed!
    echo Please run SETUP.bat first.
    pause
    exit /b 1
)

findstr "APP_KEY=base64:" .env >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Application key not set!
    echo Please run SETUP.bat first.
    pause
    exit /b 1
)

echo ✓ System is ready
echo.
echo Starting Laravel Development Server...
echo.
echo ========================================
echo Server Information:
echo ========================================
echo URL: http://localhost:8000
echo.
echo Login Credentials:
echo ------------------
echo Student: student001 / password
echo Teacher: teacher001 / password
echo Admin: admin / admin123
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve

pause
