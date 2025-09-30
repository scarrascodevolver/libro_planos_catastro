
    responsable VARCHAR(255),
    proyecto VARCHAR(255), -- Acepta "CONVENIO-FINANCIAMIENTO" o cualquier otro nombre
    total_hectareas DECIMAL(10,4),
    total_m2 BIGINT,
    cantidad_folios INT,
    observaciones TEXT,
    archivo VARCHAR(255),
    tubo VARCHAR(255),
    tela VARCHAR(255), 
    archivo_digital VARCHAR(255),
    created_by BIGINT UNSIGNED, -- FK a users
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (created_by) REFERENCES users(id)
);
TABLA 3: planos_folios
sqlCREATE TABLE planos_folios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plano_id BIGINT UNSIGNED NOT NULL,
    folio VARCHAR(50), -- NO unique, puede repetirse en diferentes planos
    solicitante VARCHAR(255),
    apellido_paterno VARCHAR(255),
    apellido_materno VARCHAR(255),
    tipo_inmueble ENUM('HIJUELA','SITIO'),
    numero_inmueble INT,
    hectareas DECIMAL(10,4), -- Solo para HIJUELA (NULL para sitios)
    m2 BIGINT, -- Para ambos tipos
    is_from_matrix BOOLEAN DEFAULT true, -- true=Matrix, false=Manual
    matrix_folio VARCHAR(50), -- Referencia original Matrix
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE CASCADE
);
TABLA 4: matrix_import
sqlCREATE TABLE matrix_import (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(50), -- NO unique, puede repetirse
    tipo_inmueble VARCHAR(100),
    provincia VARCHAR(100), 
    comuna VARCHAR(100),
    nombres VARCHAR(255),
    apellido_paterno VARCHAR(255),
    apellido_materno VARCHAR(255),
    responsable VARCHAR(255),
    convenio_financiamiento VARCHAR(255), -- Nombre original del campo Matrix
    batch_import VARCHAR(50), -- MATRIX-2025-09
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_folio (folio),
    INDEX idx_batch (batch_import)
);
TABLA 5: session_control
sqlCREATE TABLE session_control (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    has_control BOOLEAN DEFAULT false,
    requested_at TIMESTAMP NULL,
    granted_at TIMESTAMP NULL,
    released_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_control (has_control, is_active),
    INDEX idx_user_session (user_id, session_id)
);
TABLA 6: comunas_biobio
sqlCREATE TABLE comunas_biobio (
    codigo CHAR(3) PRIMARY KEY, -- 101, 102, 301, 201, 401
    nombre VARCHAR(100) NOT NULL,
    provincia VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

TAB 1: TABLA GENERAL - ESPECIFICACIÓN COMPLETA
CONTEXTO:
TAB principal donde usuarios consultan y gestionan planos existentes.
INTERFAZ TAB 1:
HEADER:
LIBRO DE PLANOS TOPOGRÁFICOS          [Mostrar/Ocultar Filtros]
Mostrando 1-25 de 1,245 registros | Filtros activos: 2
[Buscar global] [Mostrar: 25] [Excel] [PDF] [Print]
FILTROS AVANZADOS (3 FILAS):
FILTROS AVANZADOS                                    [Ocultar]
┌─────────────────────────────────────────────────────────────────┐
│ ROW 1: [Comuna] [Año] [Mes] [Responsable] [Proyecto]           │
│ ROW 2: [Folio] [Solicitante] [Ap.Pat] [Ap.Mat]                 │  
│ ROW 3: Hectáreas: [Min] a [Max]  M²: [Min] a [Max]             │
└─────────────────────────────────────────────────────────────────┘
[Limpiar] [Aplicar]
DATATABLE PRINCIPAL:
COLUMNAS (15 total):

[EDITAR] - Solo usuarios con rol 'registro'
[REASIGNAR] - Solo usuarios con rol 'registro'
N° PLANO - 0830329271SR
FOLIOS - Formato especial según cantidad
SOLICITANTE - Nombre o "MÚLTIPLES"
APELLIDO PATERNO - Real o "-"
APELLIDO MATERNO - Real o "-"
COMUNA - Texto
HECTÁREAS - Número o "-"
M² - Número
MES - Texto
AÑO - Número
RESPONSABLE - Texto
PROYECTO - Texto
[+/-] - Expandir/Colapsar

FORMATO COLUMNA FOLIOS (CRÍTICO):
php// LÓGICA DISPLAY:
// - 1 folio:     "123456"
// - 2 folios:    "123456, 789012"  
// - 3+ folios:   "123456, 789012 +4 más"

function getDisplayFolios($folios) {
    $count = $folios->count();
    if ($count <= 2) {
        return $folios->pluck('folio')->join(', ');
    }
    $first_two = $folios->take(2)->pluck('folio')->join(', ');
    return $first_two . " +{$count-2} más";
}
FILAS EXPANDIBLES - REPRESENTACIÓN VISUAL:
EJEMPLO 1: Plano con 1 folio
COLAPSADO:
| [✏️] | [🔄] | 0830329271SR | 123456 | JUAN | PEREZ | GONZALEZ | CONCEPCIÓN | 2,50 | 25000 | DIC | 2025 | CARLOS | CONVENIO | [+] |
EXPANDIDO:
| [✏️] | [🔄] | 0830329271SR | 123456 | JUAN | PEREZ | GONZALEZ | CONCEPCIÓN | 2,50 | 25000 | DIC | 2025 | CARLOS | CONVENIO | [-] |
│      │      │   └ Folio    │ 123456 │ JUAN │ PEREZ │ GONZALEZ │           │ 2,50 │ 25000 │     │      │        │          │     │
EJEMPLO 2: Plano con 6 folios
COLAPSADO:
| [✏️] | [🔄] | 0830329273CR | 111111, 222222 +4 más | MÚLTIPLES | - | - | TALCAHUANO | 12,30 | 123000 | FEB | 2025 | ANA | FISCAL | [+] |
EXPANDIDO:
| [✏️] | [🔄] | 0830329273CR | 111111, 222222 +4 más | MÚLTIPLES | - | - | TALCAHUANO | 12,30 | 123000 | FEB | 2025 | ANA | FISCAL | [-] |
│      │      │   └ Folio    │ 111111                 │ JUAN     │PEREZ │GONZALEZ│           │ 2,50  │ 25000 │     │      │       │          │     │
│      │      │   └ Folio    │ 222222                 │ MARIA    │LOPEZ │RAMIREZ │           │ 1,75  │ 17500 │     │      │       │          │     │
│      │      │   └ Folio    │ 333333                 │ PEDRO    │SILVA │MORALES │           │ 2,25  │ 22500 │     │      │       │          │     │
│      │      │   └ Folio    │ 444444                 │ ANA      │ROJAS │CASTRO  │           │ 1,50  │ 15000 │     │      │       │          │     │
│      │      │   └ Folio    │ 555555                 │ LUIS     │TORRES│HERRERA │           │ 2,75  │ 27500 │     │      │       │          │     │
│      │      │   └ Folio    │ 666666                 │ CARMEN   │ VEGA │ MORENO │           │ 2,00  │ 20000 │     │      │       │          │     │
IMPLEMENTACIÓN TÉCNICA DATATABLE:
CONFIGURACIÓN DATATABLE:
javascript$('#planosTable').DataTable({
    "pageLength": 25,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
    "searching": true,
    "ordering": true,
    "info": true,
    "responsive": {
        "details": {
            "type": "column",
            "target": -1
        }
    },
    "columnDefs": [{
        "className": "dtr-control",
        "orderable": false,
        "targets": -1
    }],
    "buttons": [
        {
            "extend": "excel",
            "text": "Excel",
            "exportOptions": {
                "columns": ":visible"
            }
        },
        "pdf", "print"
    ]
});
FILTROS AVANZADOS POR COLUMNA:
javascript// Filtros específicos
- Comuna: Select con todas las comunas disponibles
- Año: Select con años únicos de planos
- Mes: Select con meses disponibles  
- Responsable: Select con responsables únicos
- Proyecto: Select con proyectos únicos
- Folio: Input texto para búsqueda exacta
- Solicitante: Input texto para búsqueda parcial
- Apellidos: Input texto para búsqueda parcial
- Hectáreas: Range slider (min-max)
- M²: Range slider (min-max)
CONSULTA OPTIMIZADA:
php$query = Plano::select([
    'planos.*',
    DB::raw('COUNT(planos_folios.id) as cantidad_folios'),
    DB::raw('SUM(planos_folios.hectareas) as total_hectareas'),
    DB::raw('SUM(planos_folios.m2) as total_m2'),
    DB::raw('GROUP_CONCAT(planos_folios.folio ORDER BY planos_folios.id LIMIT 2) as primeros_folios')
])
->leftJoin('planos_folios', 'planos.id', '=', 'planos_folios.plano_id')
->groupBy('planos.id');
ESTRUCTURA HTML EXPANDIBLE:
php// Filas hijo para expansión
foreach($plano->folios as $folio) {
    echo "<tr class='child-row'>";
    echo "<td></td><td></td>"; // Columnas vacías EDITAR/REASIGNAR
    echo "<td>└ Folio</td>";
    echo "<td>{$folio->folio}</td>";
    echo "<td>{$folio->solicitante}</td>";
    echo "<td>{$folio->apellido_paterno}</td>";
    echo "<td>{$folio->apellido_materno}</td>";
    echo "<td></td>"; // Comuna vacía en detalle
    echo "<td>{$folio->hectareas}</td>";
    echo "<td>{$folio->m2}</td>";
    echo "<td></td><td></td><td></td><td></td><td></td>"; // Resto vacío
    echo "</tr>";
}
CONTROL DE ACCESO:

CONSULTA: Ve todo, no puede editar
REGISTRO: Ve todo + columnas EDITAR/REASIGNAR + puede modificar


TAB 2: IMPORTACIÓN MASIVA - ESPECIFICACIÓN COMPLETA
CONTEXTO:
Tab para alimentar sistema con dos tipos de archivos diferentes.
INTERFAZ TAB 2 - DOS SECCIONES:
SECCIÓN A: MATRIX IMPORTER (Mensual)
ACTUALIZACIÓN MATRIX MENSUAL
┌─────────────────────────────────────────────────────────────┐
│ Archivo: MATRIX-2025-09.xlsx                               │
│ [Seleccionar archivo] [Vista previa] [Importar]            │
│                                                             │
│ COLUMNAS EXTRAÍDAS: Solo 8 de todas las disponibles        │
│ Última importación: 15/08/2025 - 1,234 registros          │
└─────────────────────────────────────────────────────────────┘
SECCIÓN B: HISTORICAL IMPORTER (Una vez)
IMPORTACIÓN PLANOS HISTÓRICOS
┌─────────────────────────────────────────────────────────────┐
│ Archivo: PLANOS-HISTORICOS.xlsx                            │
│ [Seleccionar archivo] [Vista previa] [Importar]            │
│                                                             │
│ ATENCIÓN: Esta importación se realiza una sola vez         │
└─────────────────────────────────────────────────────────────┘
MATRIX IMPORTER:

Formato: Excel (.xlsx)
Frecuencia: Mensual
Extracción automática de 8 columnas:

TIPO INMUEBLE
Comuna
NOMBRES
APELLIDO PATERNO
APELLIDO MATERNO
FOLIOS-EXPEDIENTES
RESPONSABLE
CONVENIO-FINANCIAMIENTO


Destino: tabla matrix_import
Duplicados: Validación inteligente + opción actualizar/mantener

HISTORICAL IMPORTER:

Formato: Excel (.xlsx)
Frecuencia: Una sola vez
21 columnas del sistema anterior
Destino: tablas planos + planos_folios
Validación: Números de plano únicos, folios pueden repetirse


TAB 3: AGREGAR PLANOS - ESPECIFICACIÓN COMPLETA
CONTROL DE SESIONES (CRÍTICO):
Solo UN usuario puede crear números correlativos simultáneamente.
CONTROL DE NUMERACIÓN CORRELATIVA
┌─────────────────────────────────────────────────────────────┐
│ Estado: TIENES CONTROL - Puedes crear nuevos números       │
│ Último correlativo: 29271                                   │
│ Próximo número: 0830329272SU                               │
│ [Liberar control]                                           │
└─────────────────────────────────────────────────────────────┘
SELECTOR INICIAL - TIPO DE PLANO:
TIPO DE PLANO A CREAR
┌─────────────────────────────────────────────────────────────┐
│ ○ PLANO MATRIX - Folios desde base de datos Matrix         │
│ ○ PLANO MANUAL - Ingreso libre (fiscales y otros)          │
└─────────────────────────────────────────────────────────────┘
PLANO MATRIX - OPCIONES DE CANTIDAD:
CANTIDAD DE FOLIOS (PLANO MATRIX)
┌─────────────────────────────────────────────────────────────┐
│ ○ 1 FOLIO - Formulario simple                              │
│ ○ 2-10 FOLIOS - Formulario múltiple                        │  
│ ○ FOLIOS MASIVOS (11-150) - Importación masiva             │
└─────────────────────────────────────────────────────────────┘
OPCIÓN 1: 1 FOLIO

Input único para folio
Auto-completado inmediato desde Matrix
Validación existencia en BD

OPCIÓN 2: 2-10 FOLIOS

10 inputs disponibles
Búsqueda individual por folio
Filas aparecen conforme se encuentran

OPCIÓN 3: FOLIOS MASIVOS

Textarea para pegar lista masiva
Detección automática de folios
Listado encontrados vs no encontrados
Opción agregar folios manuales

PLANO MANUAL:

Sin validación Matrix obligatoria
Campos editables manualmente
Para casos fiscales: "FISCO DE CHILE" sin apellidos

TIPOS DE PLANO:
Después de seleccionar Matrix/Manual, elegir:

Tipo: ○ Saneamiento ○ Fiscal
Ubicación: ○ Urbano ○ Rural

Combinaciones:

Saneamiento + Urbano = SU
Saneamiento + Rural = SR
Fiscal + Urbano = CU
Fiscal + Rural = CR

CAMPOS POR UBICACIÓN:

Rural: Hijuela + Hectáreas + M²
Urbano: Solo M²

NUMERACIÓN AUTOMÁTICA:
php// 08 + codigo_comuna + correlativo + tipo + ubicacion
$numero = '08' . $codigo_comuna . $correlativo . $tipo_ubicacion;
// Ejemplo: 0830329272SU

TAB 4: REPORTES - PENDIENTE
Definir después de funcionalidades core implementadas.

SISTEMA DE CONTROL DE SESIONES
OBJETIVO:
Solo un usuario puede crear números correlativos manteniendo secuencia.
ESTADOS:

CONTROL ACTIVO: Usuario puede crear números
SIN CONTROL: Debe solicitar acceso
SOLICITUD PENDIENTE: Esperando aprobación

NOTIFICACIONES:

Tiempo real con WebSockets
Solicitudes y respuestas instantáneas
Estado actualizado automáticamente


ARQUITECTURA MODULAR
LÍMITES POR ARCHIVO:

Controllers: máx 300 líneas
Models: máx 200 líneas
Blade templates: máx 250 líneas
JavaScript: máx 300 líneas
CSS: máx 200 líneas

ESTRUCTURA RECOMENDADA:
Controllers (app/Http/Controllers/Admin/):
PlanoController.php              # CRUD básico (300 líneas max)
PlanoMasivoController.php        # Procesamiento masivo (250 líneas)
PlanoValidacionController.php    # Validaciones específicas (200 líneas)
PlanoImportacionController.php   # Tab 2 importadores (250 líneas)
SessionControlController.php     # Control de sesiones (200 líneas)
Models (app/Models/):
User.php                         # Usuario + roles (150 líneas)
Plano.php                        # Modelo principal (200 líneas)
PlanoFolio.php                   # Detalle folios (150 líneas)
MatrixImport.php                 # Auto-completado (100 líneas)
SessionControl.php               # Control sesiones (100 líneas)
ComunaBiobio.php                 # Catálogo (80 líneas)

Traits/PlanoValidationTrait.php  # Validaciones (150 líneas)
Traits/PlanoNumberTrait.php      # Numeración (100 líneas)
Views (resources/views/admin/planos/):
# 4 tabs principales
index.blade.php                  # Tab 1 - Tabla (250 líneas)
importacion.blade.php            # Tab 2 - Importación (250 líneas)
agregar.blade.php                # Tab 3 - Agregar (200 líneas)
reportes.blade.php               # Tab 4 - Reportes (150 líneas)

# Formularios específicos
create-matrix.blade.php          # Planos Matrix (200 líneas)
create-manual.blade.php          # Planos manuales (150 líneas)
create-masivo.blade.php          # Interface masiva (250 líneas)

partials/
├── tab-navigation.blade.php     # Navegación tabs (60 líneas)
├── session-control.blade.php    # Control sesiones (100 líneas)
├── folio-search.blade.php       # Búsqueda individual (80 líneas)  
├── bulk-input.blade.php         # Input masivo (100 líneas)
└── modals/
    ├── edit-plano.blade.php     # Edición (120 líneas)
    ├── reasignar.blade.php      # Reasignar número (100 líneas)
    └── session-request.blade.php # Solicitar control (80 líneas)
JavaScript (resources/js/admin/planos/):
plano-tabs.js                    # Navegación tabs (100 líneas)
plano-tabla.js                   # Tab 1 DataTable (250 líneas)
plano-importacion.js             # Tab 2 importadores (300 líneas)
plano-agregar.js                 # Tab 3 formularios (300 líneas)
session-control.js               # Control sesiones (200 líneas)

components/
├── folio-searcher.js            # Búsqueda reutilizable (150 líneas)
├── bulk-processor.js            # Procesador masivo (200 líneas)
├── matrix-autocomplete.js       # Auto-completado (120 líneas)
└── validation-display.js        # Mostrar errores (100 líneas)

PROTECCIÓN DE BASE DE DATOS

NUNCA ejecutar sin confirmación: migrate:fresh, migrate:reset, migrate:refresh, db:wipe
SIEMPRE preguntar antes de comandos destructivos
RECORDAR hacer backup antes de migraciones


PROCESO DE DESARROLLO
FASE 2: BASE DE DATOS (ACTUAL)

Crear migraciones con estructura definida
Ejecutar migraciones y verificar
Crear seeders para comunas y usuarios test

FASE 3: BACKEND MODULAR

Modelos con relaciones
Controllers separados por funcionalidad
Middleware para control de sesiones
Validaciones y traits

FASE 4: FRONTEND MODULAR

Layout AdminLTE + 4 tabs
Tab 1: DataTable con filtros
Tab 2: Importadores duales
Tab 3: Formularios Matrix/Manual

FASE 5: INTEGRACIÓN

Sistema control sesiones completo
Auto-completado Matrix funcionando
Validaciones tiempo real
Numeración automática


COMANDOS ÚTILES
bash# Servidor
cd C:\xampp\htdocs\libro_planos
php artisan serve

# Migraciones
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Cache
php artisan config:clear
php artisan cache:clear

DECISIONES TÉCNICAS CONFIRMADAS

Blade + AJAX (no Vue.js)
Numeración: 0830329272SU (S=Saneamiento, C=Fiscal)
Estructura 6 tablas optimizada
Control sesiones único
Tipos: SR/SU (saneamiento) vs CR/CU (fiscal)
Campos por ubicación: Rural (hijuela+ha+m²) vs Urbano (m²)
Casos especiales: FISCO DE CHILE sin apellidos
Folios 1-150 por plano (mayoría 1-10)
DataTable expandible con representación visual específica

SISTEMA COMPLETAMENTE ESPECIFICADO - LISTO PARA IMPLEMENTACIÓN

---

## 🎯 **ESTADO IMPLEMENTACIÓN - FASE 2 BD COMPLETADA**
**Fecha:** 2025-09-22
**Estado:** ✅ BASE DE DATOS OPERACIONAL

### ✅ **MIGRACIONES EJECUTADAS:**
```bash
✅ 2025_09_22_000001_add_role_to_users_table.php
✅ 2025_09_22_000002_create_comunas_biobio_table.php
✅ 2025_09_22_000003_create_planos_table.php
✅ 2025_09_22_000004_create_planos_folios_table.php
✅ 2025_09_22_000005_create_matrix_import_table.php
✅ 2025_09_22_000006_create_session_control_table.php
```

### ✅ **SEEDERS EJECUTADOS:**
```bash
✅ ComunaBiobioSeeder: 54 comunas cargadas
  - Provincia Concepción: 12 comunas (101-112)
  - Provincia Arauco: 7 comunas (201-207)
  - Provincia Biobío: 14 comunas (301-314)
  - Provincia Ñuble: 21 comunas (401-421)

✅ UserSeeder: 2 usuarios creados
  - Alfonso Norambuena (alfonso.norambuena@biobio.cl) - Rol: registro
  - Usuario Consulta (consulta@biobio.cl) - Rol: consulta
```

### ✅ **ESTRUCTURA BD VERIFICADA:**
- ✅ **6 tablas creadas** con todas las FK y índices
- ✅ **54 comunas** del Biobío con códigos oficiales
- ✅ **2 usuarios** listos (registro + consulta)
- ✅ **Tablas principales** vacías y listas para datos
- ✅ **Relaciones FK** funcionando correctamente

### 🎯 **SIGUIENTE FASE: BACKEND + FRONTEND**
**Prioridad:** Crear controladores y vistas para Tab 1 (Tabla General)

#### **PRÓXIMOS PASOS:**
1. **Modelos Laravel** (User, Plano, PlanoFolio, etc.)
2. **PlanoController** con funciones CRUD
3. **Vistas Blade** para 4 tabs principales
4. **DataTable** con filtros avanzados
5. **Sistema de roles** (consulta/registro)

#### **COMANDOS VERIFICACIÓN:**
```bash
# Verificar BD:
php artisan tinker
>>> \DB::table('comunas_biobio')->count();  // 54
>>> \App\Models\User::count();               // 2
>>> \Schema::hasTable('planos');             // true

# Servidor:
php artisan serve
# http://127.0.0.1:8000
```

### 📊 **PROGRESO GENERAL:**
- ✅ **FASE 1**: Laravel + AdminLTE + Auth (100%)
- ✅ **FASE 2**: Base de Datos + Seeders (100%)
- ⏳ **FASE 3**: Backend + Frontend (0%)
- ⏳ **FASE 4**: Importadores + Avanzado (0%)

**📌 LISTO PARA DESARROLLO DE INTERFAZ TAB 1**

---

## 🎯 **ESTADO ACTUAL - FASE 3 PARCIAL COMPLETADA**
**Fecha:** 2025-09-29
**Estado:** ✅ TAB 1 OPERACIONAL + XAMPP CONFIGURADO

### ✅ **TAB 1 COMPLETADO:**
- ✅ **DataTable** con filtros Excel-like (4 filas)
- ✅ **Badge inteligente:** "X planos encontrados con Y folios"
- ✅ **Búsqueda global** + filtros específicos
- ✅ **Control de roles:** registro/consulta
- ✅ **Filas expandibles** (pendiente implementar)
- ✅ **Botón EDITAR** funcional

### ✅ **XAMPP CONFIGURADO:**
- ✅ **Puerto 8080** para evitar conflictos
- ✅ **MySQL** con permisos corregidos
- ✅ **URL:** http://localhost:8080/libro_planos/public
- ✅ **Credenciales:** alfonso.norambuena@biobio.cl / alfonso123

### 🔧 **PENDIENTES TAB 1:**
**⏳ ALTA PRIORIDAD:**
1. **AGREGAR/QUITAR FOLIOS** en planos existentes
   - Modal para agregar folios individuales
   - Modal para quitar folios seleccionados
   - Recálculo automático hectáreas/m² del plano

2. **REASIGNAR NÚMERO DE PLANO**
   - Generar siguiente correlativo: 0830329271SR → 0830329272SR
   - Mantener tipo (SR/SU/CR/CU) y código región
   - **REQUIERE:** Sistema control sesión única

3. **FILAS EXPANDIBLES**
   - Mostrar detalle de folios al hacer clic en [+]
   - Formato: └ Folio 123456 | JUAN | PEREZ | GONZALEZ

### 🚨 **DEPENDENCIA CRÍTICA:**
**Sistema Control Sesión Única:**
- Solo 1 usuario puede generar números correlativos
- Tabla: `session_control` (ya creada)
- Necesario para REASIGNAR y TAB 3 (crear planos)

### 📊 **PROGRESO ACTUALIZADO:**
- ✅ **FASE 1:** Laravel + AdminLTE + Auth (100%)
- ✅ **FASE 2:** Base de Datos + Seeders (100%)
- ✅ **FASE 3:** Backend + Frontend (60% - Tab 1 parcial)
- ⏳ **FASE 4:** Tabs 2, 3 + Control Sesión (0%)

**📌 PRÓXIMO: COMPLETAR FUNCIONALIDADES TAB 1**