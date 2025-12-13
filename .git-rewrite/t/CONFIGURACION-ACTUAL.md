# ⚡ **CONFIGURACIÓN INMEDIATA - XAMPP ACTUAL**

**Para continuar con las pruebas HOY en tu XAMPP actual**

---

## 🔧 **1. HABILITAR EXTENSIÓN GD (OBLIGATORIO)**

### **Paso a paso:**
1. Abre **XAMPP Control Panel**
2. En la fila **Apache**, clic en **"Config"** → **"php.ini"**
3. Se abre el archivo en tu editor de texto
4. Presiona **Ctrl+F** y busca: `extension=gd`
5. Encontrarás líneas como:
   ```ini
   ;extension=gd
   ;extension=gd2
   ```
6. **QUITA los punto y coma** (`;`) del inicio:
   ```ini
   extension=gd
   extension=gd2
   ```
7. **Guarda el archivo** (Ctrl+S)
8. En XAMPP Control Panel, clic **"Stop"** en Apache
9. Espera 2 segundos, clic **"Start"** en Apache

### **Verificar que funcionó:**
Abre terminal CMD y ejecuta:
```cmd
php -m | find "gd"
```
Si aparece `gd`, está habilitada ✅

---

## 📁 **2. AUMENTAR LÍMITES DE ARCHIVOS (RECOMENDADO)**

**En el mismo archivo php.ini que acabas de abrir:**

1. Busca **Ctrl+F**: `upload_max_filesize`
2. Cambia de `2M` a `20M`:
   ```ini
   upload_max_filesize = 20M
   ```

3. Busca **Ctrl+F**: `post_max_size`
4. Cambia de `8M` a `25M`:
   ```ini
   post_max_size = 25M
   ```

5. Busca **Ctrl+F**: `max_execution_time`
6. Cambia de `30` a `300`:
   ```ini
   max_execution_time = 300
   ```

7. **Guarda** y **reinicia Apache** otra vez

---

## 📦 **3. INSTALAR PHPSPREADSHEET**

**Después de habilitar GD, en tu terminal:**

```cmd
cd C:\xampp\htdocs\libro_planos
composer require phpoffice/phpspreadsheet
```

**Si aún da error de GD:** Verifica que reiniciaste Apache después del paso 1.

**Si sigue fallando:** Usa versión alternativa:
```cmd
composer require phpoffice/phpspreadsheet:^1.29
```

---

## ✅ **4. VERIFICACIÓN RÁPIDA**

Una vez completados los pasos 1-3:

1. **Ir a:** http://localhost/libro_planos/public/planos/importacion/index
2. **Debería cargar** sin errores
3. **Botón "Vista previa"** debería estar activo
4. **No debería aparecer** errores de "Class IOFactory not found"

---

## 🚨 **SI ALGO FALLA:**

### **Error: "extension gd missing"**
- Verifica que quitaste los `;`
- Reinicia Apache
- Verifica con `php -m | find "gd"`

### **Error: "Class IOFactory not found"**
- PhpSpreadsheet no se instaló
- Ejecuta: `composer dump-autoload`
- Reinicia Apache

### **Error: "file too large"**
- Aumenta `upload_max_filesize` en php.ini
- Reinicia Apache

---

## 🎯 **OBJETIVO INMEDIATO:**

**Una vez completado esto, podemos probar:**
1. ✅ **Tab 2** con archivo Excel de prueba
2. ✅ **Importación Matrix** funcionando
3. ✅ **Preview de archivos** Excel

**¿Listos para continuar con las pruebas?** 🚀