@echo off
title SPK KPR SMART - Bank Sejahtera
cd /d "%~dp0"
echo ==========================================================
echo   MEMULAI APLIKASI SPK KPR SMART (PORT ANTI-BENTROK)
echo ==========================================================
echo.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0run_app.ps1"
pause
