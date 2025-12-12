@extends('layouts.admin')

@section('title', 'Agregar Planos')

@section('page-title', 'Crear Nuevo Plano')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Agregar Planos</li>
@endsection

@push('styles')
<style>
/* Cards de selección de cantidad de folios - Colores Gobierno */
.card-seleccion {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
}

.card-seleccion:hover {
    border-color: #0f69b4;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(15, 105, 180, 0.15);
}

.card-seleccion.selected {
    border-color: #0f69b4;
    background-color: #e8f2fb;
    box-shadow: 0 4px 15px rgba(15, 105, 180, 0.3);
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
    color: #0f69b4;
}
</style>
@endpush

@section('content')

<!-- PASO 1: Control de Numeración Correlativa (SIEMPRE VISIBLE) -->
<div class="card" id="card-control-sesion">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            <i class="fas fa-hashtag"></i>
            Control de Numeración Correlativa
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
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
            <div class="col-md-4">
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
            <div class="col-md-4">
                <div class="info-box mb-0" id="control-info-box">
                    <div class="info-box-icon bg-secondary" id="control-icon-box">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Control de Sesión</span>
                        <span class="info-box-number" id="control-status-display">Sin Control</span>
                        <small class="text-muted">Usa el badge <i class="fas fa-lock"></i> en la barra superior</small>
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
                            <strong>Urbano</strong> - SITIOS con Hectáreas o M²
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

        <div class="text-center mt-3" id="botones-folios-principales">
            <button type="button" class="btn btn-secondary" id="btn-volver-folios">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-success" id="btn-continuar-confirmacion" disabled>
                Continuar a Confirmación <i class="fas fa-arrow-right"></i>
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
                        <th>Cantidad de Folios:</th>
                        <td id="confirm-cantidad-folios">---</td>
                    </tr>
                    <tr>
                        <th>Total Inmuebles:</th>
                        <td id="confirm-total-inmuebles">---</td>
                    </tr>
                    <tr>
                        <th>Total Superficie:</th>
                        <td id="confirm-total-superficie">---</td>
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

// Funciones de validación eliminadas - inputs ahora son libres
// El formateo y validación se hace al salir del campo (blur) y en el backend

$(document).ready(function() {
    cargarNumeracionCorrelativa();
    initWizardListeners();
    initControlSesion();
});

// =====================================================
// CONTROL DE SESIÓN (Escucha eventos del navbar)
// =====================================================
let tieneControl = false;

function initControlSesion() {
    // Escuchar evento del layout cuando cambia el estado de control
    $(document).on('sessionControl:changed', function(e, hasControl) {
        tieneControl = hasControl;
        actualizarUIControlLocal(hasControl);
    });

    // Verificar estado inicial
    $.get('{{ route("session-control.status") }}')
        .done(function(response) {
            tieneControl = response.hasControl;
            actualizarUIControlLocal(response.hasControl);
        });
}

function actualizarUIControlLocal(hasControl) {
    const iconBox = $('#control-icon-box');
    const statusDisplay = $('#control-status-display');

    if (hasControl) {
        iconBox.removeClass('bg-secondary bg-danger').addClass('bg-success');
        iconBox.find('i').removeClass('fa-lock').addClass('fa-unlock');
        statusDisplay.text('Con Control');
        $('.card-seleccion').removeClass('disabled');
    } else {
        iconBox.removeClass('bg-secondary bg-success').addClass('bg-danger');
        iconBox.find('i').removeClass('fa-unlock').addClass('fa-lock');
        statusDisplay.text('Sin Control');
        $('.card-seleccion').addClass('disabled');
    }
}

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

            wizardData.ultimoCorrelativo = response.ultimoCorrelativo;
            wizardData.proximoCorrelativo = response.proximo;

            if (response.hayDatos) {
                // HAY DATOS: Mostrar número completo del último plano
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
        // Limpiar datos de folios al retroceder
        wizardData.folios = [];
        wizardData.foliosCompletados = [];
        wizardData.cantidadFolios = null;
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
        // Limpiar datos de folios al retroceder
        wizardData.folios = [];
        wizardData.foliosCompletados = [];
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
    const comuna = codigoComuna || '';

    // Formatear número completo: 0810129524SU (sin espacios)
    const correlativoPadded = String(wizardData.proximoCorrelativo).padStart(5, '0');
    const numeroCompleto = '08' + comuna + correlativoPadded + tipo;
    $('#proximo-correlativo-display').text(numeroCompleto);

    // También actualizar el último correlativo con formato completo
    if (wizardData.ultimoCorrelativo) {
        const ultimoPadded = String(wizardData.ultimoCorrelativo).padStart(5, '0');
        const ultimoCompleto = '08' + comuna + ultimoPadded + tipo;
        $('#ultimo-correlativo-display').text(ultimoCompleto);
    }
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

    // Limpiar datos de folios al retroceder para evitar inconsistencias
    wizardData.folios = [];
    wizardData.foliosCompletados = [];
    wizardData.folioActualIndex = 0;

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
                // Limpiar datos anteriores al cambiar cantidad
                wizardData.folios = [];
                wizardData.foliosCompletados = [];
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
    // Inicializar tracking de folio actual y limpiar datos anteriores
    wizardData.folioActualIndex = 0;
    wizardData.foliosCompletados = [];
    wizardData.folios = [];

    // Ocultar botones principales durante el wizard
    $('#botones-folios-principales').hide();

    // Mostrar el primer folio
    mostrarFolioActualMultiple();
}

function mostrarFolioActualMultiple() {
    const index = wizardData.folioActualIndex;
    const total = wizardData.cantidadFolios;
    const progreso = Math.round(((index) / total) * 100);

    let html = '';

    // Barra de progreso
    html += '<div class="mb-3">';
    html += '<div class="d-flex justify-content-between align-items-center mb-2">';
    html += '<span class="font-weight-bold text-primary"><i class="fas fa-file-alt"></i> Folio ' + (index + 1) + ' de ' + total + '</span>';
    html += '<span class="badge badge-info">' + progreso + '% completado</span>';
    html += '</div>';
    html += '<div class="progress" style="height: 8px;">';
    html += '<div class="progress-bar bg-success" style="width: ' + progreso + '%"></div>';
    html += '</div>';
    html += '</div>';

    // Resumen de folios completados
    if (wizardData.foliosCompletados.length > 0) {
        html += '<div class="mb-3">';
        html += '<small class="text-muted font-weight-bold">Folios completados:</small>';
        html += '<div class="list-group list-group-flush mt-1">';
        wizardData.foliosCompletados.forEach((completado, i) => {
            const cantidadInm = completado.cantidadInmuebles || 1;
            const tipoLabel = completado.tipo === 'HIJUELA' ? 'hijuela' : 'sitio';
            const tipoPlural = cantidadInm > 1 ? (completado.tipo === 'HIJUELA' ? 'hijuelas' : 'sitios') : tipoLabel;
            html += '<div class="list-group-item list-group-item-success py-2 px-3">';
            html += '<div class="d-flex justify-content-between align-items-center">';
            html += '<span><i class="fas fa-check-circle text-success mr-2"></i>';
            html += '<strong>Folio #' + (i + 1) + ':</strong> ' + completado.folio + ' - ' + completado.solicitante + '</span>';
            html += '<span class="badge badge-light">' + cantidadInm + ' ' + tipoPlural + '</span>';
            html += '</div>';
            html += '</div>';
        });
        html += '</div>';
        html += '</div>';
    }

    // Card del folio actual
    html += '<div class="card border-primary">';
    html += '<div class="card-header bg-primary text-white">';
    html += '<h6 class="mb-0"><i class="fas fa-search"></i> Buscando Folio #' + (index + 1) + '</h6>';
    html += '</div>';
    html += '<div class="card-body">';

    // Input de búsqueda
    html += '<div class="form-group">';
    html += '<label>Número de Folio <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control form-control-lg folio-input-multiple" data-index="' + index + '" ';
    html += 'placeholder="Escribe el folio y presiona Tab" required autofocus>';
    html += '<small class="text-muted">Presiona <kbd>Tab</kbd> o <kbd>Enter</kbd> para buscar en Matrix</small>';
    html += '</div>';

    // Contenedor de resultados
    html += '<div class="resultado-folio-multiple" id="resultado-multiple-' + index + '"></div>';

    html += '</div>';
    html += '</div>';

    // Botones de navegación
    html += '<div class="d-flex justify-content-between mt-3">';
    if (index > 0) {
        html += '<button type="button" class="btn btn-secondary" id="btn-folio-anterior">';
        html += '<i class="fas fa-arrow-left"></i> Anterior</button>';
    } else {
        html += '<div></div>';
    }
    html += '<button type="button" class="btn btn-success" id="btn-folio-siguiente" disabled>';
    if (index < total - 1) {
        html += 'Siguiente <i class="fas fa-arrow-right"></i></button>';
    } else {
        html += 'Finalizar <i class="fas fa-check"></i></button>';
    }
    html += '</div>';

    $('#inputs-multiples').html(html);

    // Si ya hay datos de este folio, restaurarlos
    if (wizardData.folios[index]) {
        const folio = wizardData.folios[index];
        $(`.folio-input-multiple[data-index="${index}"]`).val(folio.folio);
        // Mostrar resultado guardado
        mostrarResultadoMatrixMultiple(index, folio);
    }

    // Event listeners
    $('.folio-input-multiple').on('keydown', function(e) {
        if (e.key === 'Tab' || e.key === 'Enter') {
            e.preventDefault();
            const idx = $(this).data('index');
            buscarFolioMatrixMultiple(idx);
        }
    });

    $('#btn-folio-anterior').on('click', retrocederFolioMultiple);
    $('#btn-folio-siguiente').on('click', avanzarSiguienteFolioMultiple);

    // Focus en el input
    setTimeout(() => {
        $(`.folio-input-multiple[data-index="${index}"]`).focus();
    }, 100);
}

