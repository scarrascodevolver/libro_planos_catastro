Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "cmd /c cd C:\xampp\htdocs\libro_planos && C:\xampp\php\php.exe artisan schedule:work", 0, False
Set WshShell = Nothing
