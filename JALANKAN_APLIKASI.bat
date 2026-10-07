@echo off
setlocal EnableDelayedExpansion
title SPK KPR SMART - Setup Otomatis
color 0A
cd /d "%~dp0"

echo.
echo  ╔══════════════════════════════════════════════════════════╗
echo  ║       SPK KPR SMART - SISTEM KELAYAKAN KPR              ║
echo  ║       Setup Otomatis ^& Jalankan Aplikasi                ║
echo  ╚══════════════════════════════════════════════════════════╝
echo.

:: ─────────────────────────────────────────────────────────────
:: [1] CEK PHP
:: ─────────────────────────────────────────────────────────────
echo  [1/6] Mengecek PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  [!] PHP TIDAK DITEMUKAN di komputer ini!
    echo  [!] Aplikasi ini membutuhkan PHP ^& Laragon untuk berjalan.
    echo.
    echo  Mengunduh Laragon secara otomatis...
    echo  Laragon adalah alat gratis yang dibutuhkan untuk menjalankan
    echo  aplikasi ini ^(PHP + MySQL + Apache sekaligus^).
    echo.
    powershell -Command "Start-Process 'https://github.com/leokhoa/laragon/releases/download/6.0.0/laragon-wamp.exe' -Verb RunAs" >nul 2>&1
    if %errorlevel% neq 0 (
        :: Jika powershell gagal, buka browser ke halaman download
        start "" "https://laragon.org/download/"
    )
    echo.
    echo  ┌──────────────────────────────────────────────────────────┐
    echo  │  LANGKAH INSTALASI:                                      │
    echo  │  1. Install Laragon yang sudah didownload                │
    echo  │  2. Buka Laragon, klik tombol "Start All"                │
    echo  │  3. Jalankan kembali file JALANKAN_APLIKASI.bat ini      │
    echo  └──────────────────────────────────────────────────────────┘
    echo.
    pause
    exit /b 1
)
for /f "tokens=2 delims= " %%a in ('php -v 2^>nul ^| findstr /i "^PHP"') do set PHP_VER=%%a
echo  [OK] PHP ditemukan: versi !PHP_VER!

:: ─────────────────────────────────────────────────────────────
:: [2] CEK COMPOSER
:: ─────────────────────────────────────────────────────────────
echo.
echo  [2/6] Mengecek Composer...
composer -V >nul 2>&1
if %errorlevel% neq 0 (
    echo  [!] Composer belum ada. Mengunduh Composer...
    echo.
    :: Download Composer-Setup.exe
    powershell -NoProfile -ExecutionPolicy Bypass -Command ^
        "Invoke-WebRequest -Uri 'https://getcomposer.org/Composer-Setup.exe' -OutFile '%TEMP%\Composer-Setup.exe'; Start-Process '%TEMP%\Composer-Setup.exe' -Wait"

    :: Cek lagi setelah install
    composer -V >nul 2>&1
    if !errorlevel! neq 0 (
        color 0E
        echo  [!] Composer masih tidak ditemukan setelah instalasi.
        echo  [!] Buka halaman download secara manual:
        start "" "https://getcomposer.org/download/"
        echo.
        echo  Setelah install Composer, jalankan kembali file ini.
        pause
        exit /b 1
    )
)
echo  [OK] Composer ditemukan.

:: ─────────────────────────────────────────────────────────────
:: [3] CEK VENDOR (DEPENDENCIES LARAVEL)
:: ─────────────────────────────────────────────────────────────
echo.
echo  [3/6] Mengecek dependencies Laravel (folder vendor)...
if not exist "vendor\autoload.php" (
    echo  [!] Folder vendor belum ada. Menjalankan composer install...
    echo  [!] Proses ini bisa memakan waktu 1-5 menit, harap tunggu...
    echo.
    composer install --no-interaction --prefer-dist 2>&1
    if !errorlevel! neq 0 (
        color 0C
        echo.
        echo  [ERROR] composer install gagal! Pastikan koneksi internet aktif.
        pause
        exit /b 1
    )
    echo  [OK] Dependencies berhasil diinstall!
) else (
    echo  [OK] Folder vendor sudah ada. Melewati composer install.
)

:: ─────────────────────────────────────────────────────────────
:: [4] CEK FILE .ENV
:: ─────────────────────────────────────────────────────────────
echo.
echo  [4/6] Mengecek konfigurasi .env...
if not exist ".env" (
    echo  [!] File .env belum ada. Membuat dari .env.example...
    copy ".env.example" ".env" >nul
    php artisan key:generate --no-interaction >nul 2>&1
    echo  [OK] File .env berhasil dibuat dan APP_KEY di-generate.
) else (
    echo  [OK] File .env sudah ada.
)

:: ─────────────────────────────────────────────────────────────
:: [5] MIGRASI DATABASE
:: ─────────────────────────────────────────────────────────────
echo.
echo  [5/6] Mengecek dan membuat database MySQL...
php -r "try { $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); $pdo->exec('CREATE DATABASE IF NOT EXISTS `spk_kpr_smart`;'); } catch(PDOException $e) {}" >nul 2>&1
php artisan migrate --force --no-interaction >nul 2>&1
php artisan db:seed --force --no-interaction >nul 2>&1
if %errorlevel% neq 0 (
    echo  [!] Seeding atau Migrasi ada yang terlewat, namun diusahakan tetap berjalan.
) else (
    echo  [OK] Database siap dan berisi pengaturan default.
)

:: Storage link
php artisan storage:link >nul 2>&1

:: Clear cache
php artisan config:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan cache:clear >nul 2>&1

:: ─────────────────────────────────────────────────────────────
:: [6] JALANKAN SERVER
:: ─────────────────────────────────────────────────────────────
echo.
echo  [6/6] Menjalankan server aplikasi...
echo.

:: Cari port yang tersedia mulai dari 8000
set PORT=8000
:FIND_PORT
netstat -ano 2>nul | findstr ":%PORT% " >nul
if %errorlevel% equ 0 (
    set /a PORT+=1
    if !PORT! gtr 8020 (
        set PORT=8080
    )
    goto FIND_PORT
)

echo  ╔══════════════════════════════════════════════════════════╗
echo  ║  APLIKASI SIAP DIGUNAKAN!                               ║
echo  ║                                                         ║
echo  ║  Buka browser dan akses:                                ║
echo  ║  👉  http://127.0.0.1:%PORT%                               ║
echo  ║                                                         ║
echo  ║  Login Default:                                         ║
echo  ║  Admin    : admin@kpr.com    / password                 ║
echo  ║  Pimpinan : owner@kpr.com    / password                 ║
echo  ║  Marketing: marketing@kpr.com/ password                 ║
echo  ║                                                         ║
echo  ║  [Tutup jendela ini untuk menghentikan server]          ║
echo  ╚══════════════════════════════════════════════════════════╝
echo.

:: Buka browser otomatis setelah 2 detik
start /b cmd /c "timeout /t 2 >nul && start http://127.0.0.1:%PORT%"

:: Jalankan server
php artisan serve --port=%PORT%

pause
