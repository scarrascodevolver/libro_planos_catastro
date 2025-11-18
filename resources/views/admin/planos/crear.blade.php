@extends('layouts.admin')

@section('title', 'Agregar Planos')

@section('page-title', 'Crear Nuevo Plano')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Agregar Planos</li>
@endsection

@push('styles')
<style>
/* Cards de selección de cantidad de folios */
.card-seleccion {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
}

.card-seleccion:hover {
    border-color: #007bff;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
}

.card-seleccion.selected {
    border-color: #007bff;
    background-color: #e7f3ff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.card-seleccion.selected .icon-cantidad i {
    transform: scale(1.1);
}

.card-seleccion.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.icon-cantidad {
    transition: transform 0.3s ease;
}

.card-seleccion h6 {
    font-size: 0.9rem;
    color: #343a40;
}

.card-seleccion.selected h6 {
    color: #007bff;
}
</style>
@endpush

@section('content')

<!-- PASO 1: Numeración Correlativa (SIEMPRE VISIBLE) -->
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            <i class="fas fa-hashtag"></i>
            Numeración Correlativa
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-icon">
                        <i class="fas fa-arrow-left text-info"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Último Correlativo</span>
                        <span class="info-box-number" id="ultimo-correlativo-display">---</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-icon">
                        <i class="fas fa-arrow-right text-success"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Próximo a Crear</span>
                        <span class="info-box-number" id="proximo-correlativo-display">---</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PASO 2: Cantidad de Folios -->
<div class="card" id="card-cantidad">
    <div class="card-header bg-info">
        <h3 class="card-title">
            <i class="fas fa-list-ol"></i>
            Paso 1: ¿Cuántos folios tendrá el plano?
        </h3>
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-4 mb-2">
                <div class="card card-seleccion h-100" data-value="1" id="btn-cantidad-1">
                    <div class="card-body text-center py-3 px-2">
                        <div class="icon-cantidad mb-2">
                            <i class="fas fa-file-alt fa-2x" style="color: #17a2b8;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">1 FOLIO</h6>
                        <small class="text-muted">Simple</small>
                    </div>
                </div>
                <input class="cantidad-radio d-none" type="radio" name="cantidad_folios" id="cantidad_1" value="1">
            </div>
            <div class="col-md-3 col-sm-4 mb-2">
                <div class="card card-seleccion h-100" data-value="multiple" id="btn-cantidad-multiple">
                    <div class="card-body text-center py-3 px-2">
                        <div class="icon-cantidad mb-2">
                            <i class="fas fa-copy fa-2x" style="color: #28a745;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">2-10 FOLIOS</h6>
                        <small class="text-muted">Múltiple</small>
                    </div>
                </div>
                <input class="cantidad-radio d-none" type="radio" name="cantidad_folios" id="cantidad_multiple" value="multiple">
            </div>
            <div class="col-md-3 col-sm-4 mb-2">
                <div class="card card-seleccion h-100" data-value="masivo" id="btn-cantidad-masivo">
                    <div class="card-body text-center py-3 px-2">
                        <div class="icon-cantidad mb-2">
                            <i class="fas fa-layer-group fa-2x" style="color: #fd7e14;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">11+ FOLIOS</h6>
                        <small class="text-muted">Masivo</small>
                    </div>
                </div>
                <input class="cantidad-radio d-none" type="radio" name="cantidad_folios" id="cantidad_masiva" value="masivo">
            </div>
        </div>
    </div>
</div>

<!-- PASO 3: Origen (Matrix o Manual) -->
<div class="card" id="card-origen" style="display: none;">
    <div class="card-header bg-warning">
        <h3 class="card-title">
            <i class="fas fa-route"></i>
            Paso 2: ¿De dónde vienen los folios?
        </h3>
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="card card-seleccion card-origen h-100" data-value="matrix" id="btn-origen-matrix">
                    <div class="card-body text-center py-3 px-2">
                        <div class="icon-cantidad mb-2">
                            <i class="fas fa-database fa-2x" style="color: #007bff;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">MATRIX</h6>
                        <small class="text-muted">Buscar en BD</small>
                    </div>
                </div>
                <input class="origen-radio d-none" type="radio" name="origen_folios" id="origen_matrix" value="matrix">
            </div>
            <div class="col-md-4 col-sm-6 mb-2">
                <div class="card card-seleccion card-origen h-100" data-value="manual" id="btn-origen-manual">
                    <div class="card-body text-center py-3 px-2">
                        <div class="icon-cantidad mb-2">
                            <i class="fas fa-edit fa-2x" style="color: #6c757d;"></i>
                        </div>
                        <h6 class="mb-1 font-weight-bold">MANUAL</h6>
                        <small class="text-muted">Ingreso libre</small>
                    </div>
                </div>
                <input class="origen-radio d-none" type="radio" name="origen_folios" id="origen_manual" value="manual">
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary btn-sm" id="btn-volver-cantidad">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    </div>
</div>

<!-- PASO 4: Configuración Manual (solo si es Manual) -->
<div class="card" id="card-config-manual" style="display: none;">
    <div class="card-header bg-secondary">
        <h3 class="card-title">
            <i class="fas fa-cog"></i>
            Paso 3: Configuración del Plano Manual
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><i class="fas fa-map"></i> ¿Rural o Urbano?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="ubicacion_manual" id="ubicacion_rural" value="R">
                        <label class="form-check-label" for="ubicacion_rural">
                            <strong>Rural</strong> - HIJUELAS con Hectáreas + M²
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="ubicacion_manual" id="ubicacion_urbano" value="U">
                        <label class="form-check-label" for="ubicacion_urbano">
                            <strong>Urbano</strong> - SITIOS solo con M²
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><i class="fas fa-building"></i> ¿Saneamiento o Fiscal?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_manual" id="tipo_saneamiento" value="S">
                        <label class="form-check-label" for="tipo_saneamiento">
                            <strong>Saneamiento</strong> (S)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_manual" id="tipo_fiscal" value="C">
                        <label class="form-check-label" for="tipo_fiscal">
                            <strong>Fiscal / Catastro</strong> (C)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previsualización tipo plano (oculto, info ya se muestra en display superior) -->
        <div id="preview-tipo-manual" style="display: none;">
            <span id="preview-tipo-text"></span>
            <span id="preview-tipo-desc"></span>
        </div>

        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary" id="btn-volver-origen">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-primary" id="btn-continuar-config" disabled>
                Continuar <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- PASO 5: Ingreso de Folios -->
<div class="card" id="card-folios" style="display: none;">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-edit"></i>
            <span id="titulo-folios">Paso 4: Ingreso de Folios</span>
        </h3>
        <div class="card-tools">
            <span class="badge badge-success" id="badge-folios-count">0 folios</span>
        </div>
    </div>
    <div class="card-body">
        <div id="contenedor-folios">
            <!-- Aquí se generará dinámicamente el formulario según origen -->
        </div>

        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary" id="btn-volver-folios">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-success" id="btn-continuar-confirmacion" disabled>
                Continuar <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- PASO 6: Confirmación -->
