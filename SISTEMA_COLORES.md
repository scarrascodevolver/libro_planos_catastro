# 🎨 SISTEMA DE COLORES - LIBRO DE PLANOS DIGITAL

## 📋 PALETA DE COLORES OFICIAL

Esta documentación define el sistema de colores unificado para el proyecto, basado en el mockup profesional AdminLTE 3.

### 🔵 COLORES PRIMARIOS
```css
--primary-color: #074680    /* Azul Marino Principal */
--primary-dark: #053054     /* Azul Marino Oscuro (hover/activo) */
--primary-light: #0a5299    /* Azul Claro (efectos) */
--primary-hover: rgba(7, 70, 128, 0.1)  /* Hover transparente */
```

### 🎯 COLORES COMPLEMENTARIOS
```css
--secondary-color: #007bff  /* Azul Bootstrap (elementos secundarios) */
--accent-color: #e3f2fd     /* Azul Claro (fondos destacados) */
```

### ⚡ COLORES DE ESTADO
```css
--success-color: #28a745    /* Verde - Éxito */
--danger-color: #dc3545     /* Rojo - Error/Peligro */
--warning-color: #ffc107    /* Amarillo - Advertencia */
--info-color: #17a2b8       /* Cyan - Información */
```

### 🖼️ COLORES DE FONDO
```css
--bg-main: #f4f6f9          /* Fondo principal del sistema */
--bg-card: #ffffff          /* Fondo de cards/componentes */
--bg-sidebar: #053054       /* Fondo del sidebar */
--bg-hover: rgba(0, 123, 255, 0.1)  /* Hover en tablas */
```

---

## 📁 ARCHIVOS DEL SISTEMA

### 1. **ARCHIVO PRINCIPAL DE COLORES**
```
/public/css/custom-colors.css
```
- Contiene todas las variables CSS y reglas de estilo
- Se carga en todas las vistas que extienden AdminLTE
- Sobrescribe colores de Bootstrap y AdminLTE con nuestra paleta

### 2. **INCLUSIÓN EN VISTAS**
```blade
@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom-colors.css') }}">
@stop
```

---

## 🛠️ COMPONENTES CUBIERTOS

### ✅ BOTONES
- `.btn-primary` → Color azul marino #074680
- `.btn-info` → Mismo color primario 
- Estados hover → #053054

### ✅ CARDS Y HEADERS
- `.card-primary > .card-header`
- `.card-info > .card-header`
- `.card-navy > .card-header`
- Todos utilizan el color primario #074680

### ✅ TABS DEL SISTEMA
- `.card-navy.card-tabs` → Sistema completo de tabs
- Tab activo: #053054
- Tab inactivo: rgba(7, 70, 128, 0.1)

### ✅ BADGES Y ESTADOS
- `.badge-primary`, `.badge-info` → #074680
- Estados de sistema mantienen colores estándar

### ✅ TABLAS PROFESIONALES
- Headers: #074680 con texto blanco
- Hover: rgba(0, 123, 255, 0.1)
- Filas expandidas: #e3f2fd con borde #074680

### ✅ PAGINACIÓN
- Página activa: #074680
- Hover: #0a5299

### ✅ SIDEBAR
- Fondo: #053054
- Texto: Blanco rgba(255,255,255,0.9) para mejor contraste
- Enlaces hover: rgba(255,255,255,0.1)
- Enlaces activos: #074680 con texto blanco
- Iconos: rgba(255,255,255,0.8)

### ✅ FORMULARIOS
- Focus en inputs: border-color #0a5299
- Box-shadow: rgba(7, 70, 128, 0.25)

---

## 📋 GUÍAS DE USO

### ➕ AGREGAR NUEVOS COMPONENTES
1. **Usar variables CSS:**
   ```css
   .mi-componente {
       background-color: var(--primary-color);
       color: var(--text-white);
   }
   ```

2. **Para estados hover:**
   ```css
   .mi-componente:hover {
       background-color: var(--primary-dark);
   }
   ```

3. **Para fondos suaves:**
   ```css
   .mi-componente-suave {
       background-color: var(--primary-hover);
   }
   ```

### 🔄 MODIFICAR COLORES
1. **Cambiar solo en las variables CSS** (líneas 8-30 del archivo)
2. **NO modificar reglas individuales** - usar las variables
3. **Probar en todos los componentes** después de cambios

### 📱 RESPONSIVE
- El sistema incluye reglas responsive
- Los colores se mantienen en todas las resoluciones
- Componentes móviles conservan la paleta

---

## ✅ COMPONENTES PENDIENTES

### ✅ SMALL BOXES ESTADÍSTICAS
- **Total Planos:** #074680 (azul marino principal)
- **Saneamiento Rural:** #28a745 (verde éxito)
- **Saneamiento Urbano:** #17a2b8 (azul información)
- **Planos 2025:** #053054 (azul oscuro destacado)
- Texto: Blanco en todas para contraste
- Iconos: rgba(255,255,255,0.3)

### 🔄 POR IMPLEMENTAR:
- [ ] Modals con header personalizado
- [ ] Tooltips con colores consistentes
- [ ] Charts/gráficos con paleta del sistema
- [ ] Calendarios y date pickers
- [ ] Select2 y componentes avanzados

---

## 🚨 REGLAS IMPORTANTES

### ❌ NO HACER:
- ❌ **NO usar colores hardcodeados** como `#007bff` directamente
- ❌ **NO modificar AdminLTE** sin usar nuestras variables
- ❌ **NO crear estilos inline** que rompan la consistencia

### ✅ HACER:
- ✅ **Usar variables CSS** siempre que sea posible
- ✅ **Probar en modo claro/oscuro** si se implementa
- ✅ **Mantener contraste accesible** en todos los componentes
- ✅ **Documentar cambios** en este archivo

---

## 🔍 TESTING DE COLORES

### Verificar que funciona:
1. **Tabs principales** → Deben ser azul marino #074680
2. **Botones primarios** → Color consistente en todo el sistema  
3. **Headers de tablas** → Fondo azul marino con texto blanco
4. **Paginación** → Estados activos en azul marino
5. **Sidebar** → Fondo azul oscuro #053054

### En caso de problemas:
1. Verificar que `custom-colors.css` se carga correctamente
2. Revisar que no hay CSS conflictivo
3. Usar herramientas de desarrollador para inspeccionar
4. Verificar especificidad CSS (`!important` si es necesario)

---

## 📊 ESTADÍSTICAS DEL SISTEMA

- **Total variables CSS:** 15 variables principales
- **Componentes cubiertos:** 12+ tipos
- **Compatibilidad:** AdminLTE 3 + Bootstrap 4
- **Responsive:** ✅ Completamente responsive

---

*Documento actualizado: {{ date('Y-m-d') }}*
*Proyecto: Libro de Planos Digital - Región del Biobío*