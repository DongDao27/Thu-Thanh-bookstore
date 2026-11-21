param([ValidateRange(1024,65535)][int]$Port = 3000)
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path $PSScriptRoot -Parent
$frontendRoot = Join-Path $projectRoot 'frontend'
$listeners = @(Get-NetTCPConnection -State Listen -LocalPort $Port -ErrorAction SilentlyContinue)
if ($listeners.Count -gt 0) {
    foreach ($ownerId in @($listeners.OwningProcess | Select-Object -Unique)) {
        $owner = Get-CimInstance Win32_Process -Filter "ProcessId=$ownerId"
        if ($owner.Name -ne 'node.exe' -or $owner.CommandLine -notlike "*$frontendRoot*node_modules*next*start-server.js*") {
            throw "Port $Port is owned by PID $ownerId outside this frontend. Check that process before starting another server."
        }
    }
    Write-Output "This project's frontend is already running: http://localhost:$Port"
    Write-Output 'Open that URL. To restart, press Ctrl+C in its existing terminal first.'
    return
}
Push-Location $frontendRoot
try {
    & npm exec -- next dev --hostname localhost --port $Port
    if ($LASTEXITCODE -ne 0) { throw 'Frontend server stopped with an error.' }
} finally {
    Pop-Location
}
