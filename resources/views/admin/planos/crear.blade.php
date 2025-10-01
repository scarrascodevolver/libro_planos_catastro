@extends('layouts.admin')

@section('title', 'Agregar Planos')

@section('page-title', 'Agregar Planos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Agregar Planos</li>
@endsection

@section('content')

<!-- Control de Numeración (SIEMPRE VISIBLE) -->
@include('admin.planos.partials.session-control')

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
    <div class="card-body text-center">
        <div class="row">
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input cantidad-radio" type="radio" name="cantidad_folios" id="cantidad_1" value="1">
                    <label class="form-check-label" for="cantidad_1">
                        <h4>1 FOLIO</h4>
                        <small class="text-muted">Formulario simple</small>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input cantidad-radio" type="radio" name="cantidad_folios" id="cantidad_multiple" value="multiple">
                    <label class="form-check-label" for="cantidad_multiple">
                        <h4>2-10 FOLIOS</h4>
                        <small class="text-muted">Paso a paso</small>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input cantidad-radio" type="radio" name="cantidad_folios" id="cantidad_masiva" value="masivo">
                    <label class="form-check-label" for="cantidad_masiva">
                        <h4>11-150 FOLIOS</h4>
                        <small class="text-muted">Importación masiva</small>
                    </label>
                </div>
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
    <div class="card-body text-center">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check form-check-lg">
                    <input class="form-check-input origen-radio" type="radio" name="origen_folios" id="origen_matrix" value="matrix">
                    <label class="form-check-label" for="origen_matrix">
                        <h4><i class="fas fa-database"></i> MATRIX</h4>
                        <small class="text-muted">Buscar folios en base de datos<br>(Saneamiento automático)</small>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-check-lg">
                    <input class="form-check-input origen-radio" type="radio" name="origen_folios" id="origen_manual" value="manual">
                    <label class="form-check-label" for="origen_manual">
                        <h4><i class="fas fa-edit"></i> MANUAL</h4>
                        <small class="text-muted">Ingreso libre de datos<br>(Fiscales y otros)</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary" id="btn-volver-cantidad">
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

        <!-- Previsualización tipo plano -->
        <div class="alert alert-info" id="preview-tipo-manual" style="display: none;">
            <h5><i class="fas fa-eye"></i> Tipo de plano a crear:</h5>
            <h3 id="preview-tipo-text">--</h3>
            <small id="preview-tipo-desc">--</small>
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

$(document).ready(function() {
    cargarNumeracionCorrelativa();
    initWizardListeners();
});

// =====================================================
// CARGAR NUMERACIÓN CORRELATIVA
// =====================================================
function cargarNumeracionCorrelativa() {
    // TODO: Llamar al backend para obtener último correlativo
    // Por ahora datos de prueba
    wizardData.ultimoCorrelativo = 29271;
    wizardData.proximoCorrelativo = 29272;

    $('#ultimo-correlativo-display').text(wizardData.ultimoCorrelativo);
    $('#proximo-correlativo-display').text(wizardData.proximoCorrelativo);
}

// =====================================================
// LISTENERS DEL WIZARD
// =====================================================
function initWizardListeners() {
    // PASO 2: Cantidad de folios
    $('.cantidad-radio').on('change', function() {
        wizardData.cantidadTipo = $(this).val();

        if (wizardData.cantidadTipo === '1') {
            wizardData.cantidadFolios = 1;
        } else if (wizardData.cantidadTipo === 'multiple') {
            // Preguntar cantidad específica después
        } else if (wizardData.cantidadTipo === 'masivo') {
            // Validar cantidad después
        }

        // Mostrar siguiente paso
        $('#card-cantidad').hide();
        $('#card-origen').show();
    });

    // Volver a cantidad
    $('#btn-volver-cantidad').on('click', function() {
        $('#card-origen').hide();
        $('#card-cantidad').show();
    });

    // PASO 3: Origen
    $('.origen-radio').on('change', function() {
        wizardData.origenFolios = $(this).val();

        $('#card-origen').hide();

        if (wizardData.origenFolios === 'manual') {
            // Ir a configuración manual
            $('#card-config-manual').show();
        } else {
            // Ir directo a búsqueda Matrix
            mostrarFormularioFolios();
        }
    });

    // Volver a origen
    $('#btn-volver-origen').on('click', function() {
        $('#card-config-manual').hide();
        $('#card-origen').show();
    });

    // PASO 4: Configuración Manual
    $('input[name="ubicacion_manual"], input[name="tipo_manual"]').on('change', function() {
        actualizarPreviewTipoManual();
    });

    $('#btn-continuar-config').on('click', function() {
        wizardData.ubicacionManual = $('input[name="ubicacion_manual"]:checked').val();
        wizardData.tipoManual = $('input[name="tipo_manual"]:checked').val();
        wizardData.tipoPlano = wizardData.tipoManual + wizardData.ubicacionManual;

        $('#card-config-manual').hide();
        mostrarFormularioFolios();
    });

    // PASO 5: Folios
    $('#btn-volver-folios').on('click', volverDesdeFolios);
    $('#btn-continuar-confirmacion').on('click', mostrarConfirmacion);

    // PASO 6: Confirmación
    $('#btn-volver-confirmacion').on('click', function() {
        $('#card-confirmacion').hide();
        $('#card-folios').show();
    });

    $('#btn-crear-plano').on('click', crearPlano);
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

        $('#preview-tipo-text').text(tipoPlano);
        $('#preview-tipo-desc').text(descripciones[tipoPlano]);
        $('#preview-tipo-manual').show();
        $('#btn-continuar-config').prop('disabled', false);
    } else {
        $('#preview-tipo-manual').hide();
        $('#btn-continuar-config').prop('disabled', true);
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
    html += '<strong>Búsqueda en Matrix:</strong> Escribe el folio y presiona Tab o Enter para buscar.';
    html += '</div>';

    // TODO: Generar inputs según cantidad
    html += '<p>TODO: Implementar búsqueda Matrix</p>';

    $('#contenedor-folios').html(html);
    $('#titulo-folios').text('Paso 4: Buscar Folios en Matrix');
}

// =====================================================
// GENERAR FORMULARIO MANUAL
// =====================================================
function generarFormularioManual() {
    let html = '<div class="alert alert-warning">';
    html += '<i class="fas fa-edit"></i> ';
    html += '<strong>Ingreso Manual:</strong> Complete todos los campos para cada folio.';
    html += '</div>';

    // TODO: Generar formularios según cantidad y tipo
    html += '<p>TODO: Implementar formulario manual</p>';

    $('#contenedor-folios').html(html);
    $('#titulo-folios').text('Paso 4: Ingreso Manual de Folios');
}

// =====================================================
// MOSTRAR CONFIRMACIÓN
// =====================================================
function mostrarConfirmacion() {
    // TODO: Validar folios

    $('#card-folios').hide();
    $('#card-confirmacion').show();

    // TODO: Llenar resumen
}

// =====================================================
// CREAR PLANO
// =====================================================
function crearPlano() {
    // TODO: Enviar datos al backend
    Swal.fire({
        title: 'Creando plano...',
        text: 'Por favor espere',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
}
</script>
@endpush
