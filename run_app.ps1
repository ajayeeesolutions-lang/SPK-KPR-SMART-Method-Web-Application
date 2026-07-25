# Script Runner Anti-Bentrok Port untuk SPK KPR SMART
$ErrorActionPreference = "Continue"
$projectDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $projectDir

# Cari port yang benar-benar kosong mulai dari 8080 hingga 8999
$port = 8080
while ($port -lt 8999) {
    $tcp = New-Object System.Net.Sockets.TcpClient
    try {
        $tcp.Connect("127.0.0.1", $port)
        $tcp.Close()
        $port++
    } catch {
        # Port kosong ditemukan!
        break
    }
}

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "   SPK KPR METODE SMART - BANK KPR SEJAHTERA INDONESIA" -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host " [INFO] Menggunakan Port Bebas Anti-Bentrok: $port" -ForegroundColor Green
Write-Host " [INFO] Membuka Browser di http://127.0.0.1:$port ..." -ForegroundColor White
Write-Host "==========================================================" -ForegroundColor Cyan

# Buka Browser Otomatis setelah delay 1.5 detik
Start-Job -ScriptBlock {
    param($p)
    Start-Sleep -Seconds 2
    Start-Process "http://127.0.0.1:$p"
} -ArgumentList $port | Out-Null

# Jalankan server Laravel
php artisan serve --port=$port
