@extends('layouts.admin')

@section('title', 'Agregar Planos')

@section('page-title', 'Agregar Planos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Agregar Planos</li>
@endsection

@section('content')

<!-- Control de Numeración -->
@include('admin.planos.partials.session-control')

<!-- Selector Tipo de Plano -->
<div class="card" id="tipo-plano-card">
    <div class="card-header bg-info">
        <h3 class="card-title">
            <i class="fas fa-plus-circle"></i>
            Paso 1: Tipo de Plano a Crear
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check form-check-lg">
                    <input class="form-check-input" type="radio" name="tipo_plano" id="tipo_matrix" value="matrix">
                    <label class="form-check-label" for="tipo_matrix">
                        <strong>PLANO MATRIX</strong><br>
                        <small class="text-muted">Folios desde base de datos Matrix (auto-completado)</small>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-check-lg">
                    <input class="form-check-input" type="radio" name="tipo_plano" id="tipo_manual" value="manual">
                    <label class="form-check-label" for="tipo_manual">
                        <strong>PLANO MANUAL</strong><br>
                        <small class="text-muted">Ingreso libre (fiscales y otros casos especiales)</small>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configuración Plano -->
<div class="card" id="configuracion-card" style="display: none;">
    <div class="card-header bg-secondary">
        <h3 class="card-title">
            <i class="fas fa-cog"></i>
            Paso 2: Configuración del Plano
        </h3>
    </div>
    <div class="card-body">
        <form id="form-configuracion">
            <div class="row">
                <!-- Tipo y Ubicación -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Plano</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_ubicacion" id="tipo_saneamiento" value="S">
                            <label class="form-check-label" for="tipo_saneamiento">
                                Saneamiento
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_ubicacion" id="tipo_fiscal" value="C">
                            <label class="form-check-label" for="tipo_fiscal">
                                Fiscal
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ubicación</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ubicacion" id="urbano" value="U">
                            <label class="form-check-label" for="urbano">
                                Urbano (Solo M²)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ubicacion" id="rural" value="R">
                            <label class="form-check-label" for="rural">
                                Rural (Hijuela + Ha + M²)
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Comuna -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="comuna">Comuna <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="comuna" name="comuna" required>
                            <option value="">Seleccione comuna</option>
                            @foreach($comunas as $codigo => $nombre)
                                <option value="{{ $codigo }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Responsable -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="responsable">Responsable <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="responsable" name="responsable" required maxlength="255">
                    </div>
                </div>
                <!-- Proyecto -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="proyecto">Proyecto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="proyecto" name="proyecto" required maxlength="255" placeholder="Ej: CONVENIO-FINANCIAMIENTO">
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="row">
                <div class="col-md-12">
                    <h6><i class="fas fa-info-circle"></i> Información Adicional (Opcional)</h6>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="archivo">Archivo</label>
                        <input type="text" class="form-control" id="archivo" name="archivo" maxlength="255">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tubo">Tubo</label>
                        <input type="text" class="form-control" id="tubo" name="tubo" maxlength="255">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tela">Tela</label>
                        <input type="text" class="form-control" id="tela" name="tela" maxlength="255">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="archivo_digital">Archivo Digital</label>
                        <input type="text" class="form-control" id="archivo_digital" name="archivo_digital" maxlength="255">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="button" class="btn btn-secondary mr-2" id="btn-volver-tipo">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <button type="button" class="btn btn-primary" id="btn-continuar-folios">
                    <i class="fas fa-arrow-right"></i>
                    Continuar a Folios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Selector Cantidad Folios (solo Matrix) -->
<div class="card" id="cantidad-folios-card" style="display: none;">
    <div class="card-header bg-warning">
        <h3 class="card-title">
            <i class="fas fa-list-ol"></i>
            Paso 3: Cantidad de Folios (Plano Matrix)
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input" type="radio" name="cantidad_folios" id="un_folio" value="1">
                    <label class="form-check-label" for="un_folio">
                        <strong>1 FOLIO</strong><br>
                        <small class="text-muted">Formulario simple</small>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input" type="radio" name="cantidad_folios" id="multiples_folios" value="multiple">
                    <label class="form-check-label" for="multiples_folios">
                        <strong>2-10 FOLIOS</strong><br>
                        <small class="text-muted">Formulario múltiple</small>
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check form-check-lg">
                    <input class="form-check-input" type="radio" name="cantidad_folios" id="folios_masivos" value="masivo">
                    <label class="form-check-label" for="folios_masivos">
                        <strong>FOLIOS MASIVOS (11-150)</strong><br>
                        <small class="text-muted">Importación masiva</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="button" class="btn btn-secondary" id="btn-volver-configuracion">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    </div>
</div>

<!-- Ingreso de Folios -->
<div class="card" id="folios-card" style="display: none;">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-edit"></i>
            Paso 4: <span id="folios-title">Ingreso de Folios</span>
        </h3>
        <div class="card-tools">
            <span class="badge badge-success" id="folios-count">0 folios</span>
        </div>
    </div>
    <div class="card-body">

        <!-- Formulario 1 Folio -->
        <div id="form-un-folio" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="folio_unico">Folio <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="folio_unico" name="folio_unico" required>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" id="buscar_folio_unico">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8" id="resultado_folio_unico">
                    <!-- Resultado búsqueda -->
                </div>
            </div>
        </div>

        <!-- Formulario Múltiples Folios -->
        <div id="form-multiples-folios" style="display: none;">
            <div class="row mb-3">
                <div class="col-md-12">
                    <p class="text-muted">Busque hasta 10 folios. Las filas aparecerán conforme encuentre folios válidos.</p>
                </div>
            </div>
            <div id="multiples-folios-container">
                <!-- Se generan dinámicamente -->
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="agregar-fila-folio">
                <i class="fas fa-plus"></i> Agregar Fila
            </button>
        </div>

        <!-- Formulario Masivo -->
        <div id="form-folios-masivos" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="folios_masivos_texto">Lista de Folios (11-150)</label>
                        <textarea class="form-control" id="folios_masivos_texto" rows="8"
                                  placeholder="Pegue aquí la lista de folios, uno por línea o separados por comas:&#10;123456&#10;789012&#10;345678&#10;..."></textarea>
                        <small class="form-text text-muted">
                            Formato: Un folio por línea o separados por comas/punto y coma
                        </small>
                    </div>
                    <button type="button" class="btn btn-info" id="procesar-folios-masivos">
                        <i class="fas fa-search"></i> Procesar Lista
                    </button>
                </div>
                <div class="col-md-6" id="resultado-folios-masivos">
                    <!-- Resultados -->
                </div>
            </div>
        </div>

        <!-- Formulario Manual -->
        <div id="form-manual" style="display: none;">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Plano Manual:</strong> Complete todos los campos manualmente.
                Para casos fiscales use "FISCO DE CHILE" como solicitante sin apellidos.
            </div>

            <!-- Wizard: Indicador de progreso -->
            <div class="text-center mb-3" id="wizard-indicador" style="display: none;">
                <h5>
                    <span class="badge badge-primary" id="wizard-paso-actual">Folio 1 de 1</span>
                </h5>
            </div>

            <div id="manual-folios-container">
                <!-- Se genera dinámicamente -->
            </div>

            <!-- Wizard: Botones de navegación -->
            <div class="text-center mt-3" id="wizard-navegacion" style="display: none;">
                <button type="button" class="btn btn-secondary" id="btn-wizard-anterior">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn-primary" id="btn-wizard-siguiente">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <button type="button" class="btn btn-sm btn-secondary mt-2" id="agregar-fila-manual">
                <i class="fas fa-plus"></i> Agregar Folio
            </button>
        </div>

        <!-- Resumen de Folios -->
        <div id="resumen-folios" style="display: none;">
            <hr>
            <h6><i class="fas fa-list-check"></i> Resumen de Folios</h6>
            <div id="tabla-resumen">
                <!-- Tabla resumen -->
            </div>
        </div>

        <!-- Botones Finales -->
        <div class="text-center mt-4" id="botones-finales" style="display: none;">
            <button type="button" class="btn btn-secondary mr-2" id="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-success" id="btn-crear-plano">
                <i class="fas fa-save"></i> Crear Plano
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    initCrearPlanos();
});

