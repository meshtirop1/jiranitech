@echo off
REM Starts the local development server.
REM PHP was installed by winget and is not on the system PATH, so add it here.

set "PHP_DIR=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"

if not exist "%PHP_DIR%\php.exe" (
    echo Could not find php.exe in %PHP_DIR%
    echo Install it with:  winget install PHP.PHP.8.4
    exit /b 1
)

set "PATH=%PHP_DIR%;%PATH%"
cd /d "%~dp0"

php artisan serve --port=8000
