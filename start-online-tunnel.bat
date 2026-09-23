@echo off
title KEVS Online Deployment Tunnel (1 Fixed Domain)
cd /d "%~dp0"
echo ============================================================
echo      KEVS - Online Deployment (Single Fixed Domain)
echo ============================================================
echo.
echo Checking active local servers...
set "TARGET_PORT=8080"
netstat -ano | findstr ":8080 " | findstr "LISTENING" >nul
if %errorlevel% equ 0 (
    echo [OK] Detected CodeIgniter server on port 8080.
    set "TARGET_PORT=8080"
) else (
    netstat -ano | findstr ":80 " | findstr "LISTENING" >nul
    if %errorlevel% equ 0 (
        echo [OK] Detected XAMPP Apache on port 80.
        set "TARGET_PORT=80"
    ) else (
        echo [ERROR] Neither port 8080 (spark serve) nor port 80 (Apache) is running.
        echo Please start Apache in XAMPP or run 'php spark serve' first.
        pause
        exit /b 1
    )
)

echo.
echo ============================================================
echo   FIXED ONLINE DOMAIN: https://kevs-voting.loca.lt
echo ============================================================
echo.
echo If prompted for a tunnel password on first visit:
echo Visit https://loca.lt/mytunnelpassword to view your IP password.
echo.
echo Starting tunnel on port %TARGET_PORT%...
npx --yes localtunnel --port %TARGET_PORT% --subdomain kevs-voting
pause