// Variables globales
let configuracionPlano = {};
let foliosData = [];
let tipoPlanoActual = null;
let cantidadFoliosActual = null;
let ubicacionActual = null;
let wizardPasoActual = 0; // Índice del folio actual en wizard (0-based)

function initCrearPlanos() {
    initSelect2();
    initEventListeners();
}

function initSelect2() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccionar...'
    });
}

function initEventListeners() {
    // Tipo de plano
    $('input[name="tipo_plano"]').on('change', function() {
        tipoPlanoActual = $(this).val();
        $('#tipo-plano-card').hide(); // Ocultar paso 1
        $('#configuracion-card').show(); // Mostrar solo paso 2
        resetFormulario();
    });

    // Configuración
    $('#btn-continuar-folios').on('click', function() {
        if (validarConfiguracion()) {
            guardarConfiguracion();
            mostrarSiguientePaso();
        }
    });

    // Cantidad folios (solo Matrix)
    $('input[name="cantidad_folios"]').on('change', function() {
        cantidadFoliosActual = $(this).val();
        mostrarFormularioFolios();
    });

    // Ubicación - mostrar/ocultar campos hectáreas
    $('input[name="ubicacion"]').on('change', function() {
        ubicacionActual = $(this).val();
    });

    // Botones navegación wizard principal
    $('#btn-volver-tipo').on('click', function() {
        $('#configuracion-card').hide();
        $('#tipo-plano-card').show();
    });

    $('#btn-volver-configuracion').on('click', function() {
        $('#cantidad-folios-card').hide();
        $('#configuracion-card').show();
    });

    $('#btn-volver').on('click', volverPasoFolios);
    $('#btn-crear-plano').on('click', crearPlano);

    // Búsqueda folios
    $('#buscar_folio_unico').on('click', buscarFolioUnico);
    $('#procesar-folios-masivos').on('click', procesarFoliosMasivos);

    // Agregar filas
    $('#agregar-fila-folio').on('click', agregarFilaFolio);
    $('#agregar-fila-manual').on('click', agregarFilaManual);

    // Wizard navegación
    $('#btn-wizard-anterior').on('click', wizardAnterior);
    $('#btn-wizard-siguiente').on('click', wizardSiguiente);
}

