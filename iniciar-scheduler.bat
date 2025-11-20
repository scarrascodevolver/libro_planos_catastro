@echo off
echo ========================================
echo   SCHEDULER - LIBRO DE PLANOS
echo ========================================
echo.
echo Iniciando scheduler en segundo plano...
echo Este proceso liberara controles inactivos
echo cada 30 minutos automaticamente.
echo.
start /min cmd /c "cd C:\xampp\htdocs\libro_planos && C:\xampp\php\php.exe artisan schedule:work"
echo.
echo [OK] Scheduler iniciado correctamente
echo.
echo Puedes cerrar esta ventana.
echo La tarea seguira corriendo en segundo plano.
echo.
timeout /t 5
exit
