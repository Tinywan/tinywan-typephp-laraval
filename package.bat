@echo off
setlocal

rem 1. Run build
echo [INFO] Step 1: Running build.bat ...
call "%~dp0build.bat"
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Build failed with error code %ERRORLEVEL%. Packaging aborted.
    exit /b %ERRORLEVEL%
)

rem 2. Sync to dist directory
echo [INFO] Step 2: Packaging artifacts and runtime resources into dist ...

set PHP_HOME=D:\workspace\tpc_v0.6.5_windows_x86_64

if not exist "%~dp0dist" mkdir "%~dp0dist"

rem Copy executable
if exist "%~dp0build\laravel-artisan.exe" (
    copy /y "%~dp0build\laravel-artisan.exe" "%~dp0dist\laravel-artisan.exe" >nul
) else if exist "%~dp0build\laravel_artisan.exe" (
    copy /y "%~dp0build\laravel_artisan.exe" "%~dp0dist\laravel-artisan.exe" >nul
)

rem Copy DLL and extensions
if exist "%PHP_HOME%\*.dll" copy /y "%PHP_HOME%\*.dll" "%~dp0dist\" >nul
if exist "%PHP_HOME%\ext" xcopy /y /e /i /q "%PHP_HOME%\ext" "%~dp0dist\ext" >nul

rem Copy essential project structure
if exist "%~dp0app" xcopy /y /e /i /q "%~dp0app" "%~dp0dist\app" >nul
if exist "%~dp0bootstrap" xcopy /y /e /i /q "%~dp0bootstrap" "%~dp0dist\bootstrap" >nul
if exist "%~dp0config" xcopy /y /e /i /q "%~dp0config" "%~dp0dist\config" >nul
if exist "%~dp0database" xcopy /y /e /i /q "%~dp0database" "%~dp0dist\database" >nul
if exist "%~dp0public" xcopy /y /e /i /q "%~dp0public" "%~dp0dist\public" >nul
if exist "%~dp0resources" xcopy /y /e /i /q "%~dp0resources" "%~dp0dist\resources" >nul
if exist "%~dp0routes" xcopy /y /e /i /q "%~dp0routes" "%~dp0dist\routes" >nul
if exist "%~dp0storage" xcopy /y /e /i /q "%~dp0storage" "%~dp0dist\storage" >nul
if exist "%~dp0vendor" xcopy /y /e /i /q "%~dp0vendor" "%~dp0dist\vendor" >nul
if exist "%~dp0.env" copy /y "%~dp0.env" "%~dp0dist\.env" >nul
if exist "%~dp0composer.json" copy /y "%~dp0composer.json" "%~dp0dist\composer.json" >nul
if exist "%~dp0composer.lock" copy /y "%~dp0composer.lock" "%~dp0dist\composer.lock" >nul
if exist "%~dp0run.bat" copy /y "%~dp0run.bat" "%~dp0dist\run.bat" >nul
if exist "%~dp0php.ini" copy /y "%~dp0php.ini" "%~dp0dist\php.ini" >nul

echo [INFO] Packaging completed successfully! Dist path: %~dp0dist
