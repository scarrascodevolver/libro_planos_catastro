# 📋 **GUÍA COMPLETA DE INSTALACIÓN - SISTEMA LIBRO DE PLANOS**

**Fecha:** Septiembre 2025
**Sistema:** Libro de Planos Topográficos - Región del Biobío
**Entorno:** Windows + XAMPP Local

---

## 🎯 **RESUMEN**

Esta guía contiene **TODOS** los pasos necesarios para instalar completamente el sistema en un PC nuevo con Windows desde cero.

---

## 📋 **REQUISITOS PREVIOS**

### **Software Base Requerido:**
- ✅ **Windows 10/11** (64-bit)
- ✅ **XAMPP 8.2.x** o superior
- ✅ **Composer** (Gestor de dependencias PHP)
- ✅ **Git** (Control de versiones)
- ✅ **Navegador web** (Chrome/Firefox/Edge)

### **Especificaciones Mínimas:**
- **RAM:** 4GB mínimo (8GB recomendado)
- **Disco:** 2GB espacio libre
- **Procesador:** Dual-core 2.0GHz+

---

## 🔧 **PASO 1: INSTALACIÓN XAMPP**

### **1.1 Descargar e Instalar XAMPP**
1. Ir a: https://www.apachefriends.org/download.html
2. Descargar **XAMPP for Windows** (PHP 8.2.x)
3. Ejecutar instalador como **Administrador**
4. Instalar en: `C:\xampp` (ruta por defecto)
5. Seleccionar componentes:
   - ✅ **Apache**
   - ✅ **MySQL**
   - ✅ **PHP**
   - ✅ **phpMyAdmin**

### **1.2 Configuraciones CRÍTICAS PHP**

**⚠️ IMPORTANTE: Estas modificaciones son OBLIGATORIAS**

#### **A) Habilitar Extensiones PHP**
1. Abrir **XAMPP Control Panel**
2. Apache → **Config** → **php.ini**
3. Buscar y **descomentar** (quitar `;`) las siguientes líneas:

```ini
# EXTENSIONES OBLIGATORIAS - QUITAR ; al inicio
extension=gd           # Para PhpSpreadsheet (Excel)
extension=gd2          # Para PhpSpreadsheet (Excel)
extension=zip          # Para archivos comprimidos
extension=curl         # Para conexiones HTTP
extension=openssl      # Para HTTPS y encriptación
extension=pdo_mysql    # Para base de datos MySQL
extension=mbstring     # Para manejo de strings UTF-8
extension=fileinfo     # Para detección de tipos de archivo
extension=xml          # Para procesamiento XML
extension=intl         # Para internacionalización
```

#### **B) Configurar Límites de Archivos**
En el mismo archivo `php.ini`, buscar y modificar:

```ini
# LÍMITES PARA SUBIDA DE ARCHIVOS EXCEL
upload_max_filesize = 20M          # Antes: 2M
post_max_size = 25M                # Antes: 8M
max_execution_time = 300           # Antes: 30
max_input_time = 300               # Antes: 60
memory_limit = 512M                # Antes: 128M

# CONFIGURACIONES ADICIONALES
max_file_uploads = 20              # Archivos simultáneos
default_charset = "UTF-8"          # Codificación por defecto
```

#### **C) Configurar Zona Horaria**
Buscar y modificar:
```ini
date.timezone = "America/Santiago"  # Zona horaria Chile
```

### **1.3 Configurar MySQL**
1. Apache → **Config** → **my.ini**
2. Buscar sección `[mysql]` y verificar:

```ini
[mysql]
default-character-set = utf8mb4

[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
sql_mode = ""                      # IMPORTANTE: Vacío para evitar errores GROUP BY
```

### **1.4 Iniciar Servicios**
1. **Start** Apache
2. **Start** MySQL
3. Verificar que ambos estén en **verde**

---

## 📦 **PASO 2: INSTALACIÓN COMPOSER**

### **2.1 Descargar Composer**
1. Ir a: https://getcomposer.org/download/
2. Descargar **Composer-Setup.exe**
3. Ejecutar como **Administrador**
4. En la instalación:
   - **PHP executable:** `C:\xampp\php\php.exe`
   - Marcar **"Add to PATH"**

### **2.2 Verificar Instalación**
Abrir **CMD** y ejecutar:
```cmd
composer --version
```
Debe mostrar la versión instalada.

---

## 🐙 **PASO 3: INSTALACIÓN GIT**

### **3.1 Descargar Git**
1. Ir a: https://git-scm.com/download/win
2. Descargar **Git for Windows**
3. Instalar con configuración por defecto

