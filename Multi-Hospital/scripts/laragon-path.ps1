# Prepends Laragon PHP (>= 8.1 preferred) and Composer to PATH for this session.
# Default root: C:\laragon — override with $env:LARAGON_ROOT

param(
    [string] $LaragonRoot = $(if ($env:LARAGON_ROOT) { $env:LARAGON_ROOT } else { "C:\laragon" })
)

Remove-Item Env:\LARAGON_PHP_BELOW_81 -ErrorAction SilentlyContinue

if (-not (Test-Path $LaragonRoot)) {
    return
}

$phpBins = @(Get-ChildItem -Path (Join-Path $LaragonRoot "bin\php") -Recurse -Filter "php.exe" -ErrorAction SilentlyContinue)
$minPhp = New-Object System.Version 8, 1, 0
$bestExe = $null
$bestVer = $null

foreach ($exe in $phpBins) {
    try {
        $verOut = & $exe.FullName -r "echo PHP_VERSION;" 2>$null
        if ([string]::IsNullOrWhiteSpace($verOut)) { continue }
        $verOut = $verOut.Trim()
        if (-not ($verOut -match '^\d+\.\d+')) { continue }
        $v = [System.Version]$verOut
        if ($v -ge $minPhp -and ($null -eq $bestVer -or $v -gt $bestVer)) {
            $bestVer = $v
            $bestExe = $exe
        }
    } catch {
        continue
    }
}

if ($bestExe) {
    $phpDir = $bestExe.Directory.FullName
    if ($env:Path -notlike "*$phpDir*") {
        $env:Path = "$phpDir;$env:Path"
    }
} elseif ($phpBins.Count -gt 0) {
    $fallback = $phpBins | Sort-Object FullName -Descending | Select-Object -First 1
    $phpDir = $fallback.Directory.FullName
    if ($env:Path -notlike "*$phpDir*") {
        $env:Path = "$phpDir;$env:Path"
    }
    $env:LARAGON_PHP_BELOW_81 = "1"
}

$composerDir = Join-Path $LaragonRoot "bin\composer"
if (Test-Path $composerDir) {
    if ($env:Path -notlike "*$composerDir*") {
        $env:Path = "$composerDir;$env:Path"
    }
}
