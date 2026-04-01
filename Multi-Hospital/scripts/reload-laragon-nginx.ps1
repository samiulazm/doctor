# Reload Laragon nginx after editing C:\laragon\etc\nginx\sites-enabled\*.conf
# Run from PowerShell: .\scripts\reload-laragon-nginx.ps1

$laragon = if ($env:LARAGON_ROOT) { $env:LARAGON_ROOT.TrimEnd('\') } else { "C:\laragon" }
$nginx = Get-ChildItem -Path (Join-Path $laragon "bin\nginx") -Recurse -Filter "nginx.exe" -ErrorAction SilentlyContinue |
    Select-Object -First 1 -ExpandProperty FullName

if (-not $nginx) {
    Write-Error "nginx.exe not found under $laragon\bin\nginx"
    exit 1
}

Push-Location (Split-Path $nginx)
try {
    & $nginx -t
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    & $nginx -s reload
} finally {
    Pop-Location
}
