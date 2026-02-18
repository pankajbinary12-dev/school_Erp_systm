@echo off
color 0B
cls
echo ╔════════════════════════════════════════════════════════════════╗
echo ║                                                                ║
echo ║           🎓 SCHOOL ERP SYSTEM - QUICK RUN 🎓                 ║
echo ║                                                                ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

echo Checking system status...
echo.

REM Check if vendor exists
if not exist "vendor\autoload.php" (
    echo ❌ Dependencies not installed!
    echo.
    echo Running setup first...
    echo.
    call SETUP.bat
    if %errorlevel% neq 0 (
        echo.
        echo ❌ Setup failed! Please check errors above.
        pause
        exit /b 1
    )
)

REM Check if APP_KEY is set
findstr "APP_KEY=base64:" .env >nul 2>&1
if %errorlevel% neq 0 (
    echo ⚠️  Application key not set!
    echo Generating key...
    php artisan key:generate
)

echo.
echo ✅ System is ready!
echo.
echo ════════════════════════════════════════════════════════════════
echo                    🔐 LOGIN CREDENTIALS
echo ════════════════════════════════════════════════════════════════
echo.
echo 👨‍🎓 STUDENT LOGINS:
echo    Username: student001 to student010
echo    Password: password
echo    User Type: Student
echo.
echo 👨‍🏫 TEACHER LOGINS:
echo    Username: teacher001 to teacher005
echo    Password: password
echo    User Type: Teacher
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 📊 SAMPLE DATA:
echo    ✓ 10 Students
echo    ✓ 5 Teachers
echo    ✓ 12 Classes (1-12)
echo    ✓ 36 Sections (A, B, C)
echo    ✓ 10 Subjects
echo    ✓ 2 Sessions
echo.
echo ════════════════════════════════════════════════════════════════
echo.
echo 🚀 Starting Laravel Development Server...
echo.
echo Server will start at: http://localhost:8000
echo.
echo Press Ctrl+C to stop the server
echo.
echo ════════════════════════════════════════════════════════════════
echo.

php artisan serve

pause
