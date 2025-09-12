# SISTEMA LIBRO DE PLANOS DIGITAL - ESPECIFICACIONES COMPLETAS

## 🎯 OBJETIVO
Desarrollar desde 0 un sistema web para digitalizar y gestionar el libro de planos topográficos de la región del Biobío, Chile. Sistema simple, funcional y bien estructurado.

## 🏗️ ARQUITECTURA TÉCNICA

### **STACK TECNOLÓGICO:**
- **Backend**: Laravel 12
- **Frontend**: Livewire (sin JavaScript complejo)
- **UI Framework**: AdminLTE 3 (mantener diseño actual)
- **Base de Datos**: MySQL con estructura simplificada
- **Procesamiento**: Laravel Excel para importación masiva

### **PRINCIPIOS DE DISEÑO:**
- ✅ **Simplicidad extrema**: Menos código, más funcionalidad
- ✅ **Importación Excel como prioridad #1**
- ✅ **Estructura modular**: Archivos pequeños y mantenibles
- ✅ **Performance optimizado**: Consultas rápidas tipo Excel

## 🗄️ BASE DE DATOS SIMPLIFICADA

### **TABLA PRINCIPAL: `planos`**
```sql
CREATE TABLE planos (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- IDENTIFICACIÓN ÚNICA
    numero_plano VARCHAR(20) UNIQUE NOT NULL, -- PL-08-00001-SR-2025
    
    -- DATOS DEL EXPEDIENTE
    folio VARCHAR(50) NOT NULL,
    codigo_region VARCHAR(2) DEFAULT '08',
    codigo_comuna VARCHAR(3) NOT NULL,
    numero_correlativo INT NOT NULL,
    tipo_saneamiento ENUM('SR','SU','CU','CR') NOT NULL,
    
    -- DATOS PERSONALES
    solicitante VARCHAR(255) NOT NULL,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    
    -- UBICACIÓN
    provincia VARCHAR(100) DEFAULT 'BIOBÍO',
    comuna VARCHAR(100) NOT NULL,
    
    -- INMUEBLE (UNIFICADO)
    tipo_inmueble ENUM('HIJUELA','SITIO') NOT NULL, -- Rural=HIJUELA, Urbano=SITIO
    numero_inmueble INT DEFAULT 1, -- 1,2,3...
    hectareas DECIMAL(10,4) NULL, -- Solo para HIJUELA
    m2 BIGINT NOT NULL, -- Para ambos tipos
    
    -- FECHAS Y RESPONSABLES
    mes VARCHAR(20) NOT NULL,
    ano INT NOT NULL,
    responsable VARCHAR(255),
    tecnico VARCHAR(255),
    proyecto VARCHAR(255),
    
    -- CAMPOS OPCIONALES
    observaciones TEXT,
    archivo VARCHAR(255),
    tubo VARCHAR(100),
    tela VARCHAR(100),
    archivo_digital VARCHAR(500),
    
    -- REFERENCIAS EXTERNAS
    matrix_folio VARCHAR(50), -- Referencia directa al folio MATRIX
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ÍNDICES OPTIMIZADOS
    UNIQUE KEY unique_plano (codigo_region, codigo_comuna, numero_correlativo),
    INDEX idx_folio (folio),
    INDEX idx_tipo (tipo_saneamiento),
    INDEX idx_fecha (mes, ano),
    INDEX idx_responsable (responsable),
    INDEX idx_matriz (matrix_folio)
);
```

