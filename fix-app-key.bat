@echo off
echo ========================================
echo   FIX APP_KEY - LIBRO DE PLANOS
echo ========================================
echo.
echo Limpiando cachés...
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan cache:clear
echo.
echo Verificando APP_KEY en .env...
findstr "APP_KEY" .env
echo.
echo [OK] Si ves APP_KEY arriba, todo esta bien
echo.
echo RECUERDA:
echo - NO uses "php artisan config:cache" en desarrollo
echo - SOLO usa "config:cache" en produccion
echo.
pause