function validarConfiguracion() {
    const requiredFields = ['tipo_ubicacion', 'ubicacion', 'comuna', 'responsable', 'proyecto'];
    let isValid = true;

    requiredFields.forEach(function(field) {
        const value = $('input[name="' + field + '"]:checked').val() || $('input[name="' + field + '"], select[name="' + field + '"]').val();
        if (!value) {
            isValid = false;
            $('input[name="' + field + '"], select[name="' + field + '"]').addClass('is-invalid');
        } else {
            $('input[name="' + field + '"], select[name="' + field + '"]').removeClass('is-invalid');
        }
    });

    if (!isValid) {
        Swal.fire('Error', 'Complete todos los campos obligatorios', 'error');
    }

    return isValid;
}

function guardarConfiguracion() {
    configuracionPlano = {
        tipo_plano: tipoPlanoActual,
        tipo_ubicacion: $('input[name="tipo_ubicacion"]:checked').val() + $('input[name="ubicacion"]:checked').val(),
        codigo_comuna: $('#comuna').val(),
        comuna_nombre: $('#comuna option:selected').text(),
        responsable: $('#responsable').val(),
        proyecto: $('#proyecto').val(),
        observaciones: $('#observaciones').val(),
        archivo: $('#archivo').val(),
        tubo: $('#tubo').val(),
        tela: $('#tela').val(),
        archivo_digital: $('#archivo_digital').val()
    };
}