### **TABLA IMPORTACIÓN: `matrix_import`**
```sql
CREATE TABLE matrix_import (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(50) UNIQUE NOT NULL,
    nombres VARCHAR(255),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    provincia VARCHAR(100),
    comuna VARCHAR(100),
    tipo_inmueble ENUM('RURAL','URBANO'),
    responsable VARCHAR(255),
    proyecto VARCHAR(255),
    batch_import VARCHAR(50), -- Para identificar lotes de importación
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### **TABLA COMUNAS: `comunas_biobio`** (mantener existente)
```sql
-- Ya existe, mantener estructura actual
```

## 🎨 INTERFAZ DE USUARIO - 4 TABS PRINCIPALES

### **TAB 1: TABLA 📊**

#### **ESTRUCTURA DE DATATABLE:**
```
| CODIGO | CODIGO | N°    | TIPO | FOLIO  | SOLICITANTE | PATERNO | MATERNO | COMUNA      | HIJ | HA   | M²      | SITIO | M²    | FECHA     | AÑO  | EMPRESA | TECNICO |
| REG    | COM    | PLANO |      |        |             |         |         |             |     |      |         |       |       |           |      |         |         |
|--------|--------|-------|------|--------|-------------|---------|---------|-------------|-----|------|---------|-------|-------|-----------|------|---------|---------|
```

#### **FILAS EXPANDIBLES - ALINEACIÓN PERFECTA:**

**Estado COLAPSADO:**
```
| 08     | 301    | 29271 | SR   | 123456 | JUAN PEREZ      | GONZALEZ| LOPEZ  | CONCEPCIÓN  | [+] |      |         |       |       | SEPTIEMBRE| 2025 | CARLOS  | FELIPE  |
```

**Estado EXPANDIDO:**
```
| 08     | 301    | 29271 | SR   | 123456 | JUAN PEREZ      | GONZALEZ| LOPEZ  | CONCEPCIÓN  | [−] |      |         |       |       | SEPTIEMBRE| 2025 | CARLOS  | FELIPE  |
│        │        │       │      │        │                 │         │        │             │  1  │ 2,50 │ 25000   │       │       │           │      │         │         │
│        │        │       │      │        │                 │         │        │             │  2  │ 1,75 │ 17500   │       │       │           │      │         │         │
│        │        │       │      │        │                 │         │        │             │  3  │ 3,25 │ 32500   │       │       │           │      │         │         │
```

#### **FUNCIONALIDADES:**
- ✅ **16 filtros avanzados** tipo Excel
- ✅ **Búsqueda global** instantánea
- ✅ **Ordenamiento** por cualquier columna
- ✅ **Filas expandibles** con alineación perfecta
- ✅ **Contador dinámico** de registros
- ✅ **Exportación** a Excel

### **TAB 2: IMPORTAR 📥**

#### **FUNCIONALIDADES:**
- ✅ **Upload archivos Excel** (.xlsx, .xls)
- ✅ **Drag & Drop** para facilidad de uso
- ✅ **Validación** de formato y estructura
- ✅ **Importación masiva** desde MATRIX
- ✅ **Progress bar** con estado en tiempo real
- ✅ **Reporte detallado** de errores y éxitos
- ✅ **Manejo de duplicados** inteligente

#### **PROCESO DE IMPORTACIÓN:**
1. Usuario sube archivo Excel
2. Sistema valida estructura y datos
3. Mapeo automático de columnas
4. Importación batch con progress
5. Reporte final con estadísticas

### **TAB 3: AGREGAR ➕**

#### **SELECTOR DE MODO (3 opciones):**
- 📄 **1 Folio**: Formulario individual optimizado
- 📋 **Múltiples Folios (2-10)**: Formulario para varios folios
- 🏭 **Modo Masivo (11+)**: Para procesamiento masivo

#### **MODO 1 FOLIO - OPCIONES:**

##### **A. CON AUTOCOMPLETADO (desde MATRIX):**
- ✅ **Buscar folio**: Input con búsqueda en tiempo real
- ✅ **Auto-rellenar**: Datos desde `matrix_import`
- ✅ **Validación**: Verificar duplicados
- ✅ **Datos rellenados**: solicitante, apellidos, comuna, responsable, proyecto

##### **B. MODO MANUAL (datos nuevos):**
- ✅ **Formulario completo**: Usuario ingresa todos los datos
- ✅ **Validaciones**: Campos requeridos según tipo
- ✅ **Auto-numeración**: Generación automática de correlativo

#### **CASOS ESPECIALES:**
- 🏛️ **FISCO DE CHILE**: Solo nombre en solicitante, paterno/materno vacíos
- 👤 **Solicitantes normales**: Nombre + apellido paterno + materno

#### **INMUEBLES DINÁMICOS:**
- 🌾 **Rural (SR/CR)**: Hijuelas con número + hectáreas + m²
- 🏘️ **Urbano (SU/CU)**: Sitios con número + m²
- 🔄 **Conversión automática**: hectáreas ↔ metros cuadrados
- ➕ **Múltiples inmuebles**: Soporte para N hijuelas/sitios por plano

### **TAB 4: REPORTES 📈**

#### **ESTADÍSTICAS BÁSICAS:**
- 📊 **Por responsable**: Cantidad de planos por empresa/técnico
- 📅 **Por mes/año**: Distribución temporal
- 📋 **Por tipo**: SR, SU, CU, CR
- 🏘️ **Por comuna**: Estadísticas geográficas
- 📐 **Superficies**: Totales de hectáreas y m²

## 🎯 DATOS CRÍTICOS DEL SISTEMA

### **COLUMNAS EXACTAS DE LA TABLA:**
```
1.  CODIGO_REG     → 08 (fijo para Biobío)
2.  CODIGO_COM     → 301, 203, etc.
3.  N_PLANO        → 29270 (correlativo)
4.  TIPO           → SR, SU, CU, CR
5.  FOLIO          → 267319
6.  SOLICITANTE    → FISCO LOTE D CAMINO AL CEMENTERIO / JUAN PEREZ
7.  PATERNO        → GONZALEZ (vacío para FISCO)
8.  MATERNO        → LOPEZ (vacío para FISCO)
9.  COMUNA         → LOS ÁNGELES
10. HIJ            → 1, 2, 3... (solo rural)
11. HA             → 2,50 (hectáreas, solo rural)
12. M²             → 25000 (metros cuadrados)
13. SITIO          → 1, 2, 3... (solo urbano)
14. M²             → 150 (metros cuadrados sitio)
15. FECHA          → DICIEMBRE
16. AÑO            → 2024
17. EMPRESA        → CARLOS MARTINEZ
18. TECNICO        → FELIPE SAN MARTIN
```

### **TIPOS DE SANEAMIENTO:**
- **SR**: Saneamiento Rural (hijuelas con hectáreas)
- **SU**: Saneamiento Urbano (sitios con m²)
- **CU**: Fiscal Urbano (FISCO, sitios con m²)
- **CR**: Fiscal Rural (FISCO, hijuelas con hectáreas)

### **REGLAS DE NEGOCIO:**

#### **NUMERACIÓN DE PLANOS:**
- Formato: `PL-08-XXXXX-TT-YYYY`
- 08: Región Biobío (fijo)
- XXXXX: Correlativo por comuna
- TT: Tipo saneamiento
- YYYY: Año

#### **CASOS FISCALES:**
- Solicitante: "FISCO DE CHILE" o descripción del lote
- Apellidos: Siempre vacíos
- Sin folio en algunos casos

#### **INMUEBLES:**
- **Rural**: Una fila por hijuela (puede haber múltiples)
- **Urbano**: Una fila por sitio (puede haber múltiples)
- **Agrupación**: Por `numero_plano` para mostrar como expandible

## 🚀 REQUERIMIENTOS TÉCNICOS

### **PERFORMANCE:**
- ✅ **Consultas optimizadas**: Índices en campos de filtro
- ✅ **Paginación eficiente**: Para tablas grandes
- ✅ **Búsqueda rápida**: Tipo Excel sin demoras
- ✅ **Carga bajo demanda**: Para filas expandibles

### **USABILIDAD:**
- ✅ **Interfaz intuitiva**: AdminLTE familiar
- ✅ **Responsive**: Funcional en tablets y desktop
- ✅ **Validaciones claras**: Mensajes de error específicos
- ✅ **Feedback visual**: Loading states y confirmaciones

### **MANTENIBILIDAD:**
- ✅ **Código limpio**: Clases pequeñas y específicas
- ✅ **Arquitectura modular**: Componentes Livewire independientes
- ✅ **Documentación**: Comentarios y README actualizado
- ✅ **Testing**: Pruebas básicas para funciones críticas

## 📋 PLAN DE DESARROLLO

### **FASE 1: ESTRUCTURA BASE**
1. Crear migración de BD simplificada
2. Modelo Plano con relaciones básicas
3. Layout AdminLTE limpio
4. Sistema de tabs principal

### **FASE 2: IMPORTACIÓN (PRIORIDAD #1)**
1. Componente Livewire para upload
2. Procesamiento Excel con Laravel Excel
3. Validaciones y manejo de errores
4. Progress bar y reportes

### **FASE 3: DATATABLE**
1. Componente tabla con Livewire
2. Sistema de filtros avanzados
3. Filas expandibles con alineación perfecta
4. Búsqueda global optimizada

### **FASE 4: FORMULARIOS**
1. Formulario 1 folio con autocompletado
2. Modo manual para datos nuevos
3. Validaciones según tipo (fiscal vs normal)
4. Manejo de múltiples hijuelas/sitios

### **FASE 5: REPORTES**
1. Estadísticas básicas
2. Gráficos simples
3. Exportación de reportes

## ✅ CRITERIOS DE ÉXITO

### **FUNCIONAL:**
- ✅ Importación Excel masiva sin errores
- ✅ Tabla con filas expandibles perfectamente alineadas
- ✅ Formularios simples e intuitivos
- ✅ Búsqueda tipo Excel instantánea

### **TÉCNICO:**
- ✅ Código base < 50% del sistema actual
- ✅ Sin archivos gigantes (max 200 líneas por archivo)
- ✅ Performance > 90% mejor que sistema actual
- ✅ Mantenibilidad alta (componentes independientes)

### **USUARIO:**
- ✅ Misma UI familiar (AdminLTE)
- ✅ Funcionalidades principales intactas
- ✅ Velocidad notablemente superior
- ✅ Facilidad de uso mejorada

---

**NOTA**: Este prompt debe usarse para desarrollar un sistema completamente nuevo, manteniendo solo la base de datos actual de comunas y matrix_reference como referencia.