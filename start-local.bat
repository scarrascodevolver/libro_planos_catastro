@echo off
echo Iniciando Apache y MySQL para Libro de Planos...

REM Iniciar Apache
net start Apache2.4 >nul 2>&1
if %errorlevel% neq 0 (
    C:\xampp\apache\bin\httpd.exe -k start
)

REM Iniciar MySQL
net start mysql >nul 2>&1
if %errorlevel% neq 0 (
    C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone --console
)

echo Servicios iniciados. Libro de Planos disponible en:
echo http://localhost/libro_planos/public
pause