@echo off
REM PDO MySQL Configuration Fixer
REM This script helps manage PHP extensions in XAMPP

setlocal enabledelayedexpansion

echo.
echo ======================================
echo PDO MySQL Configuration Manager
echo ======================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo WARNING: This script needs to be run as Administrator
    echo Right-click cmd.exe and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo 1. Checking current PDO MySQL status...
echo.

REM Check PHP version and extensions
C:\xampp\php\php.exe -r "echo 'PHP Version: ' . PHP_VERSION . PHP_EOL; echo 'PDO MySQL: ' . (extension_loaded('pdo_mysql') ? 'OK' : 'MISSING') . PHP_EOL;"

echo.
echo Choose an option:
echo.
echo 1. Verify PDO MySQL is working (run diagnostic)
echo 2. View php.ini location
echo 3. Open php.ini in default editor
echo 4. Display mysqli extension lines in php.ini
echo 5. Exit
echo.

set /p choice="Enter your choice (1-5): "

if "%choice%"=="1" (
    echo.
    echo Running PDO MySQL diagnostic...
    echo.
    C:\xampp\php\php.exe "C:\xampp\htdocs\plms-pnvc\check_pdo.php"
    echo.
    pause
)

if "%choice%"=="2" (
    echo.
    echo PHP Configuration file location:
    C:\xampp\php\php.exe -r "echo php_ini_loaded_file() . PHP_EOL;"
    echo.
    pause
)

if "%choice%"=="3" (
    echo.
    echo Opening php.ini in default editor...
    start notepad "C:\xampp\php\php.ini"
    echo.
    echo IMPORTANT: Look for duplicate "extension=mysqli" lines
    echo Keep only ONE uncommented, comment out or delete the rest
    echo Then save and restart Apache in XAMPP Control Panel
    echo.
    pause
)

if "%choice%"=="4" (
    echo.
    echo Searching for mysqli extension lines in php.ini...
    echo.
    findstr /n "extension=mysqli\|extension=php_mysqli" "C:\xampp\php\php.ini"
    echo.
    echo If you see more than one "extension=mysqli" line, one should be commented
    echo.
    pause
)

if "%choice%"=="5" (
    exit /b 0
)

goto :EOF