<div class="card" id="card-confirmacion" style="display: none;">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            <i class="fas fa-check-circle"></i>
            Paso Final: Confirmar Creación del Plano
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <h4><i class="fas fa-info-circle"></i> Resumen del Plano</h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Número de Plano:</th>
                        <td><h4 id="confirm-numero">---</h4></td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td id="confirm-tipo">---</td>
                    </tr>
                    <tr>
                        <th>Comuna:</th>
                        <td id="confirm-comuna">---</td>
                    </tr>
                    <tr>
                        <th>Responsable:</th>
                        <td id="confirm-responsable">---</td>
                    </tr>
                    <tr>
                        <th>Proyecto:</th>
                        <td id="confirm-proyecto">---</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Total Folios:</th>
                        <td id="confirm-total-folios">---</td>
                    </tr>
                    <tr>
                        <th>Total Hectáreas:</th>
                        <td id="confirm-total-ha">---</td>
                    </tr>
                    <tr>
                        <th>Total M²:</th>
                        <td id="confirm-total-m2">---</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Folios Incluidos</h5>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <div id="confirm-lista-folios">
                    <!-- Lista de folios -->
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary btn-lg" id="btn-volver-confirmacion">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-success btn-lg" id="btn-crear-plano">
                <i class="fas fa-save"></i> Crear Plano
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Variables globales
let wizardData = {
    cantidadFolios: null,
    cantidadTipo: null, // '1', 'multiple', 'masivo'
    origenFolios: null, // 'matrix', 'manual'
    ubicacionManual: null, // 'R', 'U'
    tipoManual: null, // 'S', 'C'
    tipoPlano: null, // 'SR', 'SU', 'CR', 'CU'
    folios: [],
    ultimoCorrelativo: null,
    proximoCorrelativo: null
};

// Comunas del Biobío para select
const comunasBiobio = @json($comunas ?? []);

// Funciones de validación numérica
function validarNumeroEntero(event) {
    const charCode = event.which ? event.which : event.keyCode;
    // Permitir solo números (0-9)
    if (charCode < 48 || charCode > 57) {
        return false;
    }
    return true;
}

function validarNumeroDecimal(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const valor = event.target.value;

    // Permitir números (0-9)
    if (charCode >= 48 && charCode <= 57) {
        return true;
    }

    // Permitir coma decimal (solo una)
    if (charCode === 44 && valor.indexOf(',') === -1) {
        return true;
    }

    return false;
}

$(document).ready(function() {
    cargarNumeracionCorrelativa();
    initWizardListeners();
});

