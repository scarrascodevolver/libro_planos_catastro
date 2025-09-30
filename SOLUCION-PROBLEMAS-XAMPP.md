# 🚨 SOLUCIÓN PROBLEMAS XAMPP - LIBRO DE PLANOS

## 📋 **PROBLEMAS IDENTIFICADOS:**

### ❌ **PROBLEMA 1: Puerto 80 ocupado**
```
AH00072: make_sock: could not bind to address [::]:80
```
**Causa:** Otro servicio está usando el puerto 80 (IIS, Skype, etc.)

### ❌ **PROBLEMA 2: MySQL permisos**
```
InnoDB: The innodb_system data file 'ibdata1' must be writable
```
**Causa:** Permisos de escritura en carpeta MySQL

---

## ✅ **SOLUCIONES (ELIGE UNA):**

### **OPCIÓN A: Solución Rápida - Usar XAMPP Control Panel**

1. **Abrir XAMPP Control Panel como Administrador**
   ```
   Clic derecho en xampp-control.exe → "Ejecutar como administrador"
   ```

2. **Configurar puertos alternativos:**
   - Apache → **Config** → **httpd.conf**
   - Buscar: `Listen 80`
   - Cambiar por: `Listen 8080`
   - Guardar y cerrar

3. **Iniciar servicios:**
   - **Start** Apache (puerto 8080)
   - **Start** MySQL

4. **Acceder a la aplicación:**
   ```
   http://localhost:8080/libro_planos/public
   ```

### **OPCIÓN B: Liberar puerto 80**

1. **Detener IIS (si existe):**
   ```cmd
   # Ejecutar como Administrador
   net stop W3SVC
   net stop WAS
   iisreset /stop
   ```

2. **Verificar que el puerto esté libre:**
   ```cmd
   netstat -ano | findstr :80
   ```

3. **Iniciar Apache en puerto 80:**
   - XAMPP Control Panel → Start Apache

### **OPCIÓN C: Scripts automáticos creados**

1. **Ejecutar como Administrador:**
   ```cmd
   solucionar-problemas.bat
   ```
   o
   ```cmd
   configurar-puerto-8080.bat
   ```

---

## 🔧 **CONFIGURACIÓN MYSQL:**

### **Problema permisos MySQL:**

1. **Dar permisos completos:**
   ```cmd
   # Ejecutar como Administrador
   icacls "C:\xampp\mysql\data" /grant Everyone:F /T
   ```

2. **O cambiar propietario:**
   ```cmd
   takeown /f "C:\xampp\mysql\data" /r /d y
   ```

3. **Reiniciar MySQL:**
   - XAMPP Control Panel → Stop MySQL → Start MySQL

---

## 🎯 **CONFIGURACIÓN FINAL RECOMENDADA:**

### **1. Apache en puerto 8080:**
- **Ventaja:** No conflictos con otros servicios
- **URL:** `http://localhost:8080/libro_planos/public`

### **2. MySQL con permisos:**
- **Carpeta:** `C:\xampp\mysql\data` con permisos completos
- **Puerto:** 3306 (por defecto)

### **3. Configuración Laravel:**
```env
APP_URL=http://localhost:8080/libro_planos/public
DB_HOST=127.0.0.1
DB_PORT=3306
```

---

## ✅ **VERIFICACIÓN FINAL:**

### **Servicios corriendo:**
```cmd
# Verificar Apache
netstat -ano | findstr :8080

# Verificar MySQL
tasklist | findstr mysqld.exe
```

### **URLs de acceso:**
- 🌐 **Aplicación:** `http://localhost:8080/libro_planos/public`
- 🔍 **Diagnóstico:** `http://localhost:8080/libro_planos/public/diagnostico.php`
- 📊 **phpMyAdmin:** `http://localhost:8080/phpmyadmin`

### **Credenciales de prueba:**
```
Usuario: alfonso.norambuena@biobio.cl
Password: password
Rol: registro (completo)

Usuario: consulta@biobio.cl
Password: password
Rol: consulta (solo lectura)
```

---

## 🆘 **SI AÚN NO FUNCIONA:**

### **Usar servidor de desarrollo temporal:**
```cmd
cd C:\xampp\htdocs\libro_planos
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```
**URL:** `http://127.0.0.1:8000/planos`

### **Verificar logs:**
- **Apache:** `C:\xampp\apache\logs\error.log`
- **MySQL:** `C:\xampp\mysql\data\*.err`
- **Laravel:** `storage/logs/laravel.log`

---

## 📞 **PASOS SIGUIENTES:**

1. ✅ **Ejecutar:** `solucionar-problemas.bat` como **Administrador**
2. ✅ **Verificar:** URLs funcionen
3. ✅ **Login:** Con usuarios de prueba
4. ✅ **Probar:** Filtros Excel-like funcionando

**🎉 Una vez que Apache y MySQL estén corriendo, el sistema funcionará perfectamente con todos los filtros implementados.**