function mostrarSiguientePaso() {
    $('#configuracion-card').hide(); // Ocultar paso 2

    if (tipoPlanoActual === 'matrix') {
        $('#cantidad-folios-card').show(); // Mostrar solo paso 3
    } else {
        cantidadFoliosActual = 'manual';
        mostrarFormularioFolios(); // Ir directo a paso 4
    }
}

function mostrarFormularioFolios() {
    $('#cantidad-folios-card').hide(); // Ocultar paso 3 (si estaba visible)
    $('#folios-card').show(); // Mostrar solo paso 4

    // Ocultar todos los formularios
    $('.card-body > div[id^="form-"]').hide();

    // Mostrar formulario correspondiente
    switch(cantidadFoliosActual) {
        case '1':
            $('#folios-title').text('Ingreso de 1 Folio');
            $('#form-un-folio').show();
            break;
        case 'multiple':
            $('#folios-title').text('Ingreso de 2-10 Folios');
            $('#form-multiples-folios').show();
            inicializarMultiplesFolios();
            break;
        case 'masivo':
            $('#folios-title').text('Ingreso Masivo de Folios');
            $('#form-folios-masivos').show();
            break;
        case 'manual':
            $('#folios-title').text('Ingreso Manual de Folios');
            $('#form-manual').show();
            inicializarManual();
            break;
    }

    actualizarContadorFolios();
}

function buscarFolioUnico() {
    const folio = $('#folio_unico').val().trim();
    if (!folio) {
        Swal.fire('Error', 'Ingrese un número de folio', 'error');
        return;
    }

    $.post("{{ route('planos.crear.buscar-folio') }}", {
        folio: folio,
        _token: "{{ csrf_token() }}"
    })
    .done(function(response) {
        mostrarResultadoFolioUnico(response);
    })
    .fail(function() {
        Swal.fire('Error', 'No se pudo buscar el folio', 'error');
    });
}

function mostrarResultadoFolioUnico(response) {
    let html = '';

    if (response.encontrado) {
        html += '<div class="alert ' + (response.yaUsado ? 'alert-warning' : 'alert-success') + '">';
        html += '<h6><i class="fas ' + (response.yaUsado ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i> ';
        html += (response.yaUsado ? 'Folio Ya Usado' : 'Folio Encontrado') + '</h6>';

        if (response.yaUsado) {
            html += '<p>Este folio ya fue utilizado en otro plano.</p>';
        }

        html += '<ul class="mb-0">';
        html += '<li><strong>Solicitante:</strong> ' + response.datos.solicitante + '</li>';
        html += '<li><strong>Apellidos:</strong> ' + response.datos.apellido_paterno + ' ' + response.datos.apellido_materno + '</li>';
        html += '<li><strong>Comuna:</strong> ' + response.datos.comuna + '</li>';
        html += '<li><strong>Responsable:</strong> ' + response.datos.responsable + '</li>';
        html += '<li><strong>Proyecto:</strong> ' + response.datos.proyecto + '</li>';
        html += '</ul>';

        if (!response.yaUsado) {
            // Agregar a foliosData
            foliosData = [{
                folio: response.datos.folio,
                solicitante: response.datos.solicitante,
                apellido_paterno: response.datos.apellido_paterno,
                apellido_materno: response.datos.apellido_materno,
                tipo_inmueble: ubicacionActual === 'R' ? 'HIJUELA' : 'SITIO',
                numero_inmueble: '',
                hectareas: ubicacionActual === 'R' ? 0 : null,
                m2: 0,
                is_from_matrix: true
            }];

            html += '<div class="mt-3">';
            html += '<button type="button" class="btn btn-success btn-sm" onclick="continuarConFolio()">Continuar con este folio</button>';
            html += '</div>';
        }

        html += '</div>';
    } else {
        html += '<div class="alert alert-danger">';
        html += '<h6><i class="fas fa-times-circle"></i> Folio No Encontrado</h6>';
        html += '<p>El folio <strong>' + $('#folio_unico').val() + '</strong> no existe en la base Matrix.</p>';
        html += '</div>';
    }

    $('#resultado_folio_unico').html(html);
}

function continuarConFolio() {
    mostrarFormularioManual(foliosData);
    $('#botones-finales').show();
}

function inicializarMultiplesFolios() {
    $('#multiples-folios-container').empty();
    for (let i = 1; i <= 3; i++) {
        agregarFilaFolio();
    }
}

function agregarFilaFolio() {
    const index = $('#multiples-folios-container .folio-input-group').length + 1;
    if (index > 10) {
        Swal.fire('Límite', 'Máximo 10 folios permitidos', 'warning');
        return;
    }

    const html = `
        <div class="folio-input-group row mb-3">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control folio-input" placeholder="Folio ${index}" data-index="${index}">
                    <div class="input-group-append">
                        <button class="btn btn-info btn-sm buscar-folio-btn" type="button" data-index="${index}">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="folio-resultado" id="resultado-${index}"></div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger eliminar-fila" onclick="eliminarFilaFolio(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

    $('#multiples-folios-container').append(html);

    // Event listener para búsqueda
    $(`[data-index="${index}"].buscar-folio-btn`).on('click', function() {
        buscarFolioMultiple(index);
    });
}

function eliminarFilaFolio(index) {
    $(`.folio-input-group:has([data-index="${index}"])`).remove();
    actualizarContadorFolios();
}

function buscarFolioMultiple(index) {
    const folio = $(`.folio-input[data-index="${index}"]`).val().trim();
    if (!folio) return;

    $.post("{{ route('planos.crear.buscar-folio') }}", {
        folio: folio,
        _token: "{{ csrf_token() }}"
    })
    .done(function(response) {
        mostrarResultadoFolioMultiple(response, index);
    })
    .fail(function() {
        $(`#resultado-${index}`).html('<small class="text-danger">Error al buscar</small>');
    });
}

