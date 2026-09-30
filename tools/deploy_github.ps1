# Sargora - publication sur GitHub (code source + site en ligne GitHub Pages)
# Usage : deploy.bat   (ou : powershell -File tools\deploy_github.ps1 -Repo compte/depot)
param([string]$Repo = 'contactnasriimen-ctrl/sargora')
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)
$owner, $name = $Repo.Split('/')
$php = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $php) { $php = 'C:\xampp\php\php.exe' }

Write-Host '1/3  Sauvegarde du code source...' -ForegroundColor Yellow
git add -A
git diff --cached --quiet
if ($LASTEXITCODE -ne 0) { git commit -q -m ("Mise a jour du site " + (Get-Date -Format 'yyyy-MM-dd HH:mm')) }
git push -q origin main

Write-Host '2/3  Generation de la version statique...' -ForegroundColor Yellow
& $php tools/build_static.php "/$name" "https://$owner.github.io/$name" dist
if ($LASTEXITCODE -ne 0) { throw 'La generation a echoue.' }

Write-Host '3/3  Publication sur GitHub Pages...' -ForegroundColor Yellow
Push-Location dist
git init -q
git checkout -q -B gh-pages
git add -A
git commit -q -m ("Publication " + (Get-Date -Format 'yyyy-MM-dd HH:mm'))
git push -q -f "https://github.com/$Repo.git" gh-pages
Pop-Location

Write-Host ''
Write-Host "Termine ! Le site sera a jour dans 1 a 2 minutes : https://$owner.github.io/$name/" -ForegroundColor Green
