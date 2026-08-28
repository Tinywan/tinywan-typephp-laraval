@echo off
setlocal
cd /d "%~dp0"
set "PHPRC=%~dp0"
set "PATH=%~dp0;%PATH%"

"%~dp0laravel-artisan.exe" %*