function mostrarResultadoFolioMultiple(response, index) {
    let html = '';
    const container = $(`#resultado-${index}`);

    if (response.encontrado) {
        const cssClass = response.yaUsado ? 'text-warning' : 'text-success';
        const icon = response.yaUsado ? 'fa-exclamation-triangle' : 'fa-check-circle';

        html = `<small class="${cssClass}"><i class="fas ${icon}"></i> `;
        html += `${response.datos.solicitante} ${response.datos.apellido_paterno}`;
        if (response.yaUsado) html += ' (YA USADO)';
        html += '</small>';

        if (response.yaUsado) {
            $(`.folio-input[data-index="${index}"]`).addClass('is-invalid');
        } else {
            $(`.folio-input[data-index="${index}"]`).removeClass('is-invalid').addClass('is-valid');
        }
    } else {
        html = '<small class="text-danger"><i class="fas fa-times-circle"></i> No encontrado</small>';
        $(`.folio-input[data-index="${index}"]`).addClass('is-invalid');
    }

    container.html(html);
}

function procesarFoliosMasivos() {
    const texto = $('#folios_masivos_texto').val().trim();
    if (!texto) {
        Swal.fire('Error', 'Ingrese una lista de folios', 'error');
        return;
    }

    showProgressModal('Procesando folios...', 'Buscando en base de datos Matrix');

    $.post("{{ route('planos.crear.buscar-folios-masivos') }}", {
        folios: texto,
        _token: "{{ csrf_token() }}"
    })
    .done(function(response) {
        hideProgressModal();
        mostrarResultadosMasivos(response);
    })
    .fail(function() {
        hideProgressModal();
        Swal.fire('Error', 'No se pudo procesar la lista', 'error');
    });
}

