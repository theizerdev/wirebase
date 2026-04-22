@echo off
title Laravel Server - LAN
echo Buscando direccion IPv4...

FOR /F "tokens=*" %%A IN ('ipconfig ^| findstr "IPv4"') DO (
    FOR /F "tokens=2 delims=:" %%B IN ("%%A") DO (
        SET IP_LINE=%%B
    )
)

cd C:\laragon\www\Inversiones Danger 3000 C.A


SET IP_LOCAL=%IP_LINE: =%

IF NOT DEFINED IP_LOCAL (
    echo No se pudo encontrar la direccion IPv4. El script terminara.
    echo Asegurese de estar conectado a una red.
    pause
    exit /b
)

echo Direccion IPv4 encontrada: %IP_LOCAL%
echo.
echo Iniciando servidor de Laravel...
php artisan serve --host=%IP_LOCAL%

pause