function buscarFolioMatrixMultiple(index) {
    const folio = $(`.folio-input-multiple[data-index="${index}"]`).val().trim();
    const $resultado = $(`#resultado-multiple-${index}`);

    if (!folio) {
        $resultado.html('<small class="text-danger">Ingrese un número de folio</small>');
        return;
    }

    // Validar duplicados en folios ya completados del mismo plano
    const duplicado = wizardData.foliosCompletados.find(f => f.folio === folio);
    if (duplicado) {
        const posicionDuplicado = wizardData.foliosCompletados.indexOf(duplicado) + 1;
        $resultado.html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Folio duplicado</strong><br>
                Este folio ya fue agregado en el <strong>Folio #${posicionDuplicado}</strong> de este mismo plano.
            </div>
        `);
        $('#btn-folio-siguiente').prop('disabled', true);
        return;
    }

    $resultado.html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Buscando...</p></div>');

    $.ajax({
        url: '{{ route("planos.crear.buscar-folio") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: { folio: folio },
        success: function(response) {
            if (!response.encontrado) {
                $resultado.html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' + response.message + '</div>');
                $('#btn-folio-siguiente').prop('disabled', true);
                return;
            }

            if (response.yaUsado) {
                $resultado.html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' + response.message + '</div>');
                $('#btn-folio-siguiente').prop('disabled', true);
                return;
            }

            // Normalizar tipo_inmueble
            const datos = response.datos;
            const tipoOriginal = (datos.tipo_inmueble || '').toUpperCase();
            if (tipoOriginal.includes('HIJUELA') || tipoOriginal.includes('RURAL')) {
                datos.tipo_inmueble = 'HIJUELA';
            } else {
                datos.tipo_inmueble = 'SITIO';
            }

            mostrarResultadoMatrixMultiple(index, datos);
            wizardData.folios[index] = datos;
        },
        error: function() {
            $resultado.html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error de conexión</div>');
            $('#btn-folio-siguiente').prop('disabled', true);
        }
    });
}

function mostrarResultadoMatrixMultiple(index, data) {
    const esRural = data.tipo_inmueble === 'HIJUELA';
    const tipoLabel = esRural ? 'Hijuela' : 'Sitio';

    let html = '<div class="alert alert-success py-2 mb-3">';
    html += '<i class="fas fa-check-circle"></i> <strong>Encontrado:</strong> ' + tipoLabel;
    html += '</div>';

    // Datos personales (editables)
    html += '<div class="row">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Solicitante <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control solicitante-matrix" data-index="' + index + '" value="' + (data.solicitante || '') + '" required>';
    html += '</div></div>';

    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Ap. Paterno</label>';
    html += '<input type="text" class="form-control ap-paterno-matrix" data-index="' + index + '" value="' + (data.apellido_paterno || '') + '">';
    html += '</div></div>';

    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Ap. Materno</label>';
    html += '<input type="text" class="form-control ap-materno-matrix" data-index="' + index + '" value="' + (data.apellido_materno || '') + '">';
    html += '</div></div>';
    html += '</div>';

    // Datos del plano (solo en primer folio) - ANTES del selector
    if (index === 0) {
        html += '<hr><h6>Datos del Plano (aplican a todos los folios)</h6>';
        html += '<div class="row">';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Comuna</label>';
        html += '<input type="text" class="form-control comuna-matrix" data-index="' + index + '" value="' + (data.comuna || '') + '" readonly>';
        html += '</div></div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Responsable <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control responsable-matrix" data-index="' + index + '" value="' + (data.responsable || '') + '" required>';
        html += '</div></div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Proyecto <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control proyecto-matrix" data-index="' + index + '" value="' + (data.proyecto || '') + '" required>';
        html += '</div></div>';
        html += '</div>';
    }

    // Selector de cantidad (AL FINAL del formulario)
    const labelCantidad = esRural ? 'hijuelas' : 'sitios';
    html += '<hr><h6>Cantidad de ' + labelCantidad + '</h6>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group mb-0">';
    html += '<label>¿Cuántas ' + labelCantidad + '? <span class="text-danger">*</span></label>';
    html += '<select class="form-control cantidad-inmuebles-matrix" data-index="' + index + '" required>';
    html += '<option value="">Seleccionar...</option>';
    html += '<option value="1">1 ' + tipoLabel.toLowerCase() + '</option>';
    html += '<option value="2">2 ' + labelCantidad + '</option>';
    html += '<option value="3">3 ' + labelCantidad + '</option>';
    html += '<option value="4">4 ' + labelCantidad + '</option>';
    html += '<option value="5">5 ' + labelCantidad + '</option>';
    html += '<option value="custom">Más...</option>';
    html += '</select>';
    html += '</div></div>';
    html += '<div class="col-md-3" id="cantidad-custom-container-matrix-' + index + '" style="display: none;">';
    html += '<div class="form-group mb-0">';
    html += '<label>Cantidad exacta</label>';
    html += '<input type="number" class="form-control cantidad-custom-matrix" data-index="' + index + '" min="6" placeholder="6+">';
    html += '</div></div>';
    html += '</div>';

    // Contenedor para medidas
    html += '<div id="medidas-inmuebles-container-matrix-' + index + '" class="mb-3"></div>';

    $(`#resultado-multiple-${index}`).html(html);

    // Listeners para el selector
    attachCantidadListenersMatrix(index, esRural, tipoLabel);

    // Habilitar botón siguiente cuando se complete
    validarFolioActualMultiple(index);
}

function validarFolioActualMultiple(index) {
    // Listener para validar cuando cambia la cantidad, m² o hectáreas
    $(document).off('change input', `.cantidad-inmuebles-matrix[data-index="${index}"], .m2-inmueble-matrix[data-folio="${index}"]`);
    $(document).on('change input', `.cantidad-inmuebles-matrix[data-index="${index}"], .m2-inmueble-matrix[data-folio="${index}"]`, function() {
        verificarCompletitudFolioMultiple(index);
    });
}

function verificarCompletitudFolioMultiple(index) {
    const folio = wizardData.folios[index];
    if (!folio) {
        $('#btn-folio-siguiente').prop('disabled', true);
        return;
    }

    // Verificar cantidad seleccionada
    const cantidadSelector = $(`.cantidad-inmuebles-matrix[data-index="${index}"]`).val();
    if (!cantidadSelector) {
        $('#btn-folio-siguiente').prop('disabled', true);
        return;
    }

    // Obtener cantidad real
    let cantidad;
    if (cantidadSelector === 'custom') {
        cantidad = parseInt($(`.cantidad-custom-matrix[data-index="${index}"]`).val());
    } else {
        cantidad = parseInt(cantidadSelector);
    }

    if (!cantidad || cantidad < 1) {
        $('#btn-folio-siguiente').prop('disabled', true);
        return;
    }

    // Verificar que cada inmueble tenga M²
    let todosCompletos = true;
    for (let i = 0; i < cantidad; i++) {
        const m2 = $(`.m2-inmueble-matrix[data-folio="${index}"][data-inmueble="${i}"]`).val();

        // Debe tener M²
        if (!m2) {
            todosCompletos = false;
            break;
        }
    }

    $('#btn-folio-siguiente').prop('disabled', !todosCompletos);
}

function avanzarSiguienteFolioMultiple() {
    const index = wizardData.folioActualIndex;
    const folio = wizardData.folios[index];

    // Recolectar datos del folio actual
    const solicitante = $(`.solicitante-matrix[data-index="${index}"]`).val()?.trim();
    const apPaterno = $(`.ap-paterno-matrix[data-index="${index}"]`).val()?.trim();
    const apMaterno = $(`.ap-materno-matrix[data-index="${index}"]`).val()?.trim();

    // Obtener cantidad de inmuebles
    const cantidadSelector = $(`.cantidad-inmuebles-matrix[data-index="${index}"]`).val();
    let cantidad;
    if (cantidadSelector === 'custom') {
        cantidad = parseInt($(`.cantidad-custom-matrix[data-index="${index}"]`).val());
    } else {
        cantidad = parseInt(cantidadSelector);
    }

    // Recolectar inmuebles
    const inmuebles = [];
    let totalM2Base = 0; // Acumular todo en M² como unidad base
    const esRural = folio.tipo_inmueble === 'HIJUELA';

    for (let i = 0; i < cantidad; i++) {
        // Leer solo M²
        const m2Input = $(`.m2-inmueble-matrix[data-folio="${index}"][data-inmueble="${i}"]`).val();

        const m2 = m2Input ? normalizarNumeroJS(m2Input) : 0;

        // Validar que tenga M²
        if (!m2 || m2 <= 0) {
            continue; // Skip inmuebles vacíos
        }

        const inmueble = {
            numero_inmueble: i + 1,
            tipo_inmueble: folio.tipo_inmueble,
            m2: m2
        };

        // Sumar a total en M²
        totalM2Base += m2;

        inmuebles.push(inmueble);
    }

    // Actualizar folio
    folio.solicitante = solicitante;
    folio.apellido_paterno = apPaterno || null;
    folio.apellido_materno = apMaterno || null;
    folio.m2 = totalM2Base > 0 ? totalM2Base : null;
    folio.hectareas = totalM2Base > 0 ? totalM2Base / 10000 : null;
    folio.inmuebles = inmuebles;

    // Datos del plano (solo primer folio)
    if (index === 0) {
        folio.comuna = $(`.comuna-matrix[data-index="${index}"]`).val()?.trim();
        folio.responsable = $(`.responsable-matrix[data-index="${index}"]`).val()?.trim();
        folio.proyecto = $(`.proyecto-matrix[data-index="${index}"]`).val()?.trim();
    }

    // Guardar en completados para mostrar resumen
    wizardData.foliosCompletados[index] = {
        folio: folio.folio,
        solicitante: solicitante,
        tipo: folio.tipo_inmueble,
        cantidadInmuebles: cantidad
    };

    // Avanzar o finalizar
    if (index < wizardData.cantidadFolios - 1) {
        wizardData.folioActualIndex++;
        mostrarFolioActualMultiple();
    } else {
        // Todos completados - pasar a confirmación
        $('#btn-continuar-confirmacion').prop('disabled', false);

        // Mostrar botones principales nuevamente
        $('#botones-folios-principales').show();

        // Determinar tipo de plano basado en el primer folio
        wizardData.tipoPlano = 'S' + (folio.tipo_inmueble === 'HIJUELA' ? 'R' : 'U');

        Swal.fire({
            icon: 'success',
            title: '¡Todos los folios completados!',
            text: 'Ahora puedes continuar a la confirmación',
            confirmButtonText: 'Continuar'
        });
    }
}

function retrocederFolioMultiple() {
    if (wizardData.folioActualIndex > 0) {
        wizardData.folioActualIndex--;
        mostrarFolioActualMultiple();
    }
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

    html += '</div>';
    html += '</div>';

    // SELECTOR DE CANTIDAD DE HIJUELAS/SITIOS
    const labelCantidad = esRural ? 'hijuelas' : 'sitios';
    html += '<hr><h6>Cantidad de ' + labelCantidad + '</h6>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group mb-0">';
    html += '<label>¿Cuántas ' + labelCantidad + '? <span class="text-danger">*</span></label>';
    html += '<select class="form-control cantidad-inmuebles-matrix" data-index="' + index + '" required>';
    html += '<option value="">Seleccionar...</option>';
    html += '<option value="1">1 ' + tipoLabel.toLowerCase() + '</option>';
    html += '<option value="2">2 ' + labelCantidad + '</option>';
    html += '<option value="3">3 ' + labelCantidad + '</option>';
    html += '<option value="4">4 ' + labelCantidad + '</option>';
    html += '<option value="5">5 ' + labelCantidad + '</option>';
    html += '<option value="custom">Más...</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3" id="cantidad-custom-container-matrix-' + index + '" style="display: none;">';
    html += '<div class="form-group mb-0">';
    html += '<label>Cantidad exacta</label>';
    html += '<input type="number" class="form-control cantidad-custom-matrix" data-index="' + index + '" min="6" placeholder="6+">';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    // Contenedor para los campos de medidas de cada hijuela/sitio
    html += '<div id="medidas-inmuebles-container-matrix-' + index + '" class="mb-3"></div>';

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

    // Agregar listeners para selector de cantidad
    attachCantidadListenersMatrix(index, esRural, tipoLabel);
}

// Listeners para selector de cantidad en Matrix
function attachCantidadListenersMatrix(index, esRural, tipoInmueble) {
    // Listener para selector de cantidad
    $(document).off('change', `.cantidad-inmuebles-matrix[data-index="${index}"]`);
    $(document).on('change', `.cantidad-inmuebles-matrix[data-index="${index}"]`, function() {
        const value = $(this).val();

        if (value === 'custom') {
            $(`#cantidad-custom-container-matrix-${index}`).show();
            $(`.cantidad-custom-matrix[data-index="${index}"]`).prop('required', true).focus();
            $(`#medidas-inmuebles-container-matrix-${index}`).html('');
        } else if (value) {
            $(`#cantidad-custom-container-matrix-${index}`).hide();
            $(`.cantidad-custom-matrix[data-index="${index}"]`).prop('required', false).val('');
            generarCamposMedidasMatrix(index, parseInt(value), esRural, tipoInmueble);
        } else {
            $(`#cantidad-custom-container-matrix-${index}`).hide();
            $(`#medidas-inmuebles-container-matrix-${index}`).html('');
        }
    });

    // Listener para input personalizado
    $(document).off('change', `.cantidad-custom-matrix[data-index="${index}"]`);
    $(document).on('change', `.cantidad-custom-matrix[data-index="${index}"]`, function() {
        const cantidad = parseInt($(this).val());
        if (cantidad >= 6) {
            generarCamposMedidasMatrix(index, cantidad, esRural, tipoInmueble);
        }
    });
}

// Generar campos de medidas para Matrix
function generarCamposMedidasMatrix(folioIndex, cantidad, esRural, tipoInmueble) {
    let html = '<div class="border rounded p-3 bg-light">';
    html += '<h6 class="text-secondary mb-3"><i class="fas fa-ruler-combined"></i> Medidas de cada ' + tipoInmueble.toLowerCase() + '</h6>';

    for (let i = 0; i < cantidad; i++) {
        html += '<div class="row mb-2">';
        html += '<div class="col-md-2 d-flex align-items-center">';
        html += '<strong>' + tipoInmueble + ' #' + (i + 1) + '</strong>';
        html += '</div>';

        // M² - Requerido
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">M²</label>';
        html += '<input type="text" class="form-control form-control-sm m2-inmueble-matrix" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="Ej: 520.21 o 520,21" inputmode="decimal">';
        html += '</div>';
        html += '</div>';

        // Hectáreas - Auto calculado (readonly)
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">Hectáreas <small class="text-muted">(auto)</small></label>';
        html += '<input type="text" class="form-control form-control-sm bg-light ha-inmueble-matrix" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0,00" readonly style="cursor: not-allowed;">';
        html += '</div>';
        html += '</div>';

        // Espaciador
        html += '<div class="col-md-2"></div>';

        html += '</div>';
    }

    html += '</div>';

    $(`#medidas-inmuebles-container-matrix-${folioIndex}`).html(html);

    // Agregar listener para conversión automática M² → Hectáreas
    $(`.m2-inmueble-matrix[data-folio="${folioIndex}"]`).on('input', function() {
        const m2Input = $(this).val();
        const folioIdx = $(this).data('folio');
        const inmuebleIdx = $(this).data('inmueble');
        const haInput = $(`.ha-inmueble-matrix[data-folio="${folioIdx}"][data-inmueble="${inmuebleIdx}"]`);

        if (m2Input && m2Input.trim() !== '') {
            const m2 = normalizarNumeroJS(m2Input);
            if (m2 !== null && !isNaN(m2) && m2 > 0) {
                const ha = m2 / 10000;
                haInput.val(formatNumber(ha, 2));
            } else {
                haInput.val('');
            }
        } else {
            haInput.val('');
        }
    });
}

// Función helper para normalizar números (detecta formato automáticamente)
function normalizarNumeroJS(valor) {
    if (!valor || valor === '') return null;

    valor = String(valor).trim().replace(/\s/g, ''); // quitar espacios

    const tienePunto = valor.includes('.');
    const tieneComa = valor.includes(',');

    if (tienePunto && tieneComa) {
        // Ambos presentes: el último es decimal
        const posPunto = valor.lastIndexOf('.');
        const posComa = valor.lastIndexOf(',');

        if (posPunto > posComa) {
            // Formato USA: "1,234.56" → quitar comas, mantener punto
            valor = valor.replace(/,/g, '');
        } else {
            // Formato chileno: "1.234,56" → quitar puntos, coma a punto
            valor = valor.replace(/\./g, '').replace(',', '.');
        }
    } else if (tieneComa) {
        // Solo coma: formato chileno
        valor = valor.replace(',', '.');
    } else if (tienePunto) {
        // Solo punto: detectar si es decimal o separador miles
        const partes = valor.split('.');
        if (partes.length > 2) {
            // Múltiples puntos: separadores de miles "1.234.567"
            valor = valor.replace(/\./g, '');
        } else if (partes[1] && partes[1].length > 2) {
            // Más de 2 decimales: probablemente separador miles "1.2345"
            valor = valor.replace(/\./g, '');
        }
        // Sino, asumir que es decimal: "5483.32" → mantener
    }

    return parseFloat(valor);
}

// Conversión automática M² → Hectáreas (solo una dirección para evitar loops)
/**
 * Conversión bidireccional M² ↔ Hectáreas
 * Reglas:
 * - Si escribe M² → Convierte a Ha y bloquea input Ha (readonly)
 * - Si escribe Ha → Limpia y bloquea input M² (readonly)
 * - Si borra → Habilita ambos campos
 */
// Funciones de conversión bidireccional eliminadas - ahora solo se usa M² con conversión automática a Hectáreas

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

        // Si el folio ya fue recolectado por el wizard (tiene inmuebles), usar datos guardados
        if (folio.inmuebles && folio.inmuebles.length > 0 && folio.solicitante) {
            // Validar datos ya guardados
            if (!folio.solicitante) {
                errores.push(`Folio ${folio.folio}: Solicitante es obligatorio`);
            }
            // Validar que tenga M²
            if (!folio.m2 || folio.m2 <= 0) {
                errores.push(`Folio ${folio.folio}: Debe ingresar M²`);
            }
            // Datos del plano (solo primer folio)
            if (index === 0) {
                if (!folio.responsable) {
                    errores.push('Responsable es obligatorio');
                }
                if (!folio.proyecto) {
                    errores.push('Proyecto es obligatorio');
                }
            }
            return; // Ya está recolectado, salir
        }

        // Si no fue recolectado por wizard, leer del DOM (caso 1 folio)
        const solicitante = $(`.solicitante-matrix[data-index="${index}"]`).val()?.trim();
        const apPaterno = $(`.ap-paterno-matrix[data-index="${index}"]`).val()?.trim();
        const apMaterno = $(`.ap-materno-matrix[data-index="${index}"]`).val()?.trim();

        if (!solicitante) {
            errores.push(`Folio ${folio.folio}: Solicitante es obligatorio`);
            return;
        }

        // Verificar que se haya seleccionado cantidad de inmuebles
        const cantidadSelector = $(`.cantidad-inmuebles-matrix[data-index="${index}"]`).val();
        if (!cantidadSelector) {
            errores.push(`Folio ${folio.folio}: Debe seleccionar cantidad de ${folio.tipo_inmueble === 'HIJUELA' ? 'hijuelas' : 'sitios'}`);
            return;
        }

        // Obtener cantidad real
        let cantidadInmuebles;
        if (cantidadSelector === 'custom') {
            cantidadInmuebles = parseInt($(`.cantidad-custom-matrix[data-index="${index}"]`).val());
        } else {
            cantidadInmuebles = parseInt(cantidadSelector);
        }

        if (!cantidadInmuebles || cantidadInmuebles < 1) {
            errores.push(`Folio ${folio.folio}: Cantidad de inmuebles inválida`);
            return;
        }

        // Recolectar inmuebles individuales
        const inmuebles = [];
        let totalM2Base = 0; // Acumular todo en M² como unidad base
        const esRural = folio.tipo_inmueble === 'HIJUELA';

        for (let i = 0; i < cantidadInmuebles; i++) {
            // Leer solo M²
            const m2Input = $(`.m2-inmueble-matrix[data-folio="${index}"][data-inmueble="${i}"]`).val();

            const m2 = m2Input ? normalizarNumeroJS(m2Input) : 0;

            // Validar que tenga M²
            if (!m2 || m2 <= 0) {
                errores.push(`Folio ${folio.folio}: ${folio.tipo_inmueble} #${i + 1} debe tener M²`);
                continue;
            }

            const inmueble = {
                numero_inmueble: i + 1,
                tipo_inmueble: folio.tipo_inmueble,
                m2: m2
            };

            // Sumar a total en M²
            totalM2Base += m2;

            inmuebles.push(inmueble);
        }

        // Calcular totales: M² base y su equivalente en Hectáreas
        const totalM2 = totalM2Base > 0 ? totalM2Base : null;
        const totalHectareas = totalM2Base > 0 ? totalM2Base / 10000 : null;

        // Actualizar folio con TODOS los datos editados
        folio.solicitante = solicitante;
        folio.apellido_paterno = apPaterno || null;
        folio.apellido_materno = apMaterno || null;
        folio.numero_inmueble = 1; // Ya no se usa individual, se usa array
        folio.m2 = totalM2;
        folio.hectareas = totalHectareas;
        folio.inmuebles = inmuebles;

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


function procesarFoliosMasivos() {
    const texto = $('#folios-masivos').val().trim();
    if (!texto) {
        Swal.fire('Atención', 'Ingrese la lista de folios', 'warning');
        return;
    }

    // Dividir por saltos de línea o comas
    let folios = texto.split(/[\n,;]+/).map(f => f.trim()).filter(f => f);

    // Eliminar duplicados
    const foliosUnicos = [...new Set(folios)];
    const duplicadosCount = folios.length - foliosUnicos.length;

    // Mostrar alerta si hay duplicados
    if (duplicadosCount > 0) {
        Swal.fire({
            icon: 'info',
            title: 'Folios duplicados detectados',
            html: `Se encontraron <strong>${duplicadosCount} folios duplicados</strong> en la lista.<br><br>
                   Se procesarán solo <strong>${foliosUnicos.length} folios únicos</strong>.`,
            confirmButtonText: 'Continuar',
            confirmButtonColor: '#17a2b8'
        });
    }

    // Usar solo folios únicos
    folios = foliosUnicos;

    if (folios.length < 11 || folios.length > 150) {
        Swal.fire('Error', 'Debe ingresar entre 11 y 150 folios únicos', 'error');
        return;
    }

    $('#resultado-masivos').html('<p><i class="fas fa-spinner fa-spin"></i> Procesando ' + folios.length + ' folios únicos...</p>');
    $('#btn-procesar-masivos').prop('disabled', true);

    // Llamar al backend
    $.ajax({
        url: '{{ route("planos.crear.buscar-folios-masivos") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: { folios: texto },
        success: function(response) {
            $('#btn-procesar-masivos').prop('disabled', false);
            mostrarResultadosMasivos(response);
        },
        error: function(xhr) {
            $('#btn-procesar-masivos').prop('disabled', false);
            Swal.fire('Error', 'Error al procesar folios: ' + (xhr.responseJSON?.message || 'Error desconocido'), 'error');
            $('#resultado-masivos').html('');
        }
    });
}

function mostrarResultadosMasivos(response) {
    const { encontrados, noEncontrados, yaUsados, resumen } = response;

    let html = '';

    // Resumen
    html += '<div class="alert alert-info">';
    html += '<strong>Resumen:</strong> ';
    html += resumen.encontrados + ' encontrados';
    if (resumen.noEncontrados > 0) {
        html += ', <span class="text-danger">' + resumen.noEncontrados + ' no encontrados</span>';
    }
    if (resumen.yaUsados > 0) {
        html += ', <span class="text-warning">' + resumen.yaUsados + ' ya usados</span>';
    }
    if (resumen.duplicadosEnLista > 0) {
        html += ', <span class="text-info">' + resumen.duplicadosEnLista + ' duplicados eliminados</span>';
    }
    html += '</div>';

    // Alerta de duplicados eliminados (si los hay)
    if (resumen.duplicadosEnLista > 0) {
        html += '<div class="alert alert-info py-2">';
        html += '<i class="fas fa-info-circle"></i> ';
        html += '<strong>' + resumen.duplicadosEnLista + ' folios duplicados</strong> fueron eliminados de la lista automáticamente.';
        html += '</div>';
    }

    // Alertas de no encontrados
    if (noEncontrados.length > 0) {
        html += '<div class="alert alert-danger py-2">';
        html += '<strong>No encontrados en Matrix:</strong> ' + noEncontrados.join(', ');
        html += '</div>';
    }

    // Alertas de ya usados
    if (yaUsados.length > 0) {
        html += '<div class="alert alert-warning py-2">';
        html += '<strong>Ya usados en otros planos:</strong> ' + yaUsados.join(', ');
        html += '<br><small>Estos folios serán excluidos automáticamente</small>';
        html += '</div>';
    }

    // Filtrar folios válidos (encontrados y no usados)
    const foliosValidos = encontrados.filter(f => !f.yaUsado);

    if (foliosValidos.length === 0) {
        html += '<div class="alert alert-danger">';
        html += 'No hay folios válidos para procesar. Todos están ya usados o no fueron encontrados.';
        html += '</div>';
        $('#resultado-masivos').html(html);
        return;
    }

    // Guardar en wizardData
    wizardData.folios = foliosValidos;
    wizardData.cantidadFolios = foliosValidos.length;

    // Detectar tipo predominante
    const tipos = foliosValidos.map(f => f.tipo_inmueble);
    const tipoHijuela = tipos.filter(t => t === 'HIJUELA').length;
    const tipoSitio = tipos.length - tipoHijuela;
    const tipoPredominante = tipoHijuela >= tipoSitio ? 'HIJUELA' : 'SITIO';
    const esRural = tipoPredominante === 'HIJUELA';

    // Determinar tipo de plano
    wizardData.tipoPlano = 'S' + (esRural ? 'R' : 'U');

    html += '<hr>';
    html += '<h6><i class="fas fa-list"></i> ' + foliosValidos.length + ' folios válidos - Tipo: ' + tipoPredominante + '</h6>';

    // Tabla de folios
    html += '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';
    html += '<table class="table table-sm table-bordered">';
    html += '<thead class="thead-light sticky-top">';
    html += '<tr>';
    html += '<th>#</th>';
    html += '<th>Folio</th>';
    html += '<th>Solicitante</th>';
    html += '<th>Comuna</th>';
    html += '<th>Cant.</th>';
    html += '<th colspan="' + (esRural ? 2 : 1) + '">Medidas por ' + (esRural ? 'Hijuela' : 'Sitio') + '</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';

    foliosValidos.forEach((folio, index) => {
        const nombreCompleto = (folio.solicitante + ' ' + (folio.apellido_paterno || '') + ' ' + (folio.apellido_materno || '')).trim();

        html += '<tr class="folio-row-masivo" data-index="' + index + '">';
        html += '<td>' + (index + 1) + '</td>';
        html += '<td><strong>' + folio.folio + '</strong></td>';
        html += '<td>' + nombreCompleto + '</td>';
        html += '<td>' + folio.comuna + '</td>';
        html += '<td>';
        html += '<select class="form-control form-control-sm cantidad-masivo" data-index="' + index + '" style="width: 80px;">';
        html += '<option value="1">1</option>';
        html += '<option value="2">2</option>';
        html += '<option value="3">3</option>';
        html += '<option value="4">4</option>';
        html += '<option value="5">5</option>';
        html += '<option value="custom">Más...</option>';
        html += '</select>';
        html += '<input type="number" class="form-control form-control-sm cantidad-custom-masivo mt-1" data-index="' + index + '" ';
        html += 'min="6" max="20" placeholder="6+" style="width: 80px; display: none;">';
        html += '</td>';
        // Columnas de medidas (se llenarán dinámicamente)
        html += '<td colspan="' + (esRural ? 2 : 1) + '" class="p-0">';
        html += '<div id="medidas-masivo-' + index + '" class="medidas-container-masivo">';
        // Por defecto 1 inmueble
        html += generarCamposMedidasMasivo(index, 1, esRural, tipoPredominante);
        html += '</div>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody>';
    html += '</table>';
    html += '</div>';

    // Datos del plano (desde el primer folio)
    const primerFolio = foliosValidos[0];
    html += '<hr>';
    html += '<h6>Datos del Plano (desde primer folio)</h6>';
    html += '<div class="row">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Comuna</label>';
    html += '<input type="text" class="form-control" id="comuna-masivo" value="' + primerFolio.comuna + '" readonly>';
    html += '</div></div>';
    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Responsable <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control" id="responsable-masivo" value="' + (primerFolio.responsable || '') + '" required>';
    html += '</div></div>';
    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Proyecto <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control" id="proyecto-masivo" value="' + (primerFolio.proyecto || '') + '" required>';
    html += '</div></div>';
    html += '</div>';

    $('#resultado-masivos').html(html);

    // Guardar tipo para uso posterior
    wizardData.esRuralMasivo = esRural;
    wizardData.tipoInmuebleMasivo = tipoPredominante;

    // Obtener código de comuna
    const comunaBiobio = Object.entries(comunasBiobio).find(([cod, nom]) => nom === primerFolio.comuna);
    wizardData.codigoComunaMasivo = comunaBiobio ? comunaBiobio[0] : '000';

    // Listener para cambio de cantidad - regenerar campos
    $('.cantidad-masivo').on('change', function() {
        const index = $(this).data('index');
        const value = $(this).val();

        if (value === 'custom') {
            // Mostrar input personalizado
            $(`.cantidad-custom-masivo[data-index="${index}"]`).show().focus();
            $(`#medidas-masivo-${index}`).html('<p class="text-muted p-2 mb-0"><small>Ingrese cantidad...</small></p>');
        } else {
            // Ocultar input personalizado y generar campos
            $(`.cantidad-custom-masivo[data-index="${index}"]`).hide().val('');
            const cantidad = parseInt(value);
            const html = generarCamposMedidasMasivo(index, cantidad, esRural, tipoPredominante);
            $(`#medidas-masivo-${index}`).html(html);
            attachListenersMedidasMasivo(index, esRural);
        }
        verificarCompletitudMasivos();
    });

    // Listener para cantidad personalizada
    $('.cantidad-custom-masivo').on('change', function() {
        const index = $(this).data('index');
        const cantidad = parseInt($(this).val());
        if (cantidad >= 6 && cantidad <= 20) {
            const html = generarCamposMedidasMasivo(index, cantidad, esRural, tipoPredominante);
            $(`#medidas-masivo-${index}`).html(html);
            attachListenersMedidasMasivo(index, esRural);
            verificarCompletitudMasivos();
        }
    });

    // Attach listeners iniciales para cada folio
    foliosValidos.forEach((folio, index) => {
        attachListenersMedidasMasivo(index, esRural);
    });

    // Listener para validar completitud en campos del plano
    $('#responsable-masivo, #proyecto-masivo').on('input', function() {
        verificarCompletitudMasivos();
    });

    // Habilitar botón continuar (deshabilitado hasta completar)
    $('#btn-continuar-confirmacion').prop('disabled', true);
    verificarCompletitudMasivos();

    // Actualizar display de correlativo
    actualizarDisplayCorrelativo(wizardData.codigoComunaMasivo, wizardData.tipoPlano);
}

function generarCamposMedidasMasivo(folioIndex, cantidad, esRural, tipoInmueble) {
    let html = '<div class="p-2">';

    for (let i = 0; i < cantidad; i++) {
        html += '<div class="d-flex align-items-center mb-1' + (i > 0 ? ' border-top pt-1' : '') + '">';
        html += '<small class="mr-2 text-muted" style="min-width: 50px;">#' + (i + 1) + '</small>';

        // M² - Requerido
        html += '<input type="text" class="form-control form-control-sm m2-masivo" ';
        html += 'data-folio="' + folioIndex + '" data-inmueble="' + i + '" ';
        html += 'placeholder="M²" style="width: 120px;" inputmode="numeric">';

        // Mostrar conversión a Hectáreas
        html += '<small class="ml-2 text-muted conversion-ha-masivo" ';
        html += 'data-folio="' + folioIndex + '" data-inmueble="' + i + '"></small>';

        html += '</div>';
    }

    html += '</div>';
    return html;
}

function attachListenersMedidasMasivo(folioIndex, esRural) {
    // Mostrar conversión automática M² → Hectáreas
    $(`.m2-masivo[data-folio="${folioIndex}"]`).off('input').on('input', function() {
        const m2Input = $(this).val();
        const folioIdx = $(this).data('folio');
        const inmuebleIdx = $(this).data('inmueble');
        const conversionSpan = $(`.conversion-ha-masivo[data-folio="${folioIdx}"][data-inmueble="${inmuebleIdx}"]`);

        if (m2Input && m2Input.trim() !== '') {
            const m2 = normalizarNumeroJS(m2Input);
            if (m2 !== null && !isNaN(m2) && m2 > 0) {
                const ha = m2 / 10000;
                conversionSpan.text('= ' + formatNumber(ha, 2) + ' ha');
            } else {
                conversionSpan.text('');
            }
        } else {
            conversionSpan.text('');
        }

        verificarCompletitudMasivos();
    });
}

function verificarCompletitudMasivos() {
    let completo = true;

    // Verificar responsable y proyecto
    if (!$('#responsable-masivo').val()?.trim() || !$('#proyecto-masivo').val()?.trim()) {
        completo = false;
    }

    // Verificar que cada inmueble tenga M²
    $('.m2-masivo').each(function() {
        const m2 = $(this).val()?.trim();

        // Debe tener M² ingresado
        if (!m2) {
            completo = false;
            return false; // break del each
        }
    });

    $('#btn-continuar-confirmacion').prop('disabled', !completo);
}

function recolectarFoliosMasivos() {
    const esRural = wizardData.esRuralMasivo;
    const tipoInmueble = wizardData.tipoInmuebleMasivo;

    // Verificar datos del plano
    const responsable = $('#responsable-masivo').val()?.trim();
    const proyecto = $('#proyecto-masivo').val()?.trim();
    const comuna = $('#comuna-masivo').val()?.trim();

    if (!responsable || !proyecto) {
        Swal.fire('Error', 'Debe completar Responsable y Proyecto', 'warning');
        return false;
    }

    let errores = [];

    // Recolectar datos de cada folio
    wizardData.folios.forEach((folio, index) => {
        // Obtener cantidad (puede ser del select o del input custom)
        const selectVal = $(`.cantidad-masivo[data-index="${index}"]`).val();
        let cantidad;
        if (selectVal === 'custom') {
            cantidad = parseInt($(`.cantidad-custom-masivo[data-index="${index}"]`).val()) || 1;
        } else {
            cantidad = parseInt(selectVal) || 1;
        }

        // Recolectar inmuebles individuales
        const inmuebles = [];
        let totalM2Base = 0; // Acumular todo en M² como unidad base

        for (let i = 0; i < cantidad; i++) {
            // Leer solo M²
            const m2Input = $(`.m2-masivo[data-folio="${index}"][data-inmueble="${i}"]`).val();

            const m2 = m2Input ? normalizarNumeroJS(m2Input) : 0;

            // Validar que tenga M²
            if (!m2 || isNaN(m2) || m2 <= 0) {
                errores.push(`Folio ${folio.folio}: ${tipoInmueble} #${i + 1} debe tener M²`);
                continue;
            }

            const inmueble = {
                numero_inmueble: i + 1,
                tipo_inmueble: tipoInmueble,
                m2: m2
            };

            // Sumar a total en M²
            totalM2Base += m2;

            inmuebles.push(inmueble);
        }

        // Calcular totales: M² base y su equivalente en Hectáreas
        folio.m2 = totalM2Base > 0 ? totalM2Base : null;
        folio.hectareas = totalM2Base > 0 ? totalM2Base / 10000 : null;
        folio.tipo_inmueble = tipoInmueble;
        folio.inmuebles = inmuebles;
        folio.numero_inmueble = cantidad;

        // Datos del plano (solo en primer folio)
        if (index === 0) {
            folio.comuna = comuna;
            folio.codigo_comuna = wizardData.codigoComunaMasivo;
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
        // Preguntar cantidad exacta (2-10)
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
    } else if (wizardData.cantidadTipo === 'masivo') {
        // Preguntar cantidad exacta (11-150)
        html += '<div class="form-group">';
        html += '<label>¿Cuántos folios exactamente? (11-150)</label>';
        html += '<div class="input-group" style="max-width: 200px;">';
        html += '<input type="number" class="form-control" id="cantidad-exacta-manual-masivo" min="11" max="150" placeholder="11-150">';
        html += '<div class="input-group-append">';
        html += '<span class="input-group-text">folios</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<div id="forms-manuales"></div>';
    }

    $('#contenedor-folios').html(html);
    $('#titulo-folios').text('Paso 4: Ingreso Manual de Folios');

    if (wizardData.cantidadTipo === '1') {
        // Para 1 folio, habilitar botón continuar inmediatamente
        $('#btn-continuar-confirmacion').prop('disabled', false);
    } else if (wizardData.cantidadTipo === 'multiple') {
        $('#cantidad-exacta-manual').on('change', function() {
            const cantidad = parseInt($(this).val());
            if (cantidad) {
                wizardData.cantidadFolios = cantidad;
                // Limpiar datos anteriores al cambiar cantidad
                wizardData.folios = [];
                wizardData.foliosCompletados = [];
                generarFormulariosMultiplesManual(cantidad, esRural, tipoInmueble);
            }
        });
    } else if (wizardData.cantidadTipo === 'masivo') {
        $('#cantidad-exacta-manual-masivo').on('change', function() {
            const cantidad = parseInt($(this).val());
            if (cantidad >= 11 && cantidad <= 150) {
                wizardData.cantidadFolios = cantidad;
                // Limpiar datos anteriores al cambiar cantidad
                wizardData.folios = [];
                wizardData.foliosCompletados = [];
                generarFormulariosMultiplesManual(cantidad, esRural, tipoInmueble);
            } else if (cantidad) {
                Swal.fire('Error', 'La cantidad debe estar entre 11 y 150', 'warning');
                $(this).val('');
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
        html += '<div class="row mb-2">';
        html += '<div class="col-md-2 d-flex align-items-center">';
        html += '<strong>' + tipoInmueble + ' #' + (i + 1) + '</strong>';
        html += '</div>';

        // M² - Requerido
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">M²</label>';
        html += '<input type="text" class="form-control form-control-sm m2-inmueble" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="Ej: 520.21 o 520,21" inputmode="decimal">';
        html += '</div>';
        html += '</div>';

        // Hectáreas - Auto calculado (readonly)
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">Hectáreas <small class="text-muted">(auto)</small></label>';
        html += '<input type="text" class="form-control form-control-sm bg-light ha-inmueble-manual" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0,00" readonly style="cursor: not-allowed;">';
        html += '</div>';
        html += '</div>';

        // Espaciador
        html += '<div class="col-md-2"></div>';

        html += '</div>';
    }

    html += '</div>';
    $(`#medidas-inmuebles-container-${folioIndex}`).html(html);

    // Agregar listener para conversión automática M² → Hectáreas
    $(`.m2-inmueble[data-folio="${folioIndex}"]`).on('input', function() {
        const m2Input = $(this).val();
        const folioIdx = $(this).data('folio');
        const inmuebleIdx = $(this).data('inmueble');
        const haInput = $(`.ha-inmueble-manual[data-folio="${folioIdx}"][data-inmueble="${inmuebleIdx}"]`);

        if (m2Input && m2Input.trim() !== '') {
            const m2 = normalizarNumeroJS(m2Input);
            if (m2 !== null && !isNaN(m2) && m2 > 0) {
                const ha = m2 / 10000;
                haInput.val(formatNumber(ha, 2));
            } else {
                haInput.val('');
            }
        } else {
            haInput.val('');
        }
    });
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
    // Inicializar tracking de folio actual y limpiar datos anteriores
    wizardData.folioActualIndex = 0;
    wizardData.foliosCompletados = [];
    wizardData.folios = [];
    wizardData.esRuralManual = esRural;
    wizardData.tipoInmuebleManual = tipoInmueble;

    // Ocultar botones principales durante el wizard
    $('#botones-folios-principales').hide();

    // Mostrar el primer folio
    mostrarFolioActualManual();
}

function mostrarFolioActualManual() {
    const index = wizardData.folioActualIndex;
    const total = wizardData.cantidadFolios;
    const progreso = Math.round(((index) / total) * 100);
    const esRural = wizardData.esRuralManual;
    const tipoInmueble = wizardData.tipoInmuebleManual;

    let html = '';

    // Barra de progreso
    html += '<div class="mb-3">';
    html += '<div class="d-flex justify-content-between align-items-center mb-2">';
    html += '<span class="font-weight-bold text-warning"><i class="fas fa-edit"></i> Folio ' + (index + 1) + ' de ' + total + '</span>';
    html += '<span class="badge badge-warning">' + progreso + '% completado</span>';
    html += '</div>';
    html += '<div class="progress" style="height: 8px;">';
    html += '<div class="progress-bar bg-warning" style="width: ' + progreso + '%"></div>';
    html += '</div>';
    html += '</div>';

    // Resumen de folios completados
    if (wizardData.foliosCompletados.length > 0) {
        html += '<div class="mb-3">';
        html += '<small class="text-muted font-weight-bold">Folios completados:</small>';
        html += '<div class="list-group list-group-flush mt-1">';
        wizardData.foliosCompletados.forEach((completado, i) => {
            if (!completado) return;
            const cantidadInm = completado.cantidadInmuebles || 1;
            const tipoLabel = completado.tipo === 'HIJUELA' ? 'hijuela' : 'sitio';
            const tipoPlural = cantidadInm > 1 ? (completado.tipo === 'HIJUELA' ? 'hijuelas' : 'sitios') : tipoLabel;
            html += '<div class="list-group-item list-group-item-warning py-2 px-3">';
            html += '<div class="d-flex justify-content-between align-items-center">';
            html += '<span><i class="fas fa-check-circle text-warning mr-2"></i>';
            html += '<strong>Folio #' + (i + 1) + ':</strong> ' + (completado.folio || 'S/F') + ' - ' + completado.solicitante + '</span>';
            html += '<span class="badge badge-light">' + cantidadInm + ' ' + tipoPlural + '</span>';
            html += '</div>';
            html += '</div>';
        });
        html += '</div>';
        html += '</div>';
    }

    // Card del folio actual
    html += '<div class="card border-warning">';
    html += '<div class="card-header bg-warning">';
    html += '<h6 class="mb-0"><i class="fas fa-edit"></i> Folio Manual #' + (index + 1) + '</h6>';
    html += '</div>';
    html += '<div class="card-body">';

    // Formulario del folio (simplificado, sin card extra)
    // FILA 1: Folio y datos personales
    html += '<div class="row">';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>N° Folio</label>';
    html += '<input type="text" class="form-control folio-manual-multiple" data-index="' + index + '" placeholder="Opcional">';
    html += '</div></div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Solicitante <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control solicitante-manual-multiple" data-index="' + index + '" required>';
    html += '</div></div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Ap. Paterno</label>';
    html += '<input type="text" class="form-control ap-paterno-manual-multiple" data-index="' + index + '">';
    html += '</div></div>';

    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Ap. Materno</label>';
    html += '<input type="text" class="form-control ap-materno-manual-multiple" data-index="' + index + '">';
    html += '</div></div>';
    html += '</div>';

    // Datos del plano (solo en primer folio)
    if (index === 0) {
        html += '<hr><h6>Datos del Plano (aplican a todos los folios)</h6>';
        html += '<div class="row">';
        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Comuna <span class="text-danger">*</span></label>';
        html += '<select class="form-control comuna-manual-multiple" data-index="' + index + '" required>';
        html += '<option value="">Seleccionar...</option>';
        // Agregar comunas desde variable JavaScript
        Object.entries(comunasBiobio).forEach(([codigo, nombre]) => {
            html += '<option value="' + codigo + '">' + nombre + '</option>';
        });
        html += '</select>';
        html += '</div></div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Responsable <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control responsable-manual-multiple" data-index="' + index + '" required>';
        html += '</div></div>';

        html += '<div class="col-md-4">';
        html += '<div class="form-group">';
        html += '<label>Proyecto <span class="text-danger">*</span></label>';
        html += '<input type="text" class="form-control proyecto-manual-multiple" data-index="' + index + '" required>';
        html += '</div></div>';
        html += '</div>';
    }

    // Selector de cantidad de hijuelas/sitios
    const labelCantidad = esRural ? 'hijuelas' : 'sitios';
    html += '<hr><h6>Cantidad de ' + labelCantidad + '</h6>';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group mb-0">';
    html += '<label>¿Cuántas ' + labelCantidad + '? <span class="text-danger">*</span></label>';
    html += '<select class="form-control cantidad-inmuebles-manual-multiple" data-index="' + index + '" required>';
    html += '<option value="">Seleccionar...</option>';
    html += '<option value="1">1 ' + tipoInmueble.toLowerCase() + '</option>';
    html += '<option value="2">2 ' + labelCantidad + '</option>';
    html += '<option value="3">3 ' + labelCantidad + '</option>';
    html += '<option value="4">4 ' + labelCantidad + '</option>';
    html += '<option value="5">5 ' + labelCantidad + '</option>';
    html += '<option value="custom">Más...</option>';
    html += '</select>';
    html += '</div></div>';
    html += '<div class="col-md-3" id="cantidad-custom-container-manual-multiple-' + index + '" style="display: none;">';
    html += '<div class="form-group mb-0">';
    html += '<label>Cantidad exacta</label>';
    html += '<input type="number" class="form-control cantidad-custom-manual-multiple" data-index="' + index + '" min="6" placeholder="6+">';
    html += '</div></div>';
    html += '</div>';

    // Contenedor para medidas
    html += '<div id="medidas-inmuebles-container-manual-multiple-' + index + '" class="mb-3"></div>';

    html += '</div>';
    html += '</div>';

    // Botones de navegación
    html += '<div class="d-flex justify-content-between mt-3">';
    if (index > 0) {
        html += '<button type="button" class="btn btn-secondary" id="btn-folio-anterior-manual">';
        html += '<i class="fas fa-arrow-left"></i> Anterior</button>';
    } else {
        html += '<div></div>';
    }
    html += '<button type="button" class="btn btn-warning" id="btn-folio-siguiente-manual" disabled>';
    if (index < total - 1) {
        html += 'Siguiente <i class="fas fa-arrow-right"></i></button>';
    } else {
        html += 'Finalizar <i class="fas fa-check"></i></button>';
    }
    html += '</div>';

    const container = $('#forms-manuales');
    if (container.length === 0) {
        console.error('ERROR: #forms-manuales container not found!');
        return;
    }
    container.html(html);

    // Restaurar datos si existen
    if (wizardData.folios[index]) {
        const folio = wizardData.folios[index];
        $(`.folio-manual-multiple[data-index="${index}"]`).val(folio.folio || '');
        $(`.solicitante-manual-multiple[data-index="${index}"]`).val(folio.solicitante || '');
        $(`.ap-paterno-manual-multiple[data-index="${index}"]`).val(folio.apellido_paterno || '');
        $(`.ap-materno-manual-multiple[data-index="${index}"]`).val(folio.apellido_materno || '');
    }

    // Event listeners
    attachListenersManualMultiple(index, esRural, tipoInmueble);

    // Focus
    setTimeout(() => {
        $(`.solicitante-manual-multiple[data-index="${index}"]`).focus();
    }, 100);
}

function attachListenersManualMultiple(index, esRural, tipoInmueble) {
    const labelCantidad = esRural ? 'hijuelas' : 'sitios';

    // Listener para selector de cantidad
    $(`.cantidad-inmuebles-manual-multiple[data-index="${index}"]`).on('change', function() {
        const value = $(this).val();

        if (value === 'custom') {
            $(`#cantidad-custom-container-manual-multiple-${index}`).show();
            $(`.cantidad-custom-manual-multiple[data-index="${index}"]`).prop('required', true).focus();
            $(`#medidas-inmuebles-container-manual-multiple-${index}`).html('');
        } else if (value) {
            $(`#cantidad-custom-container-manual-multiple-${index}`).hide();
            $(`.cantidad-custom-manual-multiple[data-index="${index}"]`).prop('required', false).val('');
            generarCamposMedidasManualMultiple(index, parseInt(value), esRural, tipoInmueble);
        } else {
            $(`#cantidad-custom-container-manual-multiple-${index}`).hide();
            $(`#medidas-inmuebles-container-manual-multiple-${index}`).html('');
        }
        verificarCompletitudFolioManual(index);
    });

    // Listener para cantidad custom
    $(`.cantidad-custom-manual-multiple[data-index="${index}"]`).on('change', function() {
        const cantidad = parseInt($(this).val());
        if (cantidad >= 6) {
            generarCamposMedidasManualMultiple(index, cantidad, esRural, tipoInmueble);
        }
        verificarCompletitudFolioManual(index);
    });

    // Listener para validar duplicados en folio (opcional pero no puede repetirse)
    $(`.folio-manual-multiple[data-index="${index}"]`).on('blur', function() {
        const folio = $(this).val().trim();

        // Solo validar si ingresó un folio (es opcional)
        if (!folio) return;

        // Verificar duplicados en folios ya completados
        const duplicado = wizardData.foliosCompletados.find(f => f.folio && f.folio === folio);
        if (duplicado) {
            const posicionDuplicado = wizardData.foliosCompletados.indexOf(duplicado) + 1;
            Swal.fire({
                icon: 'warning',
                title: 'Folio duplicado',
                html: `Este folio ya fue agregado en el <strong>Folio #${posicionDuplicado}</strong> de este plano.<br><br>Por favor, ingrese un folio diferente o déjelo vacío.`,
                confirmButtonText: 'Entendido'
            });
            $(this).val(''); // Limpiar el campo
            $(this).focus(); // Volver a poner foco
        }
    });

    // Listener para campos requeridos
    $(`.solicitante-manual-multiple[data-index="${index}"]`).on('input', function() {
        verificarCompletitudFolioManual(index);
    });

    // Listeners para datos del plano (solo primer folio)
    if (index === 0) {
        $(`.comuna-manual-multiple[data-index="${index}"]`).on('change', function() {
            verificarCompletitudFolioManual(index);
        });
        $(`.responsable-manual-multiple[data-index="${index}"]`).on('input', function() {
            verificarCompletitudFolioManual(index);
        });
        $(`.proyecto-manual-multiple[data-index="${index}"]`).on('input', function() {
            verificarCompletitudFolioManual(index);
        });
    }

    // Botones navegación
    $('#btn-folio-anterior-manual').on('click', retrocederFolioManual);
    $('#btn-folio-siguiente-manual').on('click', avanzarSiguienteFolioManual);

    // Verificar completitud inicial (para que el botón esté correctamente deshabilitado)
    verificarCompletitudFolioManual(index);
}

function generarCamposMedidasManualMultiple(folioIndex, cantidad, esRural, tipoInmueble) {
    let html = '<div class="border rounded p-3 bg-light">';
    html += '<h6 class="text-secondary mb-3"><i class="fas fa-ruler-combined"></i> Medidas de cada ' + tipoInmueble.toLowerCase() + '</h6>';

    for (let i = 0; i < cantidad; i++) {
        html += '<div class="row mb-2">';
        html += '<div class="col-md-2 d-flex align-items-center">';
        html += '<strong>' + tipoInmueble + ' #' + (i + 1) + '</strong>';
        html += '</div>';

        // M² - Requerido
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">M²</label>';
        html += '<input type="text" class="form-control form-control-sm m2-inmueble-manual-multiple" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0,00" inputmode="decimal" onkeypress="return validarNumeroDecimal(event)">';
        html += '</div></div>';

        // Hectáreas - Auto calculado (readonly)
        html += '<div class="col-md-4">';
        html += '<div class="form-group mb-0">';
        html += '<label class="small mb-1">Hectáreas <small class="text-muted">(auto)</small></label>';
        html += '<input type="text" class="form-control form-control-sm bg-light ha-inmueble-manual-multiple" data-folio="' + folioIndex + '" data-inmueble="' + i + '" placeholder="0,00" readonly style="cursor: not-allowed;">';
        html += '</div>';
        html += '</div>';

        // Espaciador
        html += '<div class="col-md-2"></div>';

        html += '</div>';
    }

    html += '</div>';

    $(`#medidas-inmuebles-container-manual-multiple-${folioIndex}`).html(html);

    // Listener para conversión automática M² → Hectáreas y validar
    $(`.m2-inmueble-manual-multiple[data-folio="${folioIndex}"]`).on('input', function() {
        const m2Input = $(this).val();
        const folioIdx = $(this).data('folio');
        const inmuebleIdx = $(this).data('inmueble');
        const haInput = $(`.ha-inmueble-manual-multiple[data-folio="${folioIdx}"][data-inmueble="${inmuebleIdx}"]`);

        if (m2Input && m2Input.trim() !== '') {
            const m2 = normalizarNumeroJS(m2Input);
            if (m2 !== null && !isNaN(m2) && m2 > 0) {
                const ha = m2 / 10000;
                haInput.val(formatNumber(ha, 2));
            } else {
                haInput.val('');
            }
        } else {
            haInput.val('');
        }

        verificarCompletitudFolioManual(folioIndex);
    });
}

function verificarCompletitudFolioManual(index) {
    // Verificar solicitante
    const solicitante = $(`.solicitante-manual-multiple[data-index="${index}"]`).val()?.trim();
    if (!solicitante) {
        $('#btn-folio-siguiente-manual').prop('disabled', true);
        return;
    }

    // Si es primer folio, verificar datos del plano
    if (index === 0) {
        const comuna = $(`.comuna-manual-multiple[data-index="${index}"]`).val();
        const responsable = $(`.responsable-manual-multiple[data-index="${index}"]`).val()?.trim();
        const proyecto = $(`.proyecto-manual-multiple[data-index="${index}"]`).val()?.trim();

        if (!comuna || !responsable || !proyecto) {
            $('#btn-folio-siguiente-manual').prop('disabled', true);
            return;
        }
    }

    // Verificar cantidad seleccionada
    const cantidadSelector = $(`.cantidad-inmuebles-manual-multiple[data-index="${index}"]`).val();
    if (!cantidadSelector) {
        $('#btn-folio-siguiente-manual').prop('disabled', true);
        return;
    }

    // Obtener cantidad real
    let cantidad;
    if (cantidadSelector === 'custom') {
        cantidad = parseInt($(`.cantidad-custom-manual-multiple[data-index="${index}"]`).val());
    } else {
        cantidad = parseInt(cantidadSelector);
    }

    if (!cantidad || cantidad < 1) {
        $('#btn-folio-siguiente-manual').prop('disabled', true);
        return;
    }

    // Verificar que cada inmueble tenga M²
    let todosCompletos = true;
    for (let i = 0; i < cantidad; i++) {
        const m2 = $(`.m2-inmueble-manual-multiple[data-folio="${index}"][data-inmueble="${i}"]`).val();

        // Debe tener M²
        if (!m2) {
            todosCompletos = false;
            break;
        }
    }

    $('#btn-folio-siguiente-manual').prop('disabled', !todosCompletos);
}

function avanzarSiguienteFolioManual() {
    const index = wizardData.folioActualIndex;
    const esRural = wizardData.esRuralManual;
    const tipoInmueble = wizardData.tipoInmuebleManual;

    // Recolectar datos del folio actual
    const folio = $(`.folio-manual-multiple[data-index="${index}"]`).val()?.trim() || null;
    const solicitante = $(`.solicitante-manual-multiple[data-index="${index}"]`).val()?.trim();
    const apPaterno = $(`.ap-paterno-manual-multiple[data-index="${index}"]`).val()?.trim();
    const apMaterno = $(`.ap-materno-manual-multiple[data-index="${index}"]`).val()?.trim();

    // Obtener cantidad de inmuebles
    const cantidadSelector = $(`.cantidad-inmuebles-manual-multiple[data-index="${index}"]`).val();
    let cantidad;
    if (cantidadSelector === 'custom') {
        cantidad = parseInt($(`.cantidad-custom-manual-multiple[data-index="${index}"]`).val());
    } else {
        cantidad = parseInt(cantidadSelector);
    }

    // Recolectar inmuebles
    const inmuebles = [];
    let totalM2Base = 0; // Acumular todo en M² como unidad base

    for (let i = 0; i < cantidad; i++) {
        const m2Input = $(`.m2-inmueble-manual-multiple[data-folio="${index}"][data-inmueble="${i}"]`).val();

        const m2 = m2Input ? normalizarNumeroJS(m2Input) : 0;

        // Validar que tenga M²
        if (!m2 || isNaN(m2) || m2 <= 0) {
            continue; // Skip este inmueble vacío
        }

        const inmueble = {
            numero_inmueble: i + 1,
            tipo_inmueble: tipoInmueble,
            m2: m2
        };

        // Sumar a total en M²
        totalM2Base += m2;

        inmuebles.push(inmueble);
    }

    // Guardar folio en wizardData
    wizardData.folios[index] = {
        folio: folio,
        solicitante: solicitante,
        apellido_paterno: apPaterno || null,
        apellido_materno: apMaterno || null,
        tipo_inmueble: tipoInmueble,
        m2: totalM2Base > 0 ? totalM2Base : null,
        hectareas: totalM2Base > 0 ? totalM2Base / 10000 : null,
        inmuebles: inmuebles,
        is_from_matrix: false
    };

    // Datos del plano (solo primer folio)
    if (index === 0) {
        const codigoComuna = $(`.comuna-manual-multiple[data-index="${index}"]`).val();
        const comunaNombre = $(`.comuna-manual-multiple[data-index="${index}"] option:selected`).text();
        wizardData.folios[index].codigo_comuna = codigoComuna;
        wizardData.folios[index].comuna = comunaNombre;
        wizardData.folios[index].responsable = $(`.responsable-manual-multiple[data-index="${index}"]`).val()?.trim();
        wizardData.folios[index].proyecto = $(`.proyecto-manual-multiple[data-index="${index}"]`).val()?.trim();
    }

    // Guardar en completados para resumen
    wizardData.foliosCompletados[index] = {
        folio: folio,
        solicitante: solicitante,
        tipo: tipoInmueble,
        cantidadInmuebles: cantidad
    };

    // Avanzar o finalizar
    if (index < wizardData.cantidadFolios - 1) {
        wizardData.folioActualIndex++;
        mostrarFolioActualManual();
    } else {
        // Todos completados
        $('#btn-continuar-confirmacion').prop('disabled', false);

        // Mostrar botones principales nuevamente
        $('#botones-folios-principales').show();

        Swal.fire({
            icon: 'success',
            title: '¡Todos los folios completados!',
            text: 'Ahora puedes continuar a la confirmación',
            confirmButtonText: 'Continuar'
        });
    }
}

function retrocederFolioManual() {
    if (wizardData.folioActualIndex > 0) {
        wizardData.folioActualIndex--;
        mostrarFolioActualManual();
    }
}

// =====================================================
// MOSTRAR CONFIRMACIÓN
// =====================================================
function mostrarConfirmacion() {
    // Recolectar todos los folios según origen y tipo
    if (wizardData.origenFolios === 'matrix') {
        // Verificar si es masivo
        if (wizardData.cantidadTipo === 'masivo') {
            if (!recolectarFoliosMasivos()) {
                return;
            }
        } else {
            // Primero recolectar las medidas de los formularios
            if (!recolectarMedidasMatrix()) {
                return;
            }
            // Luego validar que todos están completos
            if (!validarFoliosMatrixCompleto()) {
                return;
            }
        }
    } else if (wizardData.origenFolios === 'manual') {
        if (!recolectarFoliosManuales()) {
            return;
        }
    }

    // Calcular totales en M² como unidad base
    let totalM2 = 0;
    wizardData.folios.forEach(folio => {
        if (folio.m2 && folio.m2 > 0) {
            // Sumar M² directamente
            totalM2 += parseFloat(folio.m2);
        }
    });

    // Convertir total M² a Hectáreas para mostrar
    let totalHectareas = totalM2 > 0 ? totalM2 / 10000 : 0;

    // Obtener código comuna
    let codigoComuna = '';
    if (wizardData.origenFolios === 'matrix') {
        if (wizardData.cantidadTipo === 'masivo') {
            codigoComuna = wizardData.codigoComunaMasivo || wizardData.folios[0].codigo_comuna || '000';
        } else {
            codigoComuna = wizardData.folios[0].codigo_comuna || '000';
        }
    } else {
        // Para manual: verificar si los datos están en wizardData (wizard múltiple) o en DOM (1 folio)
        if (wizardData.folios[0] && wizardData.folios[0].codigo_comuna) {
            codigoComuna = wizardData.folios[0].codigo_comuna;
        } else {
            codigoComuna = $('#comuna-manual').val() || '000';
        }
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

    // Contar cantidad de folios
    const cantidadFolios = wizardData.folios.length;
    $('#confirm-cantidad-folios').text(cantidadFolios);

    // Contar total de inmuebles
    let totalInmuebles = 0;
    wizardData.folios.forEach(folio => {
        if (folio.inmuebles && folio.inmuebles.length > 0) {
            totalInmuebles += folio.inmuebles.length;
        } else {
            totalInmuebles += 1;
        }
    });

    // Mostrar cantidad de inmuebles con etiqueta apropiada
    const tipoInmuebleLabel = wizardData.tipoPlano.includes('R') ? 'hijuelas' : 'sitios';
    $('#confirm-total-inmuebles').text(totalInmuebles + ' ' + tipoInmuebleLabel);

    // Mostrar superficie total en ambas unidades: "XXX m² (YY,YY ha)"
    if (totalM2 > 0) {
        $('#confirm-total-superficie').text(formatNumber(totalM2, 2) + ' m² (' + formatNumber(totalHectareas, 2) + ' ha)');
    } else {
        $('#confirm-total-superficie').text('-');
    }

    // Generar lista de inmuebles
    let listaHTML = '<table class="table table-sm table-hover">';
    listaHTML += '<thead><tr>';
    listaHTML += '<th>#</th><th>Folio</th><th>Solicitante</th><th>Tipo</th>';
    if (wizardData.tipoPlano.includes('R')) {
        listaHTML += '<th>Hectáreas</th>';
    }
    listaHTML += '<th>M²</th></tr></thead><tbody>';

    let contador = 1;
    wizardData.folios.forEach((folio) => {
        const nombreCompleto = folio.solicitante + ' ' + (folio.apellido_paterno || '') + ' ' + (folio.apellido_materno || '');

        if (folio.inmuebles && folio.inmuebles.length > 0) {
            // Mostrar cada inmueble por separado
            folio.inmuebles.forEach((inmueble) => {
                listaHTML += '<tr>';
                listaHTML += '<td>' + contador + '</td>';
                listaHTML += '<td>' + (folio.folio || '-') + '</td>';
                listaHTML += '<td>' + nombreCompleto + '</td>';
                listaHTML += '<td>' + inmueble.tipo_inmueble + ' #' + inmueble.numero_inmueble + '</td>';
                if (wizardData.tipoPlano.includes('R')) {
                    listaHTML += '<td>' + (inmueble.hectareas ? formatNumber(parseFloat(inmueble.hectareas), 2) : '-') + '</td>';
                }
                listaHTML += '<td>' + formatNumber(parseFloat(inmueble.m2), 2) + '</td>';
                listaHTML += '</tr>';
                contador++;
            });
        } else {
            // Sin desglose - mostrar folio con totales
            listaHTML += '<tr>';
            listaHTML += '<td>' + contador + '</td>';
            listaHTML += '<td>' + (folio.folio || '-') + '</td>';
            listaHTML += '<td>' + nombreCompleto + '</td>';
            listaHTML += '<td>' + (folio.tipo_inmueble || 'HIJUELA') + '</td>';
            if (wizardData.tipoPlano.includes('R')) {
                listaHTML += '<td>' + (folio.hectareas ? formatNumber(parseFloat(folio.hectareas), 2) : '-') + '</td>';
            }
            listaHTML += '<td>' + formatNumber(parseFloat(folio.m2), 2) + '</td>';
            listaHTML += '</tr>';
            contador++;
        }
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
    const esRural = wizardData.ubicacionManual === 'R';
    const tipoInmueble = esRural ? 'HIJUELA' : 'SITIO';

    // Si los datos ya fueron recolectados por el wizard (múltiples folios)
    if (wizardData.folios.length > 0 && wizardData.folios[0] && wizardData.folios[0].inmuebles) {
        // Validar que el primer folio tenga los datos del plano
        const primerFolio = wizardData.folios[0];
        if (!primerFolio.codigo_comuna || !primerFolio.responsable || !primerFolio.proyecto) {
            Swal.fire('Error', 'Debes completar Comuna, Responsable y Proyecto en el primer folio', 'warning');
            return false;
        }

        // Validar que todos los folios tengan datos
        let errores = [];
        wizardData.folios.forEach((folio, index) => {
            if (!folio || !folio.solicitante) {
                errores.push(`Folio ${index + 1}: Solicitante es obligatorio`);
            }
            // Validar que tenga M²
            if (!folio.m2 || folio.m2 <= 0) {
                errores.push(`Folio ${index + 1}: Debe ingresar M²`);
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

        return true; // Datos ya recolectados y válidos
    }

    // Caso de 1 folio: leer desde DOM
    wizardData.folios = [];

    // Obtener datos comunes del plano
    const comuna = $('#comuna-manual').val();
    const responsable = $('#responsable-manual').val();
    const proyecto = $('#proyecto-manual').val();

    if (!comuna || !responsable || !proyecto) {
        Swal.fire('Error', 'Debes completar Comuna, Responsable y Proyecto', 'warning');
        return false;
    }

    // Recolectar cada folio con sus inmuebles (hijuelas/sitios)
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

        // Recolectar inmuebles (hijuelas/sitios) de este folio
        let inmuebles = [];
        let totalM2Base = 0; // Acumular todo en M² como unidad base

        $(`.m2-inmueble[data-folio="${index}"]`).each(function(inmuebleIndex) {
            const m2Valor = $(this).val();

            const m2 = m2Valor ? normalizarNumeroJS(m2Valor) : 0;

            // Validar que tenga M²
            if (!m2 || isNaN(m2) || m2 <= 0) {
                return; // Skip este inmueble vacío
            }

            const inmuebleData = {
                numero_inmueble: inmuebleIndex + 1,
                tipo_inmueble: tipoInmueble,
                m2: m2
            };

            // Sumar a total en M²
            totalM2Base += m2;

            inmuebles.push(inmuebleData);
        });

        if (inmuebles.length === 0) {
            errores.push(`Folio ${index + 1}: Debe ingresar al menos una ${tipoInmueble.toLowerCase()}`);
            return;
        }

        // Crear el folio con totales e inmuebles
        const folioData = {
            folio: folio || null,
            solicitante: solicitante,
            apellido_paterno: apPaterno || null,
            apellido_materno: apMaterno || null,
            tipo_inmueble: tipoInmueble,
            numero_inmueble: inmuebles.length, // Cantidad de inmuebles
            m2: totalM2Base > 0 ? totalM2Base : null,
            hectareas: totalM2Base > 0 ? totalM2Base / 10000 : null,
            is_from_matrix: false,
            comuna: comuna,
            responsable: responsable,
            proyecto: proyecto,
            inmuebles: inmuebles // Array con el desglose
        };

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
    // Convertir a número con decimales fijos
    const numStr = num.toFixed(decimals);

    // Separar parte entera y decimal
    const [parteEntera, parteDecimal] = numStr.split('.');

    // Agregar separador de miles a la parte entera
    const parteEnteraFormateada = parteEntera.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Retornar con coma decimal
    return parteDecimal ? `${parteEnteraFormateada},${parteDecimal}` : parteEnteraFormateada;
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
        // Para manual: verificar si los datos están en wizardData (wizard múltiple) o en DOM (1 folio)
        if (wizardData.folios[0] && wizardData.folios[0].codigo_comuna) {
            // Datos del wizard múltiple
            codigoComuna = wizardData.folios[0].codigo_comuna;
            comuna = wizardData.folios[0].comuna;
            responsable = wizardData.folios[0].responsable;
            proyecto = wizardData.folios[0].proyecto;
        } else {
            // Datos de 1 folio (DOM)
            codigoComuna = $('#comuna-manual').val();
            comuna = $('#comuna-manual option:selected').text();
            responsable = $('#responsable-manual').val();
            proyecto = $('#proyecto-manual').val();
        }
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
                is_from_matrix: wizardData.origenFolios === 'matrix',
                inmuebles: folio.inmuebles || [] // Array con desglose de hijuelas/sitios
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