function mostrarResultadosMasivos(response) {
    let html = '<div class="card">';
    html += '<div class="card-body">';
    html += '<h6>Resultados del Procesamiento</h6>';
    html += `<ul>`;
    html += `<li><strong>Total procesados:</strong> ${response.totalProcesados}</li>`;
    html += `<li><strong>Encontrados:</strong> ${response.resumen.encontrados}</li>`;
    html += `<li><strong>No encontrados:</strong> ${response.resumen.noEncontrados}</li>`;
    html += `<li><strong>Ya usados:</strong> ${response.resumen.yaUsados}</li>`;
    html += `</ul>`;

    if (response.noEncontrados.length > 0) {
        html += '<div class="alert alert-warning mt-3">';
        html += '<h6>Folios no encontrados en Matrix:</h6>';
        html += '<small>' + response.noEncontrados.join(', ') + '</small>';
        html += '</div>';
    }

    if (response.yaUsados.length > 0) {
        html += '<div class="alert alert-danger mt-3">';
        html += '<h6>Folios ya utilizados:</h6>';
        html += '<small>' + response.yaUsados.join(', ') + '</small>';
        html += '</div>';
    }

    const foliosValidos = response.encontrados.filter(f => !f.yaUsado);
    if (foliosValidos.length > 0) {
        html += '<div class="alert alert-success mt-3">';
        html += `<h6>Folios válidos encontrados: ${foliosValidos.length}</h6>`;
        html += '<button type="button" class="btn btn-success btn-sm" onclick="continuarConFoliosMasivos()">Continuar con estos folios</button>';
        html += '</div>';

        // Guardar folios válidos
        foliosData = foliosValidos.map(function(f) {
            return {
                folio: f.folio,
                solicitante: f.solicitante,
                apellido_paterno: f.apellido_paterno,
                apellido_materno: f.apellido_materno,
                tipo_inmueble: ubicacionActual === 'R' ? 'HIJUELA' : 'SITIO',
                numero_inmueble: '',
                hectareas: ubicacionActual === 'R' ? 0 : null,
                m2: 0,
                is_from_matrix: true
            };
        });
    }

    html += '</div></div>';
    $('#resultado-folios-masivos').html(html);
}

function continuarConFoliosMasivos() {
    mostrarFormularioManual(foliosData);
    $('#botones-finales').show();
}

function inicializarManual() {
    $('#manual-folios-container').empty();
    agregarFilaManual();
    // inicializarWizard() ya se llama dentro de agregarFilaManual()
}

