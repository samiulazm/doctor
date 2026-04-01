# Run from: Multi-Hospital directory
#   .\run-tests.ps1
# Uses Laragon (C:\laragon) first when installed, then Docker, then PHP+composer.phar.
# PowerShell 5.x: use semicolons, not &&

$ErrorActionPreference = "Stop"
$root = $PSScriptRoot
Set-Location $root

function Invoke-ComposerTestDocker {
    param([string] $ProjectRoot)
    docker run --rm `
        -v "${ProjectRoot}:/app" `
        -w /app `
        php:8.1-cli `
        bash /app/scripts/docker-test.sh
}

. (Join-Path $root "scripts\laragon-path.ps1")

if ($env:LARAGON_PHP_BELOW_81 -eq "1" -and (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Laragon PHP is older than 8.1. Using Docker for tests..." -ForegroundColor Yellow
    Invoke-ComposerTestDocker -ProjectRoot $root
    exit $LASTEXITCODE
}

if ($env:LARAGON_PHP_BELOW_81 -eq "1") {
    Write-Host @"

Laragon only has PHP below 8.1. Add PHP 8.1+ in Laragon (Menu > PHP > Version), or install Docker and run:
  .\run-tests.ps1

"@ -ForegroundColor Yellow
    exit 1
}

if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host "Using Composer on PATH (Laragon or global)..." -ForegroundColor Cyan
    composer install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    composer test
    exit $LASTEXITCODE
}

$php = Get-Command php -ErrorAction SilentlyContinue
if ($php) {
    $phar = Join-Path $root "composer.phar"
    if (-not (Test-Path $phar)) {
        Write-Host "Downloading composer.phar to Multi-Hospital..." -ForegroundColor Cyan
        [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
        Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile $phar -UseBasicParsing
    }
    Write-Host "Using php + composer.phar..." -ForegroundColor Cyan
    & php $phar install --no-interaction --prefer-dist
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    & php $phar test
    exit $LASTEXITCODE
}

if (Get-Command docker -ErrorAction SilentlyContinue) {
    Write-Host "Using Docker (php:8.1-cli; first run may take a few minutes)..." -ForegroundColor Cyan
    Invoke-ComposerTestDocker -ProjectRoot $root
    exit $LASTEXITCODE
}

Write-Host @"

Could not find Laragon PHP, Composer, or Docker.

Laragon (recommended):
  1. Install/start Laragon with PHP 8.1+ enabled.
  2. From this folder:
       .\run-tests.ps1
     or:
       .\composer.ps1 install
       .\composer.ps1 test

If Laragon is not at C:\laragon, set:
  `$env:LARAGON_ROOT = 'D:\your\laragon'

Docker: install Docker Desktop, then .\run-tests.ps1

"@ -ForegroundColor Yellow
exit 1