// =====================================================
// CARGAR NUMERACIÓN CORRELATIVA
// =====================================================
function cargarNumeracionCorrelativa() {
    console.log('🔍 Cargando numeración correlativa...');
    $.ajax({
        url: '{{ route("planos.crear.ultimo-correlativo") }}',
        method: 'GET',
        success: function(response) {
            console.log('✅ Respuesta recibida:', response);

            wizardData.ultimoCorrelativo = response.ultimo;
            wizardData.proximoCorrelativo = response.proximo;

            if (response.hayDatos) {
                // HAY DATOS: Mostrar normalmente
                $('#ultimo-correlativo-display').text(response.ultimo);
                $('#proximo-correlativo-display').text(response.proximo);

                // Habilitar el wizard
                $('.card-seleccion').removeClass('disabled');
            } else {
                // NO HAY DATOS: Mostrar mensaje y bloquear
                $('#ultimo-correlativo-display').html('<span class="text-muted">(Ninguno)</span>');
                $('#proximo-correlativo-display').html('<span class="text-danger">(No disponible)</span>');

                // Deshabilitar el wizard
                $('.card-seleccion').addClass('disabled');

                // Mostrar alerta
                $('#card-cantidad').prepend(`
                    <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                        <h5><i class="fas fa-exclamation-triangle"></i> Atención</h5>
                        <p><strong>${response.mensaje}</strong></p>
                        <p class="mb-0">Por favor, vaya a <a href="{{ route('planos.importacion.index') }}" class="alert-link">Importación Masiva</a> para cargar los planos históricos primero.</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar correlativo:', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText
            });

            $('#ultimo-correlativo-display').text('Error al cargar');
            $('#proximo-correlativo-display').text('Error al cargar');
            $('.card-seleccion').addClass('disabled');
        }
    });
}

// =====================================================
// LISTENERS DEL WIZARD
// =====================================================
function initWizardListeners() {
    // PASO 2: Cantidad de folios - Click en cards de selección
    $('.card-seleccion').off('click').on('click', function() {
        if ($(this).hasClass('disabled')) return;

        // Quitar selección anterior y agregar a la nueva
        $('.card-seleccion').removeClass('selected');
        $(this).addClass('selected');

        // Obtener valor y marcar radio oculto (convertir a string para comparaciones)
        const value = String($(this).data('value'));
        $('input.cantidad-radio[value="' + value + '"]').prop('checked', true);

        wizardData.cantidadTipo = value;

        if (wizardData.cantidadTipo === '1') {
            wizardData.cantidadFolios = 1;
        } else if (wizardData.cantidadTipo === 'multiple') {
            // Preguntar cantidad específica después
        } else if (wizardData.cantidadTipo === 'masivo') {
            // Validar cantidad después
        }

        // Pequeño delay para mostrar la selección antes de avanzar
        setTimeout(function() {
            $('#card-cantidad').hide();
            $('#card-origen').show();
        }, 200);
    });

    // Volver a cantidad
    $('#btn-volver-cantidad').off('click').on('click', function() {
        $('#card-origen').hide();
        $('#card-cantidad').show();
        // Limpiar selección visual y radios
        $('.card-seleccion').removeClass('selected');
        $('.card-origen').removeClass('selected');
        $('.cantidad-radio').prop('checked', false);
        $('.origen-radio').prop('checked', false);
    });

    // PASO 3: Origen - Click en cards de selección
    $('.card-origen').off('click').on('click', function() {
        // Quitar selección anterior y agregar a la nueva
        $('.card-origen').removeClass('selected');
        $(this).addClass('selected');

        // Obtener valor y marcar radio oculto
        const value = $(this).data('value');
        $('input.origen-radio[value="' + value + '"]').prop('checked', true);

        wizardData.origenFolios = value;

        // Pequeño delay para mostrar la selección antes de avanzar
        setTimeout(function() {
            $('#card-origen').hide();

            if (wizardData.origenFolios === 'manual') {
                // Ir a configuración manual
                $('#card-config-manual').show();
            } else {
                // Ir directo a búsqueda Matrix
                mostrarFormularioFolios();
            }
        }, 200);
    });

    // Volver a origen
    $('#btn-volver-origen').off('click').on('click', function() {
        $('#card-config-manual').hide();
        $('#card-origen').show();
        // Limpiar selección de origen
        $('.origen-radio').prop('checked', false);
    });

    // PASO 4: Configuración Manual
    $('input[name="ubicacion_manual"], input[name="tipo_manual"]').off('change').on('change', function() {
        actualizarPreviewTipoManual();
    });

    $('#btn-continuar-config').off('click').on('click', function() {
        wizardData.ubicacionManual = $('input[name="ubicacion_manual"]:checked').val();
        wizardData.tipoManual = $('input[name="tipo_manual"]:checked').val();
        wizardData.tipoPlano = wizardData.tipoManual + wizardData.ubicacionManual;

        $('#card-config-manual').hide();
        mostrarFormularioFolios();
    });

    // PASO 5: Folios
    $('#btn-volver-folios').off('click').on('click', volverDesdeFolios);
    $('#btn-continuar-confirmacion').off('click').on('click', mostrarConfirmacion);

    // PASO 6: Confirmación
    $('#btn-volver-confirmacion').off('click').on('click', function() {
        $('#card-confirmacion').hide();
        $('#card-folios').show();
    });

    $('#btn-crear-plano').off('click').on('click', crearPlano);
}

// =====================================================
// ACTUALIZAR PREVIEW TIPO MANUAL
// =====================================================
function actualizarPreviewTipoManual() {
    const ubicacion = $('input[name="ubicacion_manual"]:checked').val();
    const tipo = $('input[name="tipo_manual"]:checked').val();

    if (ubicacion && tipo) {
        const tipoPlano = tipo + ubicacion;
        const descripciones = {
            'SR': 'Saneamiento Rural - HIJUELAS',
            'SU': 'Saneamiento Urbano - SITIOS',
            'CR': 'Fiscal Rural / Catastro Rural - HIJUELAS',
            'CU': 'Fiscal Urbano / Catastro Urbano - SITIOS'
        };

        $('#btn-continuar-config').prop('disabled', false);

        // Actualizar display superior con número parcial (sin comuna aún)
        actualizarDisplayCorrelativo(null, tipoPlano);
    } else {
        $('#btn-continuar-config').prop('disabled', true);
    }
}

// =====================================================
// ACTUALIZAR DISPLAY CORRELATIVO SUPERIOR
// =====================================================
function actualizarDisplayCorrelativo(codigoComuna, tipoPlano) {
    if (!wizardData.proximoCorrelativo) return;

    const tipo = tipoPlano || wizardData.tipoPlano || '';
    const comuna = codigoComuna || '---';

    const numeroCompleto = '08' + comuna + wizardData.proximoCorrelativo + tipo;
    $('#proximo-correlativo-display').text(numeroCompleto);
}

// =====================================================
// MOSTRAR FORMULARIO DE FOLIOS
// =====================================================
function mostrarFormularioFolios() {
    $('#card-folios').show();

    if (wizardData.origenFolios === 'matrix') {
        generarFormularioMatrix();
    } else {
        generarFormularioManual();
    }
}

function volverDesdeFolios() {
    $('#card-folios').hide();

    if (wizardData.origenFolios === 'manual') {
        $('#card-config-manual').show();
    } else {
        $('#card-origen').show();
    }
}

// =====================================================
// GENERAR FORMULARIO MATRIX
// =====================================================
function generarFormularioMatrix() {
    let html = '<div class="alert alert-info">';
    html += '<i class="fas fa-database"></i> ';
    html += '<strong>Búsqueda en Matrix:</strong> Escribe el folio y presiona Tab para buscar automáticamente.';
    html += '</div>';

    if (wizardData.cantidadTipo === '1') {
        // 1 FOLIO SIMPLE
        html += generarInputMatrix(0);
    } else if (wizardData.cantidadTipo === 'multiple') {
        // 2-10 FOLIOS: Preguntar cantidad exacta primero
        html += '<div class="form-group">';
        html += '<label>¿Cuántos folios exactamente? (2-10)</label>';
        html += '<select class="form-control" id="cantidad-exacta-multiple">';
        html += '<option value="">Seleccionar...</option>';
        for (let i = 2; i <= 10; i++) {
            html += `<option value="${i}">${i} folios</option>`;
        }
        html += '</select>';
        html += '</div>';
        html += '<div id="inputs-multiples"></div>';
    } else if (wizardData.cantidadTipo === 'masivo') {
        // 11-150 FOLIOS MASIVOS
        html += '<div class="form-group">';
        html += '<label>Lista de Folios (uno por línea o separados por comas)</label>';
        html += '<textarea class="form-control" id="folios-masivos" rows="10" ';
        html += 'placeholder="123456&#10;789012&#10;345678&#10;..."></textarea>';
        html += '<small class="text-muted">Pegue aquí la lista de folios (11-150)</small>';
        html += '</div>';
        html += '<button type="button" class="btn btn-info" id="btn-procesar-masivos">';
        html += '<i class="fas fa-search"></i> Procesar Lista</button>';
        html += '<div id="resultado-masivos" class="mt-3"></div>';
    }

    $('#contenedor-folios').html(html);
    $('#titulo-folios').text('Paso 4: Buscar Folios en Matrix');

    // Event listeners
    if (wizardData.cantidadTipo === '1') {
        // Agregar listener para 1 FOLIO
        $('.folio-input').on('keydown', function(e) {
            if (e.key === 'Tab' || e.key === 'Enter') {
                e.preventDefault();
                const index = $(this).data('index');
                buscarFolioMatrix(index);
            }
        });
    } else if (wizardData.cantidadTipo === 'multiple') {
        $('#cantidad-exacta-multiple').on('change', function() {
            const cantidad = parseInt($(this).val());
            if (cantidad) {
                wizardData.cantidadFolios = cantidad;
                generarInputsMultiples(cantidad);
            }
        });
    } else if (wizardData.cantidadTipo === 'masivo') {
        $('#btn-procesar-masivos').on('click', procesarFoliosMasivos);
    }
}

function generarInputMatrix(index) {
    let html = '<div class="card mb-3 folio-matrix-card" data-index="' + index + '">';
    html += '<div class="card-body">';

    // Input de búsqueda
    html += '<div class="form-group">';
    html += '<label>Folio #' + (index + 1) + ' <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control folio-input" data-index="' + index + '" ';
    html += 'placeholder="Escribe y presiona Tab para buscar" required>';
    html += '<small class="text-muted">Presiona <kbd>Tab</kbd> o <kbd>Enter</kbd> para buscar en Matrix</small>';
    html += '</div>';

    // Contenedor de resultados (se llena al encontrar)
    html += '<div class="resultado-folio" id="resultado-' + index + '"></div>';

    html += '</div>';
    html += '</div>';
    return html;
}

function generarInputsMultiples(cantidad) {
    let html = '';
    for (let i = 0; i < cantidad; i++) {
        html += generarInputMatrix(i);
    }
    $('#inputs-multiples').html(html);

    // Agregar listeners de búsqueda
    $('.folio-input').on('keydown', function(e) {
        if (e.key === 'Tab' || e.key === 'Enter') {
            e.preventDefault();
            const index = $(this).data('index');
            buscarFolioMatrix(index);
        }
    });
}

function buscarFolioMatrix(index) {
    const folio = $(`.folio-input[data-index="${index}"]`).val().trim();
    const $resultado = $(`#resultado-${index}`);

    if (!folio) {
        $resultado.html('<small class="text-danger">Ingrese un número de folio</small>');
        return;
    }

    $resultado.html('<i class="fas fa-spinner fa-spin"></i> Buscando...');

    $.ajax({
        url: '{{ route("planos.crear.buscar-folio") }}',
        method: 'POST',
        data: {
            folio: folio,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.encontrado) {
                // Si el folio YA ESTÁ USADO - mostrar error y NO auto-completar
                if (response.yaUsado) {
                    const planoInfo = response.planoExistente;
                    const tipoDescripciones = {
                        'SR': 'Saneamiento Rural',
                        'SU': 'Saneamiento Urbano',
                        'CR': 'Fiscal/Catastro Rural',
                        'CU': 'Fiscal/Catastro Urbano'
                    };
                    const tipoDesc = tipoDescripciones[planoInfo.tipo] || planoInfo.tipo;

                    $resultado.html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Folio ya usado</strong><br>
                            <small>Este folio ya está registrado en:</small><br>
                            <strong>Plano:</strong> ${planoInfo.numero}<br>
                            <strong>Solicitante:</strong> <span class="text-dark">${planoInfo.solicitante}</span><br>
                            <strong>Tipo:</strong> ${tipoDesc} (${planoInfo.tipo})<br>
                            <strong>Comuna:</strong> ${planoInfo.comuna}<br>
                            <strong>Responsable:</strong> ${planoInfo.responsable}
                        </div>
                    `);

                    // NO agregar a wizardData
                    wizardData.folios[index] = null;

                    // Mostrar alerta adicional
                    Swal.fire({
                        icon: 'error',
                        title: 'Folio ya usado',
                        html: `
                            <p class="mb-3"><strong>${response.message}</strong></p>
                            <hr>
                            <div class="text-left">
                                <p class="mb-2"><i class="fas fa-user text-primary"></i> <strong>Solicitante:</strong> ${planoInfo.solicitante}</p>
                                <p class="mb-2"><i class="fas fa-file-alt text-info"></i> <strong>Tipo:</strong> ${tipoDesc} (${planoInfo.tipo})</p>
                                <p class="mb-2"><i class="fas fa-map-marker-alt text-success"></i> <strong>Comuna:</strong> ${planoInfo.comuna}</p>
                                <p class="mb-0"><i class="fas fa-user-tie text-secondary"></i> <strong>Responsable:</strong> ${planoInfo.responsable}</p>
                            </div>
                        `,
                        confirmButtonText: 'Entendido'
                    });

                    return; // Salir, NO continuar con auto-completado
                }

                // Folio NO USADO - auto-completar normalmente
                // Normalizar tipo_inmueble de Matrix a HIJUELA/SITIO
                const datos = response.datos;
                const tipoOriginal = (datos.tipo_inmueble || '').toUpperCase();
                if (tipoOriginal.includes('HIJUELA') || tipoOriginal.includes('RURAL')) {
                    datos.tipo_inmueble = 'HIJUELA';
                } else {
                    datos.tipo_inmueble = 'SITIO';
                }

                mostrarResultadoMatrix(index, datos);
                wizardData.folios[index] = datos;
                validarFoliosMatrix();
            } else {
                $resultado.html('<div class="alert alert-danger"><i class="fas fa-times"></i> Folio no encontrado en Matrix</div>');
            }
        },
        error: function() {
            $resultado.html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error al buscar</div>');
        }
    });
}

function mostrarResultadoMatrix(index, data) {
    const esRural = data.tipo_inmueble === 'HIJUELA';
    const tipoLabel = esRural ? 'Hijuela' : 'Sitio';

    // Mensaje pequeño de éxito
    let html = '<div class="alert alert-success py-2 mb-3">';
    html += '<i class="fas fa-check-circle"></i> <strong>Encontrado:</strong> ' + tipoLabel + ' - Puedes modificar los datos';
    html += '</div>';

    // Formulario editable (sin card extra)
    html += '<h6 class="text-primary mb-3">Datos del ' + tipoLabel + '</h6>';

    // FILA 1: Datos personales
    html += '<div class="row">';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Solicitante <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control solicitante-matrix" data-index="' + index + '" value="' + (data.solicitante || '') + '" required>';
    html += '</div>';
    html += '</div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Ap. Paterno</label>';
    html += '<input type="text" class="form-control ap-paterno-matrix" data-index="' + index + '" value="' + (data.apellido_paterno || '') + '">';
    html += '</div>';
    html += '</div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Ap. Materno</label>';
    html += '<input type="text" class="form-control ap-materno-matrix" data-index="' + index + '" value="' + (data.apellido_materno || '') + '">';
    html += '</div>';
    html += '</div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>N° ' + tipoLabel + '</label>';
    html += '<input type="number" class="form-control numero-inmueble-matrix" data-index="' + index + '" value="' + (index + 1) + '" min="1">';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // FILA 2: Medidas
    html += '<div class="row">';
    if (esRural) {
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Hectáreas</label>';
        html += '<input type="text" class="form-control hectareas-matrix" data-index="' + index + '" placeholder="0,0000" inputmode="decimal" onkeypress="return validarNumeroDecimal(event)">';
        html += '<small class="text-muted">Formato: 2,5000</small>';
        html += '</div>';
        html += '</div>';

        html += '<div class="col-md-4">';
    } else {
        html += '<div class="col-md-8">';
    }

    html += '<div class="form-group">';
    html += '<label>M² <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control m2-matrix" data-index="' + index + '" placeholder="0" required inputmode="numeric" onkeypress="return validarNumeroEntero(event)">';
    html += '<small class="text-muted">Solo números enteros</small>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // FILA 3: Datos del plano (solo en primer folio)
    if (index === 0) {
        html += '<hr><h6>Datos del Plano (aplican a todos los folios)</h6>';
        html += '<div class="row">';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Comuna <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control comuna-matrix" data-index="' + index + '" value="' + (data.comuna || '') + '" readonly>';
        html += '<small class="text-muted">Desde Matrix</small>';
        html += '</div>';
        html += '</div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Responsable <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control responsable-matrix" data-index="' + index + '" value="' + (data.responsable || '') + '" required>';
        html += '</div>';
        html += '</div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Proyecto <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control proyecto-matrix" data-index="' + index + '" value="' + (data.proyecto || '') + '" required>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }

    $(`#resultado-${index}`).html(html);

    // Agregar conversión hectáreas ↔ m² si es rural
    if (esRural) {
        attachConversionListenersMatrix(index);
    }
}

function validarFoliosMatrix() {
    // Validar que todos coincidan en comuna, responsable, proyecto, tipo_inmueble
    const foliosValidos = wizardData.folios.filter(f => f !== undefined && f !== null);

    if (foliosValidos.length === 0) return;

    const comunas = [...new Set(foliosValidos.map(f => f.comuna))];
    const responsables = [...new Set(foliosValidos.map(f => f.responsable))];
    const proyectos = [...new Set(foliosValidos.map(f => f.proyecto))];
    const tipos = [...new Set(foliosValidos.map(f => f.tipo_inmueble))];

    let errores = [];
    if (comunas.length > 1) errores.push('Diferentes comunas: ' + comunas.join(', '));
    if (responsables.length > 1) errores.push('Diferentes responsables');
    if (proyectos.length > 1) errores.push('Diferentes proyectos');
    if (tipos.length > 1) errores.push('Mezcla de HIJUELA y SITIO');

    if (errores.length > 0) {
        $('#btn-continuar-confirmacion').prop('disabled', true);
        Swal.fire({
            icon: 'error',
            title: 'Folios incompatibles',
            html: errores.join('<br>')
        });
    } else if (foliosValidos.length === wizardData.cantidadFolios) {
        // Todos los folios encontrados y válidos
        wizardData.tipoPlano = 'S' + (tipos[0] === 'HIJUELA' ? 'R' : 'U');
        $('#btn-continuar-confirmacion').prop('disabled', false);
    }
}

// Recolectar TODOS los datos de folios Matrix (editables)
function recolectarMedidasMatrix() {
    let errores = [];

    wizardData.folios.forEach((folio, index) => {
        if (!folio) return;

        // Leer datos personales (editables)
        const solicitante = $(`.solicitante-matrix[data-index="${index}"]`).val()?.trim();
        const apPaterno = $(`.ap-paterno-matrix[data-index="${index}"]`).val()?.trim();
        const apMaterno = $(`.ap-materno-matrix[data-index="${index}"]`).val()?.trim();

        if (!solicitante) {
            errores.push(`Folio ${folio.folio}: Solicitante es obligatorio`);
            return;
        }

        // Leer medidas
        const numeroInmueble = $(`.numero-inmueble-matrix[data-index="${index}"]`).val();
        const m2Input = $(`.m2-matrix[data-index="${index}"]`).val();

        if (!m2Input) {
            errores.push(`Folio ${folio.folio}: M² es obligatorio`);
            return;
        }

        const m2 = parseFloat(m2Input.replace(/\./g, '').replace(',', '.'));
        if (isNaN(m2) || m2 <= 0) {
            errores.push(`Folio ${folio.folio}: M² inválido`);
            return;
        }

        // Actualizar folio con TODOS los datos editados
        folio.solicitante = solicitante;
        folio.apellido_paterno = apPaterno || null;
        folio.apellido_materno = apMaterno || null;
        folio.numero_inmueble = parseInt(numeroInmueble) || (index + 1);
        folio.m2 = m2;

        // Agregar hectáreas si es rural
        if (folio.tipo_inmueble === 'HIJUELA') {
            const haInput = $(`.hectareas-matrix[data-index="${index}"]`).val();
            if (haInput) {
                folio.hectareas = parseFloat(haInput.replace(/\./g, '').replace(',', '.'));
            }
        }

        // Leer datos del plano (solo en primer folio)
        if (index === 0) {
            const comuna = $(`.comuna-matrix[data-index="${index}"]`).val()?.trim();
            const responsable = $(`.responsable-matrix[data-index="${index}"]`).val()?.trim();
            const proyecto = $(`.proyecto-matrix[data-index="${index}"]`).val()?.trim();

            if (!responsable) {
                errores.push('Responsable es obligatorio');
                return;
            }

            if (!proyecto) {
                errores.push('Proyecto es obligatorio');
                return;
            }

            // Actualizar datos del plano (aplican a todos los folios)
            folio.comuna = comuna;
            folio.responsable = responsable;
            folio.proyecto = proyecto;
        }
    });

    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores.join('<br>')
        });
        return false;
    }

    return true;
}

// Conversión hectáreas ↔ m² para Matrix
function attachConversionListenersMatrix(index) {
    // Hectáreas -> M²
    $(document).on('input', `.hectareas-matrix[data-index="${index}"]`, function() {
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');
        if (valor && !isNaN(valor)) {
            const ha = parseFloat(valor);
            const m2 = ha * 10000;
            $(`.m2-matrix[data-index="${index}"]`).val(formatNumber(m2, 2));
        }
    });

    // M² -> Hectáreas
    $(document).on('input', `.m2-matrix[data-index="${index}"]`, function() {
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');
        if (valor && !isNaN(valor)) {
            const m2 = parseFloat(valor);
            const ha = m2 / 10000;
            $(`.hectareas-matrix[data-index="${index}"]`).val(formatNumber(ha, 4));
        }
    });
}

function procesarFoliosMasivos() {
    const texto = $('#folios-masivos').val().trim();
    if (!texto) {
        Swal.fire('Atención', 'Ingrese la lista de folios', 'warning');
        return;
    }

    // Dividir por saltos de línea o comas
    const folios = texto.split(/[\n,;]+/).map(f => f.trim()).filter(f => f);

    if (folios.length < 11 || folios.length > 150) {
        Swal.fire('Error', 'Debe ingresar entre 11 y 150 folios', 'error');
        return;
    }

    $('#resultado-masivos').html('<p><i class="fas fa-spinner fa-spin"></i> Procesando ' + folios.length + ' folios...</p>');

    // TODO: Llamar al backend para buscar todos
    // Por ahora simulación
    Swal.fire('Info', 'Procesamiento masivo pendiente de implementar', 'info');
}

// =====================================================
// GENERAR FORMULARIO MANUAL
// =====================================================
function generarFormularioManual() {
    const esRural = wizardData.ubicacionManual === 'R';
    const tipoInmueble = esRural ? 'HIJUELA' : 'SITIO';

    let html = '<div class="alert alert-warning">';
    html += '<i class="fas fa-edit"></i> ';
    html += `<strong>Ingreso Manual:</strong> Tipo de plano: ${wizardData.tipoPlano} (${tipoInmueble}S)`;
    html += '</div>';

    if (wizardData.cantidadTipo === '1') {
        // 1 FOLIO SIMPLE
        html += generarFormularioFolioManual(0, esRural, tipoInmueble);
    } else if (wizardData.cantidadTipo === 'multiple') {
        // Preguntar cantidad exacta
        html += '<div class="form-group">';
        html += '<label>¿Cuántos folios exactamente? (2-10)</label>';
        html += '<select class="form-control" id="cantidad-exacta-manual">';
        html += '<option value="">Seleccionar...</option>';
        for (let i = 2; i <= 10; i++) {
            html += `<option value="${i}">${i} folios</option>`;
        }
        html += '</select>';
        html += '</div>';
        html += '<div id="forms-manuales"></div>';
    }

    $('#contenedor-folios').html(html);
    $('#titulo-folios').text('Paso 4: Ingreso Manual de Folios');

    if (wizardData.cantidadTipo === '1') {
        // Para 1 folio, habilitar botón continuar inmediatamente
        $('#btn-continuar-confirmacion').prop('disabled', false);

        // Agregar conversión para 1 folio rural
        if (esRural) {
            attachConversionListenersManual(0);
        }
    } else if (wizardData.cantidadTipo === 'multiple') {
        $('#cantidad-exacta-manual').on('change', function() {
            const cantidad = parseInt($(this).val());
            if (cantidad) {
                wizardData.cantidadFolios = cantidad;
                generarFormulariosMultiplesManual(cantidad, esRural, tipoInmueble);
            }
        });
    }

    // Listener para actualizar display superior cuando se seleccione comuna
    $('#comuna-manual').on('change', function() {
        const codigoComuna = $(this).val();
        actualizarDisplayCorrelativo(codigoComuna || null, wizardData.tipoPlano);
    });

    // Listener para mostrar input personalizado y generar campos de medidas
    $('.cantidad-inmuebles-manual').on('change', function() {
        const index = $(this).data('index');
        const value = $(this).val();
        const esRuralLocal = wizardData.ubicacionManual === 'R';
        const tipoInmuebleLocal = esRuralLocal ? 'HIJUELA' : 'SITIO';

        if (value === 'custom') {
            $(`#cantidad-custom-container-${index}`).show();
            $(`.cantidad-custom-manual[data-index="${index}"]`).prop('required', true).focus();
            $(`#medidas-inmuebles-container-${index}`).html('');
        } else if (value) {
            $(`#cantidad-custom-container-${index}`).hide();
            $(`.cantidad-custom-manual[data-index="${index}"]`).prop('required', false).val('');
            generarCamposMedidas(index, parseInt(value), esRuralLocal, tipoInmuebleLocal);
        } else {
            $(`#cantidad-custom-container-${index}`).hide();
            $(`#medidas-inmuebles-container-${index}`).html('');
        }
    });

    // Listener para input personalizado de cantidad
    $('.cantidad-custom-manual').on('change', function() {
        const index = $(this).data('index');
        const cantidad = parseInt($(this).val());
        const esRuralLocal = wizardData.ubicacionManual === 'R';
        const tipoInmuebleLocal = esRuralLocal ? 'HIJUELA' : 'SITIO';

        if (cantidad >= 6) {
            generarCamposMedidas(index, cantidad, esRuralLocal, tipoInmuebleLocal);
        }
    });
}

// Generar campos de medidas para cada hijuela/sitio
function generarCamposMedidas(folioIndex, cantidad, esRural, tipoInmueble) {
    let html = '<div class="border rounded p-3 bg-light">';
    html += '<h6 class="text-secondary mb-3"><i class="fas fa-ruler-combined"></i> Medidas de cada ' + tipoInmueble.toLowerCase() + '</h6>';

    for (let i = 0; i < cantidad; i++) {
        html += '<div class="row align-items-end mb-2">';
        html += '<div class="col-md-2">';
        html += '<strong>' + tipoInmueble + ' #' + (i + 1) + '</strong>';
        html += '</div>';

        if (esRural) {
            html += '<div class="col-md-4">';
            html += '<div class="form-group mb-0">';
            html += '<label class="small">Hectáreas</label>';
            html += '<input type="text" class="form-control form-control-sm hectareas-inmueble" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0,0000" inputmode="decimal" onkeypress="return validarNumeroDecimal(event)">';
            html += '<small class="text-muted">Auto-convierte a M²</small>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-4">';
        } else {
            html += '<div class="col-md-8">';
        }

        html += '<div class="form-group mb-0">';
        html += '<label class="small">M² <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control form-control-sm m2-inmueble" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0" required inputmode="numeric" onkeypress="return validarNumeroEntero(event)">';
        if (esRural) {
            html += '<small class="text-muted">Calculado desde Ha</small>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }

    html += '</div>';
    $(`#medidas-inmuebles-container-${folioIndex}`).html(html);

    // Agregar listeners de conversión hectáreas -> m² si es rural
    if (esRural) {
        attachConversionListenersInmuebles(folioIndex, cantidad);
    }
}

// Conversión hectáreas -> m² para inmuebles individuales (hijuelas/sitios)
function attachConversionListenersInmuebles(folioIndex, cantidad) {
    for (let i = 0; i < cantidad; i++) {
        // Hectáreas -> M² (conversión automática)
        $(document).off('input', `.hectareas-inmueble[data-folio="${folioIndex}"][data-inmueble="${i}"]`);
        $(document).on('input', `.hectareas-inmueble[data-folio="${folioIndex}"][data-inmueble="${i}"]`, function() {
            let valor = $(this).val().replace(/\./g, '').replace(',', '.');
            if (valor && !isNaN(valor)) {
                const ha = parseFloat(valor);
                const m2 = Math.round(ha * 10000); // 1 ha = 10,000 m²
                $(`.m2-inmueble[data-folio="${folioIndex}"][data-inmueble="${i}"]`).val(m2);
            } else {
                $(`.m2-inmueble[data-folio="${folioIndex}"][data-inmueble="${i}"]`).val('');
            }
        });
    }
}

function generarFormularioFolioManual(index, esRural, tipoInmueble) {
    let html = '<div class="card mb-3 folio-manual-card" data-index="' + index + '">';
    html += '<div class="card-body">';
    html += '<h6 class="text-primary mb-3">Folio #' + (index + 1) + '</h6>';

    // Fila 1: Folio y Solicitante
    html += '<div class="row">';
    html += '<div class="col-md-2">';
    html += '<div class="form-group">';
    html += '<label>Folio</label>';
    html += '<input type="text" class="form-control folio-manual" data-index="' + index + '" placeholder="Opcional">';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Solicitante <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control solicitante-manual" data-index="' + index + '" required>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Ap. Paterno</label>';
    html += '<input type="text" class="form-control ap-paterno-manual" data-index="' + index + '">';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-2">';
    html += '<div class="form-group">';
    html += '<label>Ap. Materno</label>';
    html += '<input type="text" class="form-control ap-materno-manual" data-index="' + index + '">';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Fila 3: Datos del plano (solo en primer folio)
    if (index === 0) {
        html += '<hr><h6>Datos del Plano (aplican a todos los folios)</h6>';
        html += '<div class="row">';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Comuna <span class="text-danger">*</span></label>';
        html += '<select class="form-control" id="comuna-manual" required>';
        html += '<option value="">Seleccionar...</option>';
        // Agregar comunas desde variable JavaScript
        Object.entries(comunasBiobio).forEach(([codigo, nombre]) => {
            html += `<option value="${codigo}">${nombre}</option>`;
        });
        html += '</select>';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Responsable <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control" id="responsable-manual" required>';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Proyecto <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control" id="proyecto-manual" required>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }

    // Selector de cantidad de hijuelas/sitios (al final del formulario)
    const labelCantidad = esRural ? 'hijuelas' : 'sitios';
    html += '<hr><h6>Cantidad de ' + labelCantidad + '</h6>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group mb-0">';
    html += '<label>¿Cuántas ' + labelCantidad + '? <span class="text-danger">*</span></label>';
    html += '<select class="form-control cantidad-inmuebles-manual" data-index="' + index + '" required>';
    html += '<option value="">Seleccionar...</option>';
    html += '<option value="1">1 ' + tipoInmueble.toLowerCase() + '</option>';
    html += '<option value="2">2 ' + labelCantidad + '</option>';
    html += '<option value="3">3 ' + labelCantidad + '</option>';
    html += '<option value="4">4 ' + labelCantidad + '</option>';
    html += '<option value="5">5 ' + labelCantidad + '</option>';
    html += '<option value="custom">Más...</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3" id="cantidad-custom-container-' + index + '" style="display: none;">';
    html += '<div class="form-group mb-0">';
    html += '<label>Cantidad exacta</label>';
    html += '<input type="number" class="form-control cantidad-custom-manual" data-index="' + index + '" min="6" placeholder="6+">';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Contenedor para los campos de medidas de cada hijuela/sitio
    html += '<div id="medidas-inmuebles-container-' + index + '" class="mb-3"></div>';

    html += '</div></div>';
    return html;
}

function generarFormulariosMultiplesManual(cantidad, esRural, tipoInmueble) {
    let html = '';
    for (let i = 0; i < cantidad; i++) {
        html += generarFormularioFolioManual(i, esRural, tipoInmueble);
    }
    $('#forms-manuales').html(html);
    $('#btn-continuar-confirmacion').prop('disabled', false);

    // Agregar conversión hectáreas ↔ m² si es rural
    if (esRural) {
        for (let i = 0; i < cantidad; i++) {
            attachConversionListenersManual(i);
        }
    }
}

// Conversión hectáreas ↔ m² para Manual
function attachConversionListenersManual(index) {
    // Hectáreas -> M²
    $(document).on('input', `.hectareas-manual[data-index="${index}"]`, function() {
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');
        if (valor && !isNaN(valor)) {
            const ha = parseFloat(valor);
            const m2 = ha * 10000;
            $(`.m2-manual[data-index="${index}"]`).val(formatNumber(m2, 2));
        }
    });

    // M² -> Hectáreas
    $(document).on('input', `.m2-manual[data-index="${index}"]`, function() {
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');
        if (valor && !isNaN(valor)) {
            const m2 = parseFloat(valor);
            const ha = m2 / 10000;
            $(`.hectareas-manual[data-index="${index}"]`).val(formatNumber(ha, 4));
        }
    });
}

// =====================================================
// MOSTRAR CONFIRMACIÓN
// =====================================================
function mostrarConfirmacion() {
    // Recolectar todos los folios según origen
    if (wizardData.origenFolios === 'matrix') {
        // Primero recolectar las medidas de los formularios
        if (!recolectarMedidasMatrix()) {
            return;
        }
        // Luego validar que todos están completos
        if (!validarFoliosMatrixCompleto()) {
            return;
        }
    } else if (wizardData.origenFolios === 'manual') {
        if (!recolectarFoliosManuales()) {
            return;
        }
    }

    // Calcular totales
    let totalHectareas = 0;
    let totalM2 = 0;
    wizardData.folios.forEach(folio => {
        if (folio.hectareas) {
            totalHectareas += parseFloat(folio.hectareas);
        }
        if (folio.m2) {
            totalM2 += parseFloat(folio.m2);
        }
    });

    // Obtener código comuna
    let codigoComuna = '';
    if (wizardData.origenFolios === 'matrix') {
        codigoComuna = wizardData.folios[0].codigo_comuna || '000';
    } else {
        codigoComuna = $('#comuna-manual').val() || '000';
    }

    // Generar número de plano: 08 + codigo_comuna + correlativo + tipo
    const numeroPlano = '08' + codigoComuna + wizardData.proximoCorrelativo + wizardData.tipoPlano;

    // Descripciones de tipo
    const tiposDesc = {
        'SR': 'Saneamiento Rural',
        'SU': 'Saneamiento Urbano',
        'CR': 'Fiscal Rural',
        'CU': 'Fiscal Urbano'
    };

    // Llenar resumen
    $('#confirm-numero').text(numeroPlano);
    $('#confirm-tipo').text(tiposDesc[wizardData.tipoPlano] || wizardData.tipoPlano);
    $('#confirm-comuna').text(wizardData.folios[0].comuna || $('#comuna-manual option:selected').text());
    $('#confirm-responsable').text(wizardData.folios[0].responsable || $('#responsable-manual').val());
    $('#confirm-proyecto').text(wizardData.folios[0].proyecto || $('#proyecto-manual').val());
    $('#confirm-total-folios').text(wizardData.folios.length);
    $('#confirm-total-ha').text(totalHectareas > 0 ? formatNumber(totalHectareas, 4) + ' ha' : '-');
    $('#confirm-total-m2').text(formatNumber(totalM2, 2) + ' m²');

    // Generar lista de folios
    let listaHTML = '<table class="table table-sm table-hover">';
    listaHTML += '<thead><tr>';
    listaHTML += '<th>#</th><th>Folio</th><th>Solicitante</th><th>Tipo</th>';
    if (wizardData.tipoPlano.includes('R')) {
        listaHTML += '<th>Hectáreas</th>';
    }
    listaHTML += '<th>M²</th></tr></thead><tbody>';

    wizardData.folios.forEach((folio, index) => {
        listaHTML += '<tr>';
        listaHTML += '<td>' + (index + 1) + '</td>';
        listaHTML += '<td>' + (folio.folio || '-') + '</td>';
        listaHTML += '<td>' + folio.solicitante + ' ' + (folio.apellido_paterno || '') + ' ' + (folio.apellido_materno || '') + '</td>';
        listaHTML += '<td>' + (folio.tipo_inmueble || (wizardData.ubicacionManual === 'R' ? 'HIJUELA' : 'SITIO')) + ' #' + (folio.numero_inmueble || (index + 1)) + '</td>';
        if (wizardData.tipoPlano.includes('R')) {
            listaHTML += '<td>' + (folio.hectareas ? formatNumber(parseFloat(folio.hectareas), 4) : '-') + '</td>';
        }
        listaHTML += '<td>' + formatNumber(parseFloat(folio.m2), 2) + '</td>';
        listaHTML += '</tr>';
    });

    listaHTML += '</tbody></table>';
    $('#confirm-lista-folios').html(listaHTML);

    // Mostrar card
    $('#card-folios').hide();
    $('#card-confirmacion').show();
}

// Validar que todos los folios Matrix están completos
function validarFoliosMatrixCompleto() {
    if (wizardData.folios.length === 0) {
        Swal.fire('Error', 'No hay folios para procesar', 'error');
        return false;
    }

    const foliosValidos = wizardData.folios.filter(f => f !== null);
    if (foliosValidos.length === 0) {
        Swal.fire('Error', 'No se encontraron folios válidos', 'error');
        return false;
    }

    wizardData.folios = foliosValidos;
    return true;
}

// Recolectar datos de formularios manuales
function recolectarFoliosManuales() {
    wizardData.folios = [];
    const esRural = wizardData.ubicacionManual === 'R';
    const tipoInmueble = esRural ? 'HIJUELA' : 'SITIO';

    // Obtener datos comunes del plano
    const comuna = $('#comuna-manual').val();
    const responsable = $('#responsable-manual').val();
    const proyecto = $('#proyecto-manual').val();

    if (!comuna || !responsable || !proyecto) {
        Swal.fire('Error', 'Debes completar Comuna, Responsable y Proyecto', 'warning');
        return false;
    }

    // Recolectar cada folio
    let errores = [];
    $('.solicitante-manual').each(function(index) {
        const solicitante = $(this).val().trim();
        const apPaterno = $(`.ap-paterno-manual[data-index="${index}"]`).val()?.trim() || '';
        const apMaterno = $(`.ap-materno-manual[data-index="${index}"]`).val()?.trim() || '';
        const folio = $(`.folio-manual[data-index="${index}"]`).val()?.trim() || '';

        if (!solicitante) {
            errores.push(`Folio ${index + 1}: Solicitante es obligatorio`);
            return;
        }

        // Sumar M² de todas las hijuelas/sitios de este folio
        let totalM2 = 0;
        let totalHectareas = 0;
        let cantidadInmuebles = 0;

        $(`.m2-inmueble[data-folio="${index}"]`).each(function() {
            const valor = $(this).val();
            if (valor) {
                const m2 = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
                if (!isNaN(m2) && m2 > 0) {
                    totalM2 += m2;
                    cantidadInmuebles++;
                }
            }
        });

        // Sumar hectáreas si es rural
        if (esRural) {
            $(`.hectareas-inmueble[data-folio="${index}"]`).each(function() {
                const valor = $(this).val();
                if (valor) {
                    const ha = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
                    if (!isNaN(ha) && ha > 0) {
                        totalHectareas += ha;
                    }
                }
            });
        }

        if (totalM2 <= 0) {
            errores.push(`Folio ${index + 1}: M² es obligatorio (debe ingresar medidas)`);
            return;
        }

        const folioData = {
            folio: folio || null,
            solicitante: solicitante,
            apellido_paterno: apPaterno || null,
            apellido_materno: apMaterno || null,
            tipo_inmueble: tipoInmueble,
            numero_inmueble: cantidadInmuebles,
            m2: totalM2,
            is_from_matrix: false,
            comuna: comuna,
            responsable: responsable,
            proyecto: proyecto
        };

        // Agregar hectáreas si es rural
        if (esRural && totalHectareas > 0) {
            folioData.hectareas = totalHectareas;
        }

        wizardData.folios.push(folioData);
    });

    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores.join('<br>')
        });
        return false;
    }

    if (wizardData.folios.length === 0) {
        Swal.fire('Error', 'No hay folios para crear', 'error');
        return false;
    }

    return true;
}

function formatNumber(num, decimals) {
    return num.toFixed(decimals).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// =====================================================
// CREAR PLANO
// =====================================================
function crearPlano() {
    // Mostrar loading
    Swal.fire({
        title: 'Creando plano...',
        text: 'Por favor espere',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Calcular totales
    let totalHectareas = 0;
    let totalM2 = 0;
    wizardData.folios.forEach(folio => {
        if (folio.hectareas) {
            totalHectareas += parseFloat(folio.hectareas);
        }
        totalM2 += parseFloat(folio.m2);
    });

    // Obtener datos comunes
    let comuna, responsable, proyecto, codigoComuna;

    if (wizardData.origenFolios === 'matrix') {
        comuna = wizardData.folios[0].comuna;
        responsable = wizardData.folios[0].responsable;
        proyecto = wizardData.folios[0].proyecto;
        codigoComuna = wizardData.folios[0].codigo_comuna;
    } else {
        codigoComuna = $('#comuna-manual').val();
        comuna = $('#comuna-manual option:selected').text();
        responsable = $('#responsable-manual').val();
        proyecto = $('#proyecto-manual').val();
    }

    // Preparar payload
    const payload = {
        // Datos del plano
        tipo_plano: wizardData.origenFolios, // 'matrix' o 'manual'
        tipo_ubicacion: wizardData.tipoPlano, // 'SR', 'SU', 'CR', 'CU'
        codigo_comuna: codigoComuna,
        comuna_nombre: comuna,
        responsable: responsable,
        proyecto: proyecto,

        // Folios
        folios: wizardData.folios.map(folio => {
            return {
                folio: folio.folio || '',
                solicitante: folio.solicitante || folio.nombres,
                apellido_paterno: folio.apellido_paterno || null,
                apellido_materno: folio.apellido_materno || null,
                tipo_inmueble: folio.tipo_inmueble,
                numero_inmueble: folio.numero_inmueble || null,
                hectareas: folio.hectareas || null,
                m2: folio.m2,
                is_from_matrix: wizardData.origenFolios === 'matrix'
            };
        })
    };

    // Enviar al backend
    $.ajax({
        url: '{{ route("planos.crear.store") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Plano creado exitosamente!',
                    html: `
                        <p><strong>Número de plano:</strong> ${response.plano.numero}</p>
                        <p><strong>Folios:</strong> ${response.plano.folios}</p>
                    `,
                    confirmButtonText: 'Ver en la tabla',
                    showCancelButton: true,
                    cancelButtonText: 'Crear otro plano'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirigir a la tabla de planos
                        window.location.href = '{{ route("planos.index") }}';
                    } else {
                        // Recargar página para crear otro
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear plano',
                    text: response.message || 'Ocurrió un error inesperado'
                });
            }
        },
        error: function(xhr) {
            let mensaje = 'Error al crear el plano';

            if (xhr.status === 422) {
                // Errores de validación
                const errores = xhr.responseJSON?.errors;
                if (errores) {
                    const listaErrores = Object.values(errores).flat();
                    mensaje = listaErrores.join('<br>');
                }
            } else if (xhr.responseJSON?.message) {
                mensaje = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: mensaje
            });
        }
    });
}
</script>
@endpush
