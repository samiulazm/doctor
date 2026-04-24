# Composer wrapper: uses Laragon (C:\laragon) when present, else PHP + composer.phar.
# Usage:  .\composer.ps1 install
# Override Laragon folder:  $env:LARAGON_ROOT = "D:\laragon"

$ErrorActionPreference = "Stop"
$root = $PSScriptRoot
Set-Location $root

. (Join-Path $root "scripts\laragon-path.ps1")

if ($env:LARAGON_PHP_BELOW_81 -eq "1") {
    Write-Host @"

Laragon only has PHP below 8.1. This project needs PHP 8.1+.

In Laragon: Menu > PHP > pick 8.1 or newer (e.g. 8.3), then run again.

"@ -ForegroundColor Yellow
    exit 1
}

if (Get-Command composer -ErrorAction SilentlyContinue) {
    & composer @args
    exit $LASTEXITCODE
}

$phpCmd = Get-Command php -ErrorAction SilentlyContinue
if (-not $phpCmd) {
    Write-Host @"

PHP was not found. Laragon PHP expected under:
  C:\laragon\bin\php\...

Set `$env:LARAGON_ROOT` if Laragon is elsewhere, or use:
  .\run-tests.ps1   (Docker)

"@ -ForegroundColor Yellow
    exit 1
}

$phar = Join-Path $root "composer.phar"
if (-not (Test-Path $phar)) {
    Write-Host "Downloading composer.phar to Multi-Hospital..." -ForegroundColor Cyan
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile $phar -UseBasicParsing
}

& php $phar @args
exit $LASTEXITCODE
