@echo off
chcp 65001 >nul
title Sargora - publication GitHub
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File tools\deploy_github.ps1
echo.
pause
