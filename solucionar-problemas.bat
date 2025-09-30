@echo off
echo ==========================================
echo  SOLUCIONANDO PROBLEMAS XAMPP
echo ==========================================

echo.
echo [1/4] Verificando servicios que usan puerto 80...
netstat -ano | findstr :80

echo.
echo [2/4] Deteniendo servicios que pueden conflictir...

REM Detener IIS si esta corriendo
echo Deteniendo IIS...
net stop W3SVC >nul 2>&1
net stop WAS >nul 2>&1
iisreset /stop >nul 2>&1

REM Detener SQL Server Reporting Services si existe
echo Deteniendo SQL Server Reporting Services...
net stop "SQL Server Reporting Services" >nul 2>&1
net stop "ReportServer" >nul 2>&1

echo.
echo [3/4] Configurando permisos para MySQL...
REM Dar permisos completos a la carpeta de datos MySQL
if exist "C:\xampp\mysql\data" (
    echo Configurando permisos en C:\xampp\mysql\data...
    icacls "C:\xampp\mysql\data" /grant Everyone:F /T >nul 2>&1
    echo Permisos de MySQL configurados
)

echo.
echo [4/4] Intentando iniciar servicios XAMPP...

REM Matar procesos Apache/MySQL existentes
taskkill /F /IM httpd.exe >nul 2>&1
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

REM Iniciar Apache en puerto alternativo si 80 esta ocupado
echo Iniciando Apache...
cd /d "C:\xampp\apache\bin"
start /B httpd.exe
timeout /t 3 >nul

REM Iniciar MySQL
echo Iniciando MySQL...
cd /d "C:\xampp\mysql\bin"
start /B mysqld.exe --defaults-file=..\my.ini --standalone --console
timeout /t 5 >nul

echo.
echo ==========================================
echo VERIFICACION:
echo ==========================================
echo.
echo Verificando Apache...
netstat -ano | findstr :80
if %errorlevel% equ 0 (
    echo ✅ Apache corriendo en puerto 80
) else (
    echo ❌ Apache no pudo iniciarse en puerto 80
    echo 💡 Usando puerto alternativo...
    echo    Accede a: http://localhost:8080/libro_planos/public
)

echo.
echo Verificando MySQL...
tasklist | findstr mysqld.exe >nul
if %errorlevel% equ 0 (
    echo ✅ MySQL corriendo
) else (
    echo ❌ MySQL no pudo iniciarse
    echo 💡 Revisar permisos en C:\xampp\mysql\data
)

echo.
echo ==========================================
echo URLs DE ACCESO:
echo ==========================================
echo 🌐 Aplicación: http://localhost/libro_planos/public
echo 🔍 Diagnóstico: http://localhost/libro_planos/public/diagnostico.php
echo 📊 phpMyAdmin: http://localhost/phpmyadmin
echo ⚙️  XAMPP Panel: C:\xampp\xampp-control.exe
echo.
pause