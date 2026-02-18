@echo off
echo ========================================
echo   SCHOOL ERP - ADMIN DASHBOARD
echo   MCD Inter College Style
echo ========================================
echo.
echo Starting server...
echo.
start http://127.0.0.1:8000/login
echo.
echo Browser opened at: http://127.0.0.1:8000/login
echo.
echo LOGIN CREDENTIALS:
echo ------------------
echo Username: admin
echo Password: admin123
echo User Type: Admin
echo.
echo Server is running at: http://127.0.0.1:8000
echo.
echo Press Ctrl+C to stop the server
echo ========================================
php artisan serve