### **3.2 Configurar Git (Opcional)**
```cmd
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

---

## 💾 **PASO 4: DESCARGAR EL PROYECTO**

### **4.1 Clonar Repositorio**
```cmd
cd C:\xampp\htdocs
git clone https://github.com/scarrascodevolver/libro_planos_catastro.git
cd libro_planos_catastro
```

### **4.2 Instalar Dependencias PHP**
```cmd
composer install
```

**⚠️ Si da error de extensiones faltantes:** Verificar que todas las extensiones del **Paso 1.2** estén habilitadas.

### **4.3 Instalar PhpSpreadsheet**
```cmd
composer require phpoffice/phpspreadsheet
```

**Si falla:** Verificar que `extension=gd` esté habilitada en `php.ini`.

---

## 🗄️ **PASO 5: CONFIGURAR BASE DE DATOS**

### **5.1 Crear Base de Datos**
1. Ir a: http://localhost/phpmyadmin
2. **Crear nueva base de datos:**
   - Nombre: `libro_planos_catastro`
   - Cotejamiento: `utf8mb4_unicode_ci`

### **5.2 Configurar Laravel**
1. Copiar archivo de configuración:
   ```cmd
   copy .env.example .env
   ```

2. Editar `.env` con los datos correctos:
   ```env
   APP_NAME="Libro de Planos Biobío"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=libro_planos_catastro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Generar clave de aplicación:
   ```cmd
   php artisan key:generate
   ```

### **5.3 Ejecutar Migraciones**
```cmd
php artisan migrate
php artisan db:seed
```

---

## 🚀 **PASO 6: VERIFICAR INSTALACIÓN**

### **6.1 Iniciar Servidor**
```cmd
php artisan serve
```

### **6.2 Acceder al Sistema**
1. **URL:** http://127.0.0.1:8000
2. **Credenciales de prueba:**

**Usuario Administrador (Registro):**
- **Email:** `alfonso.norambuena@biobio.cl`
- **Password:** `alfonso123`

**Usuario Consulta (Solo lectura):**
- **Email:** `consulta@biobio.cl`
- **Password:** `consulta123`

### **6.3 Verificar Funcionalidades**
✅ **Login funciona**
✅ **Tab 1:** Tabla general carga sin errores
✅ **Tab 2:** Importación muestra interface
✅ **Tab 3:** Crear planos (solo usuario registro)

---

## 🔧 **SOLUCIÓN DE PROBLEMAS COMUNES**

### **Error: "extension gd missing"**
**Solución:** Verificar en `php.ini` que esté descomentado:
```ini
extension=gd
```
Reiniciar Apache.

### **Error: "GROUP BY syntax error"**
**Solución:** En `my.ini` configurar:
```ini
sql_mode = ""
```
Reiniciar MySQL.

### **Error: "file upload too large"**
**Solución:** En `php.ini` aumentar:
```ini
upload_max_filesize = 20M
post_max_size = 25M
```

### **Error: "Composer not found"**
**Solución:** Verificar que Composer esté en PATH del sistema.

---

## 📁 **ESTRUCTURA FINAL ESPERADA**

```
C:\xampp\htdocs\libro_planos_catastro\
├── app/                    # Código Laravel
├── database/              # Migraciones y seeders
├── resources/             # Vistas y assets
├── public/               # Punto de entrada
├── vendor/               # Dependencias Composer
├── .env                  # Configuración local
├── composer.json         # Dependencias PHP
├── INSTALACION-XAMPP.md  # Este documento
└── README.md             # Documentación general
```

---

## ✅ **CHECKLIST FINAL DE VERIFICACIÓN**

### **Servicios XAMPP:**
- [ ] Apache corriendo (puerto 80)
- [ ] MySQL corriendo (puerto 3306)
- [ ] phpMyAdmin accesible

### **PHP Configurado:**
- [ ] extension=gd habilitada
- [ ] upload_max_filesize = 20M
- [ ] memory_limit = 512M
- [ ] Zona horaria configurada

### **Base de Datos:**
- [ ] Base `libro_planos_catastro` creada
- [ ] Migraciones ejecutadas (6 tablas)
- [ ] Seeders ejecutados (54 comunas + 2 usuarios)

### **Dependencias:**
- [ ] Composer instalado y funcionando
- [ ] PhpSpreadsheet instalado sin errores
- [ ] Todas las dependencias Laravel instaladas

### **Sistema Funcionando:**
- [ ] Login con usuarios de prueba
- [ ] Tab 1: Tabla general sin errores SQL
- [ ] Tab 2: Interface de importación visible
- [ ] Tab 3: Crear planos (usuario registro)

---

## 📞 **SOPORTE**

**Si encuentras problemas durante la instalación:**

1. **Verificar logs de Apache:** `C:\xampp\apache\logs\error.log`
2. **Verificar logs de Laravel:** `storage/logs/laravel.log`
3. **Verificar configuración PHP:** http://localhost/dashboard (sección PHP)

---

## 📝 **NOTAS ADICIONALES**

- **Backup automático:** El sistema no requiere configuración adicional de backup para uso local
- **Actualizaciones:** Para actualizar, hacer `git pull` y `composer update`
- **Seguridad:** En producción cambiar credenciales por defecto
- **Performance:** Para mejor rendimiento, usar SSD y 8GB+ RAM

---

**🎉 ¡INSTALACIÓN COMPLETA! EL SISTEMA ESTÁ LISTO PARA USAR.**