function agregarFilaManual() {
    const index = $('#manual-folios-container .manual-folio-row').length + 1;
    if (index > 150) {
        Swal.fire('Límite', 'Máximo 150 folios permitidos', 'warning');
        return;
    }

    const esRural = ubicacionActual === 'R';

    const html = `
        <div class="manual-folio-row card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Folio ${index}</h6>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaManual(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Folio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control manual-folio" data-index="${index}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Solicitante <span class="text-danger">*</span></label>
                            <input type="text" class="form-control manual-solicitante" data-index="${index}" required
                                   placeholder="FISCO DE CHILE para casos fiscales">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Apellido Paterno</label>
                            <input type="text" class="form-control manual-apellido-paterno" data-index="${index}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Apellido Materno</label>
                            <input type="text" class="form-control manual-apellido-materno" data-index="${index}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo Inmueble</label>
                            <select class="form-control manual-tipo-inmueble" data-index="${index}">
                                <option value="SITIO" ${!esRural ? 'selected' : ''}>SITIO</option>
                                <option value="HIJUELA" ${esRural ? 'selected' : ''}>HIJUELA</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>N° Inmueble</label>
                            <input type="number" class="form-control manual-numero-inmueble" data-index="${index}">
                        </div>
                    </div>
                    ${esRural ? `
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hectáreas</label>
                            <input type="number" step="0.0001" class="form-control manual-hectareas" data-index="${index}">
                        </div>
                    </div>
                    ` : '<div class="col-md-3"></div>'}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>M² <span class="text-danger">*</span></label>
                            <input type="number" class="form-control manual-m2" data-index="${index}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#manual-folios-container').append(html);
    actualizarContadorFolios();
    inicializarWizard(); // Activar wizard después de agregar
}

function eliminarFilaManual(index) {
    $(`.manual-folio-row:has([data-index="${index}"])`).remove();
    actualizarContadorFolios();
    inicializarWizard(); // Actualizar wizard después de eliminar
}

function mostrarFormularioManual(foliosPreCargados = null) {
    if (foliosPreCargados) {
        $('#manual-folios-container').empty();

        foliosPreCargados.forEach(function(folio, index) {
            const realIndex = index + 1;
            const esRural = ubicacionActual === 'R';

            const html = `
                <div class="manual-folio-row card mb-3">
                    <div class="card-header bg-success">
                        <h6 class="card-title mb-0 text-white">Folio ${realIndex} - ${folio.folio}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Folio</label>
                                    <input type="text" class="form-control manual-folio" data-index="${realIndex}"
                                           value="${folio.folio}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Solicitante</label>
                                    <input type="text" class="form-control manual-solicitante" data-index="${realIndex}"
                                           value="${folio.solicitante}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Apellido Paterno</label>
                                    <input type="text" class="form-control manual-apellido-paterno" data-index="${realIndex}"
                                           value="${folio.apellido_paterno}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Apellido Materno</label>
                                    <input type="text" class="form-control manual-apellido-materno" data-index="${realIndex}"
                                           value="${folio.apellido_materno}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipo Inmueble</label>
                                    <select class="form-control manual-tipo-inmueble" data-index="${realIndex}">
                                        <option value="SITIO" ${folio.tipo_inmueble === 'SITIO' ? 'selected' : ''}>SITIO</option>
                                        <option value="HIJUELA" ${folio.tipo_inmueble === 'HIJUELA' ? 'selected' : ''}>HIJUELA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>N° Inmueble</label>
                                    <input type="number" class="form-control manual-numero-inmueble" data-index="${realIndex}"
                                           value="${folio.numero_inmueble || ''}">
                                </div>
                            </div>
                            ${esRural ? `
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hectáreas</label>
                                    <input type="number" step="0.0001" class="form-control manual-hectareas" data-index="${realIndex}"
                                           value="${folio.hectareas || ''}">
                                </div>
                            </div>
                            ` : '<div class="col-md-3"></div>'}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>M² <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control manual-m2" data-index="${realIndex}"
                                           value="${folio.m2 || ''}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#manual-folios-container').append(html);
        });
    }

    $('#form-manual').show();
    actualizarContadorFolios();
    inicializarWizard(); // Activar wizard al mostrar formulario
}

function recopilarFoliosFinales() {
    const folios = [];

    $('.manual-folio-row').each(function(index) {
        const realIndex = index + 1;

        const folio = {
            folio: $(`.manual-folio[data-index="${realIndex}"]`).val(),
            solicitante: $(`.manual-solicitante[data-index="${realIndex}"]`).val(),
            apellido_paterno: $(`.manual-apellido-paterno[data-index="${realIndex}"]`).val(),
            apellido_materno: $(`.manual-apellido-materno[data-index="${realIndex}"]`).val(),
            tipo_inmueble: $(`.manual-tipo-inmueble[data-index="${realIndex}"]`).val(),
            numero_inmueble: $(`.manual-numero-inmueble[data-index="${realIndex}"]`).val(),
            hectareas: $(`.manual-hectareas[data-index="${realIndex}"]`).val(),
            m2: $(`.manual-m2[data-index="${realIndex}"]`).val(),
            is_from_matrix: tipoPlanoActual === 'matrix'
        };

        if (folio.folio && folio.solicitante && folio.m2) {
            folios.push(folio);
        }
    });

    return folios;
}

