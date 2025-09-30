@echo off
echo ==============================================
echo  CONFIGURACION LIBRO DE PLANOS PARA XAMPP
echo ==============================================

REM Paso 1: Generar clave de aplicacion
echo.
echo [1/5] Generando clave de aplicacion...
C:\xampp\php\php.exe artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: No se pudo generar la clave
    pause
    exit /b 1
)

REM Paso 2: Limpiar cache
echo.
echo [2/5] Limpiando cache...
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan route:clear

REM Paso 3: Cachear configuracion para produccion
echo.
echo [3/5] Cacheando configuracion...
C:\xampp\php\php.exe artisan config:cache

REM Paso 4: Verificar permisos
echo.
echo [4/5] Configurando permisos...
if exist "storage" (
    attrib -R storage /S
    echo Permisos de storage configurados
)
if exist "bootstrap\cache" (
    attrib -R bootstrap\cache /S
    echo Permisos de bootstrap/cache configurados
)

REM Paso 5: Iniciar servicios
echo.
echo [5/5] Iniciando servicios XAMPP...

REM Intentar iniciar Apache
net start Apache2.4 >nul 2>&1
if %errorlevel% equ 0 (
    echo Apache iniciado como servicio
) else (
    echo Iniciando Apache manualmente...
    start /B C:\xampp\apache\bin\httpd.exe
    timeout /t 3 >nul
)

REM Intentar iniciar MySQL
net start mysql >nul 2>&1
if %errorlevel% equ 0 (
    echo MySQL iniciado como servicio
) else (
    echo Iniciando MySQL manualmente...
    start /B C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone
    timeout /t 3 >nul
)

echo.
echo ==============================================
echo CONFIGURACION COMPLETADA!
echo.
echo Accede a la aplicacion en:
echo http://localhost/libro_planos/public
echo.
echo Para diagnosticos:
echo http://localhost/libro_planos/public/diagnostico.php
echo ==============================================
pause