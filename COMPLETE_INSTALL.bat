@echo off
color 0B
cls
echo ╔════════════════════════════════════════════════════════════════╗
echo ║                                                                ║
echo ║        🎓 SCHOOL ERP SYSTEM - COMPLETE INSTALLATION 🎓       ║
echo ║                                                                ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

echo [STEP 1/8] Checking Prerequisites...
echo ════════════════════════════════════════════════════════════════
echo.

REM Check PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP not found! Please install PHP 8.2 or higher.
    pause
    exit /b 1
)
echo ✓ PHP is installed

REM Check Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer not found! Please install Composer.
    pause
    exit /b 1
)
echo ✓ Composer is installed

REM Check PostgreSQL
psql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ⚠️  PostgreSQL command not found (psql)
    echo    Make sure PostgreSQL is installed and running
)
echo.

echo [STEP 2/8] Creating Database...
echo ════════════════════════════════════════════════════════════════
echo.
psql -U postgres -c "DROP DATABASE IF EXISTS school_erp;" 2>nul
psql -U postgres -c "CREATE DATABASE school_erp;"
if %errorlevel% neq 0 (
    echo ⚠️  Could not create database automatically
    echo    Please create database manually:
    echo    CREATE DATABASE school_erp;
    echo.
    pause
) else (
    echo ✓ Database 'school_erp' created successfully
)
echo.

echo [STEP 3/8] Installing Composer Dependencies...
echo ════════════════════════════════════════════════════════════════
echo.
call composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo ❌ Composer install failed!
    pause
    exit /b 1
)
echo ✓ Dependencies installed
echo.

echo [STEP 4/8] Generating Application Key...
echo ════════════════════════════════════════════════════════════════
echo.
php artisan key:generate --force
if %errorlevel% neq 0 (
    echo ❌ Key generation failed!
    pause
    exit /b 1
)
echo ✓ Application key generated
echo.

echo [STEP 5/8] Running Database Migrations...
echo ════════════════════════════════════════════════════════════════
echo.
php artisan migrate:fresh --force
if %errorlevel% neq 0 (
    echo ❌ Migration failed!
    echo    Please check:
    echo    1. Database 'school_erp' exists
    echo    2. .env file has correct credentials
    echo    3. PostgreSQL is running
    pause
    exit /b 1
)
echo ✓ Database migrated successfully
echo.

echo [STEP 6/8] Seeding Database with Sample Data...
echo ════════════════════════════════════════════════════════════════
echo.
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ⚠️  Seeding failed (optional step)
) else (
    echo ✓ Database seeded successfully
)
echo.

echo [STEP 7/8] Creating Storage Link...
echo ════════════════════════════════════════════════════════════════
echo.
php artisan storage:link
echo ✓ Storage link created
echo.

echo [STEP 8/8] Clearing Cache...
echo ════════════════════════════════════════════════════════════════
echo.
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared
echo.

echo ╔════════════════════════════════════════════════════════════════╗
echo ║                                                                ║
echo ║                    ✅ INSTALLATION COMPLETE! ✅               ║
echo ║                                                                ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo 🎉 Your School ERP System is ready to use!
echo.
echo ════════════════════════════════════════════════════════════════
echo                      🔐 LOGIN CREDENTIALS
echo ════════════════════════════════════════════════════════════════
echo.
echo 👨‍💼 ADMIN LOGIN:
echo    Username: admin
echo    Password: admin123
echo    User Type: Admin
echo.
echo 👨‍🎓 STUDENT LOGIN:
echo    Username: student001 to student010
echo    Password: password
echo    User Type: Student
echo.
echo 👨‍🏫 TEACHER LOGIN:
echo    Username: teacher001 to teacher005
echo    Password: password
echo    User Type: Teacher
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 📊 SAMPLE DATA CREATED:
echo    ✓ 1 Admin User
echo    ✓ 10 Students
echo    ✓ 5 Teachers
echo    ✓ 12 Classes (1-12)
echo    ✓ 36 Sections (A, B, C)
echo    ✓ 10 Subjects
echo    ✓ 2 Sessions
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 🚀 TO START THE SERVER:
echo    Run: php artisan serve
echo    OR
echo    Double-click: START_SERVER.bat
echo.
echo 🌐 THEN VISIT:
echo    http://localhost:8000
echo.
echo ════════════════════════════════════════════════════════════════
echo.
pause