function crearPlano() {
    const foliosFinales = recopilarFoliosFinales();

    if (foliosFinales.length === 0) {
        Swal.fire('Error', 'Debe agregar al menos un folio válido', 'error');
        return;
    }

    if (foliosFinales.length > 150) {
        Swal.fire('Error', 'Máximo 150 folios permitidos por plano', 'error');
        return;
    }

    Swal.fire({
        title: '¿Confirmar creación?',
        html: `Se creará un plano con <strong>${foliosFinales.length} folio(s)</strong><br>` +
              `Tipo: <strong>${configuracionPlano.tipo_ubicacion}</strong><br>` +
              `Comuna: <strong>${configuracionPlano.comuna_nombre}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, crear plano',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarCreacionPlano(foliosFinales);
        }
    });
}

function ejecutarCreacionPlano(folios) {
    const data = {
        ...configuracionPlano,
        folios: folios,
        _token: "{{ csrf_token() }}"
    };

    showProgressModal('Creando plano...', 'Generando número y guardando datos');

    $.post("{{ route('planos.crear.store') }}", data)
        .done(function(response) {
            hideProgressModal();

            if (response.success) {
                Swal.fire({
                    title: '¡Plano creado exitosamente!',
                    html: `<strong>Número:</strong> ${response.plano.numero}<br>` +
                          `<strong>Folios:</strong> ${response.plano.folios}`,
                    icon: 'success',
                    confirmButtonText: 'Ir a Tabla General'
                }).then(() => {
                    window.location.href = "{{ route('planos.index') }}";
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            hideProgressModal();
            Swal.fire('Error', 'No se pudo crear el plano', 'error');
        });
}

function actualizarContadorFolios() {
    let count = 0;

    if (cantidadFoliosActual === '1') {
        count = foliosData.length;
    } else if (cantidadFoliosActual === 'multiple') {
        count = $('.folio-input-group .is-valid').length;
    } else if (cantidadFoliosActual === 'masivo') {
        count = foliosData.length;
    } else if (cantidadFoliosActual === 'manual') {
        count = $('.manual-folio-row').length;
    }

    $('#folios-count').text(count + ' folio' + (count !== 1 ? 's' : ''));
}

function resetFormulario() {
    configuracionPlano = {};
    foliosData = [];
    // Ocultar todos los pasos excepto configuración (que ya está visible)
    $('#cantidad-folios-card, #folios-card').hide();
    $('input[name="cantidad_folios"]').prop('checked', false);
}

function volverPasoFolios() {
    // Volver desde la card de folios
    $('#folios-card').hide();

    if (tipoPlanoActual === 'matrix') {
        $('#cantidad-folios-card').show(); // Volver a cantidad si es Matrix
    } else {
        $('#configuracion-card').show(); // Volver a configuración si es Manual
    }
}

// Funciones de utilidad
function showProgressModal(title, message) {
    $('#progress-title').text(title);
    $('#progress-message').text(message);
    $('#progress-modal').modal('show');
}

function hideProgressModal() {
    $('#progress-modal').modal('hide');
}

// =====================================================
// WIZARD: Sistema de navegación paso a paso
// =====================================================

function mostrarFolioWizard(indice) {
    const $folios = $('.manual-folio-row');
    const totalFolios = $folios.length;

    if (totalFolios === 0) return;

    // Validar índice
    if (indice < 0) indice = 0;
    if (indice >= totalFolios) indice = totalFolios - 1;

    wizardPasoActual = indice;

    // Ocultar todos los folios
    $folios.hide();

    // Mostrar solo el folio actual
    $folios.eq(indice).fadeIn(300);

    // Actualizar indicador
    $('#wizard-paso-actual').text(`Folio ${indice + 1} de ${totalFolios}`);

    // Mostrar/ocultar wizard si hay más de 1 folio
    if (totalFolios > 1) {
        $('#wizard-indicador').show();
        $('#wizard-navegacion').show();

        // Habilitar/deshabilitar botones según posición
        $('#btn-wizard-anterior').prop('disabled', indice === 0);
        $('#btn-wizard-siguiente').prop('disabled', indice === totalFolios - 1);
    } else {
        $('#wizard-indicador').hide();
        $('#wizard-navegacion').hide();
    }
}

function wizardAnterior() {
    mostrarFolioWizard(wizardPasoActual - 1);
}

function wizardSiguiente() {
    mostrarFolioWizard(wizardPasoActual + 1);
}

function inicializarWizard() {
    wizardPasoActual = 0;
    mostrarFolioWizard(0);
}
</script>
@endpush