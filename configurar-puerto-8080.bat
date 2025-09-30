@echo off
echo ==========================================
echo  CONFIGURANDO APACHE EN PUERTO 8080
echo ==========================================

echo.
echo Este script configurará Apache para usar el puerto 8080
echo en lugar del 80 para evitar conflictos.
echo.
pause

REM Hacer backup del archivo de configuración
if exist "C:\xampp\apache\conf\httpd.conf" (
    echo Haciendo backup de httpd.conf...
    copy "C:\xampp\apache\conf\httpd.conf" "C:\xampp\apache\conf\httpd.conf.backup"
)

REM Crear archivo temporal con la configuración modificada
echo # Configuración Apache Puerto 8080 para Libro de Planos > temp_httpd_patch.txt
echo. >> temp_httpd_patch.txt
echo Listen 8080 >> temp_httpd_patch.txt
echo ServerName localhost:8080 >> temp_httpd_patch.txt

echo.
echo ==========================================
echo INSTRUCCIONES MANUALES:
echo ==========================================
echo.
echo 1. Abrir C:\xampp\apache\conf\httpd.conf
echo 2. Buscar la línea "Listen 80"
echo 3. Cambiarla por "Listen 8080"
echo 4. Buscar "ServerName localhost:80"
echo 5. Cambiarla por "ServerName localhost:8080"
echo 6. Guardar el archivo
echo 7. Reiniciar Apache desde XAMPP Control Panel
echo.
echo Después podrás acceder en:
echo http://localhost:8080/libro_planos/public
echo.
pause

REM Intentar aplicar cambios automáticamente
echo Intentando aplicar cambios automáticamente...
powershell -Command "(Get-Content 'C:\xampp\apache\conf\httpd.conf') -replace 'Listen 80', 'Listen 8080' -replace 'ServerName localhost:80', 'ServerName localhost:8080' | Set-Content 'C:\xampp\apache\conf\httpd.conf'"

if %errorlevel% equ 0 (
    echo ✅ Configuración aplicada automáticamente
    echo Reiniciando Apache...

    REM Detener Apache
    taskkill /F /IM httpd.exe >nul 2>&1
    timeout /t 2

    REM Iniciar Apache
    cd /d "C:\xampp\apache\bin"
    start /B httpd.exe
    timeout /t 3

    echo.
    echo ✅ Apache configurado en puerto 8080
    echo 🌐 Accede a: http://localhost:8080/libro_planos/public
) else (
    echo ❌ No se pudo aplicar automáticamente
    echo 💡 Aplica los cambios manualmente según las instrucciones arriba
)

echo.
pause