@echo off
chcp 65001 >nul
title Sargora - serveur local
cd /d "%~dp0"
set PHP=php
where php >nul 2>nul || set PHP=C:\xampp\php\php.exe
echo.
echo   SARGORA  -  Grow brands. Create impact.
echo   Site     : http://localhost:8090
echo   Admin    : http://localhost:8090/admin
echo   (fermez cette fenetre pour arreter le serveur)
echo.
start "" http://localhost:8090
"%PHP%" -S localhost:8090 router.php
