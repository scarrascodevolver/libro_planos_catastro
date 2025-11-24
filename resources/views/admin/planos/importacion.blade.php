@extends('layouts.admin')

@section('title', 'Importación Masiva')

@section('page-title', 'Importación Masiva')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Importación Masiva</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-import"></i>
            Importación Masiva
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Matrix Importer -->
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-3">
                        <i class="fas fa-file-excel text-success"></i>
                        Matrix (Mensual)
                        <i class="fas fa-info-circle text-muted ml-1" data-toggle="tooltip"
                           title="Extrae: Tipo Inmueble, Comuna, Nombres, Apellidos, Folio, Responsable, Convenio"></i>
                    </h6>

                    <form id="form-matrix-import" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="batch_name" name="batch_name" value="MATRIX-{{ date('Y-m') }}">
                        <input type="hidden" name="accion_duplicados" value="actualizar">

                        <div class="form-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="archivo_matrix" name="archivo" accept=".xlsx,.xls" required>
                                <label class="custom-file-label" for="archivo_matrix">Seleccionar Excel Matrix (.xlsx)</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $ultimoBatch ?? 'Sin importar' }} | <span id="total-matrix">{{ number_format($totalMatrix) }}</span> folios
                            </small>
                            <button type="button" class="btn btn-sm btn-primary" id="btn-importar-matrix">
                                <i class="fas fa-upload"></i> Importar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historical Importer -->
            <div class="col-md-6">
                <div class="border rounded p-3 h-100 border-warning">
                    <h6 class="mb-3">
                        <i class="fas fa-history text-warning"></i>
                        Históricos (Una vez)
                        <i class="fas fa-info-circle text-muted ml-1" data-toggle="tooltip"
                           title="21 columnas: N° Plano, Comuna, Responsable, Proyecto, Folios, Datos personales, Hectáreas, M², Fechas, Archivos"></i>
                        <span class="badge badge-danger ml-1">Irreversible</span>
                    </h6>

                    <form id="form-historicos-import" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="archivo_historicos" name="archivo" accept=".xlsx,.xls" required>
                                <label class="custom-file-label" for="archivo_historicos">Seleccionar PLANOS-HISTORICOS.xlsx</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-warning" id="btn-importar-historicos">
                                <i class="fas fa-upload"></i> Importar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gestión de Datos Importados -->
        <hr class="my-4">
        <div class="row">
            <div class="col-12 mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-database"></i>
                    Gestión de Datos
                </h5>
            </div>

            <!-- Eliminar Matrix -->
            <div class="col-md-6">
                <button type="button" class="btn btn-warning btn-block" id="btn-eliminar-matrix">
                    <i class="fas fa-trash"></i> Eliminar Datos Matrix
                    <span class="badge badge-light ml-2" id="total-matrix-delete">{{ number_format($totalMatrix) }}</span>
                </button>
            </div>

            <!-- Eliminar Planos -->
            <div class="col-md-6">
                <button type="button" class="btn btn-danger btn-block" id="btn-eliminar-historicos">
                    <i class="fas fa-fire"></i> Eliminar TODOS los Planos
                    <span class="badge badge-light ml-2" id="total-historicos-delete">0</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye"></i>
                    Vista Previa del Archivo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="preview-content">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="confirm-import" style="display: none;">
                    <i class="fas fa-check"></i>
                    Confirmar Importación
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
/* Cambiar texto del botón "Browse" a "Examinar" en español */
.custom-file-input ~ .custom-file-label::after {
    content: "Examinar" !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    @if(auth()->user()->isRegistro())
        checkSessionControlImport();
        setInterval(checkSessionControlImport, 10000); // Cada 10 segundos
    @endif

    initImportForms();
    loadEstadisticas();
});

let currentPreviewType = null;
let hasSessionControl = false;

@if(auth()->user()->isRegistro())
function checkSessionControlImport() {
    $.ajax({
        url: '{{ route("session-control.status") }}',
        method: 'GET',
        success: function(response) {
            hasSessionControl = response.hasControl;

            const btnHistoricos = $('#btn-importar-historicos');

            if (response.hasControl) {
                // Tiene control - habilitar botón
                btnHistoricos.prop('disabled', false)
                             .removeClass('btn-secondary')
                             .addClass('btn-warning');
            } else {
                // No tiene control - deshabilitar botón
                btnHistoricos.prop('disabled', true)
                             .removeClass('btn-warning')
                             .addClass('btn-secondary');
            }
        },
        error: function(xhr) {
            console.error('Error verificando control:', xhr);
            // En caso de error, deshabilitar por seguridad
            $('#btn-importar-historicos')
                .prop('disabled', true)
                .removeClass('btn-warning')
                .addClass('btn-secondary');
        }
    });
}
@endif

function initImportForms() {
    // Matrix Import - Un solo botón que hace preview + confirmar
    $('#btn-importar-matrix').on('click', function() {
        const formData = new FormData();
        const archivo = $('#archivo_matrix')[0].files[0];

        if (!archivo) {
            Swal.fire('Error', 'Debe seleccionar un archivo', 'error');
            return;
        }

        formData.append('archivo', archivo);

        // Mostrar loading con SweetAlert
        Swal.fire({
            title: 'Analizando archivo...',
            text: 'Verificando estructura y contenido',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('planos.importacion.preview-matrix') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.close();

                if (response.success) {
                    showPreview(response, 'matrix');
                } else {
                    Swal.fire('Error', response.message, 'error');
                    if (response.errores) {
                        let erroresHtml = '<ul>';
                        response.errores.forEach(function(error) {
                            erroresHtml += '<li>' + error + '</li>';
                        });
                        erroresHtml += '</ul>';

                        Swal.fire({
                            title: 'Errores encontrados',
                            html: erroresHtml,
                            icon: 'error',
                            width: 600
                        });
                    }
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'No se pudo procesar el archivo', 'error');
            }
        });
    });

    // Históricos Import - Un solo botón que hace preview + confirmar
    $('#btn-importar-historicos').on('click', function() {
        // VERIFICAR CONTROL DE SESIÓN
        @if(auth()->user()->isRegistro())
            if (!hasSessionControl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin Control de Numeración',
                    text: 'Debes tener el control de numeración para importar planos históricos',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
        @endif

        const formData = new FormData();
        const archivo = $('#archivo_historicos')[0].files[0];

        if (!archivo) {
            Swal.fire('Error', 'Debe seleccionar un archivo', 'error');
            return;
        }

        formData.append('archivo_excel', archivo);

        Swal.fire({
            title: 'Analizando archivo históricos...',
            text: 'Verificando estructura de 21 columnas',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('admin.planos.historico.preview') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.close();

                if (response.success) {
                    showPreview(response, 'historicos');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'No se pudo procesar el archivo', 'error');
            }
        });
    });

    // File input labels
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
}

function showPreview(data, type) {
    currentPreviewType = type;

    let html = '';

    // Mensaje principal según si hay errores
    if (data.registrosConErrores > 0) {
        html += '<div class="alert alert-warning mb-3">';
        html += '<h5 class="mb-2"><i class="fas fa-exclamation-triangle"></i> ' + data.totalFilas + ' folios a importar</h5>';
        html += '<p class="mb-0">Válidos: <strong class="text-success">' + data.registrosValidos + '</strong> | ';
        html += 'Con campos vacíos: <strong class="text-danger">' + data.registrosConErrores + '</strong></p>';
        html += '</div>';

        // Resumen de errores por campo
        if (data.erroresPorCampo && Object.keys(data.erroresPorCampo).length > 0) {
            html += '<p class="mb-2"><strong>Campos faltantes:</strong> ';
            let camposList = [];
            Object.keys(data.erroresPorCampo).forEach(function(campo) {
                let nombreCampo = campo.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                camposList.push(nombreCampo + ' (' + data.erroresPorCampo[campo] + ')');
            });
            html += camposList.join(', ') + '</p>';
        }

        // Tabla detallada de folios con errores
        html += '<div class="table-responsive" style="max-height: 300px; overflow-y: auto;">';
        html += '<table class="table table-sm table-bordered table-striped mb-0">';
        html += '<thead class="thead-dark"><tr>';
        html += '<th>Fila</th><th>Folio</th><th>Campos Faltantes</th>';
        html += '</tr></thead><tbody>';

        data.detalleErrores.forEach(function(error) {
            html += '<tr>';
            html += '<td>' + error.fila + '</td>';
            html += '<td><strong>' + error.folio + '</strong></td>';
            html += '<td class="text-danger">' + error.campos.join(', ') + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';

        // Botón descargar CSV
        html += '<div class="mt-2">';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="descargarErroresCSV()"><i class="fas fa-download"></i> Descargar CSV</button>';
        html += '</div>';

        // Guardar errores para descarga
        window.erroresMatrix = data.detalleErrores;

    } else {
        html += '<div class="alert alert-success mb-0">';
        html += '<h5 class="mb-0"><i class="fas fa-check-circle"></i> ' + data.totalFilas + ' folios listos para importar</h5>';
        html += '</div>';
    }

    $('#preview-content').html(html);

    // Mostrar/ocultar botón importar según errores
    if (data.registrosConErrores > 0) {
        $('#confirm-import').html('<i class="fas fa-exclamation-triangle"></i> Importar de todos modos').removeClass('btn-primary').addClass('btn-warning');
    } else {
        $('#confirm-import').html('<i class="fas fa-check"></i> Confirmar Importación').removeClass('btn-warning').addClass('btn-primary');
    }
    $('#confirm-import').show();
    $('#preview-modal').modal('show');
}

function descargarErroresCSV() {
    if (!window.erroresMatrix || window.erroresMatrix.length === 0) {
        Swal.fire('Info', 'No hay errores para descargar', 'info');
        return;
    }

    let csv = 'Fila,Folio,Campos Faltantes\n';
    window.erroresMatrix.forEach(function(error) {
        csv += error.fila + ',"' + error.folio + '","' + error.campos.join(', ') + '"\n';
    });

    let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement('a');
    let url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'errores_matrix_' + new Date().toISOString().slice(0,10) + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function executeMatrixImport() {
    const formData = new FormData($('#form-matrix-import')[0]);

    Swal.fire({
        title: 'Importando datos Matrix...',
        text: 'Procesando registros, por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: "{{ route('planos.importacion.import-matrix') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();

            if (response.success) {
                // Mostrar estadísticas
                let html = '<div class="alert alert-success">';
                html += '<h6><i class="fas fa-check-circle"></i> Importación completada</h6>';
                html += '<p>' + response.message + '</p>';
                html += '<ul>';
                html += '<li>Procesados: <strong>' + response.estadisticas.procesados + '</strong></li>';
                html += '<li>Nuevos: <strong>' + response.estadisticas.nuevos + '</strong></li>';
                html += '<li>Actualizados: <strong>' + response.estadisticas.actualizados + '</strong></li>';
                html += '<li>Errores: <strong>' + response.estadisticas.errores + '</strong></li>';
                html += '</ul></div>';

                if (response.errores && response.errores.length > 0) {
                    html += '<div class="alert alert-warning">';
                    html += '<h6>Errores encontrados:</h6>';
                    html += '<ul class="mb-0">';
                    response.errores.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                    html += '</ul></div>';
                }

                Swal.fire({
                    title: '¡Éxito!',
                    html: html,
                    icon: 'success',
                    width: 600,
                    confirmButtonText: 'Cerrar'
                });

                // Reset form
                $('#form-matrix-import')[0].reset();
                $('.custom-file-label').first().html('Seleccionar Excel Matrix (.xlsx)');

                // Reload estadísticas
                loadEstadisticas();

            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'No se pudo completar la importación', 'error');
        }
    });
}

function executeHistoricosImport() {
    const formData = new FormData();
    const archivo = $('#archivo_historicos')[0].files[0];

    if (!archivo) {
        Swal.fire('Error', 'Debe seleccionar un archivo', 'error');
        return;
    }

    formData.append('archivo_excel', archivo);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    Swal.fire({
        title: 'Importando planos históricos...',
        text: 'Procesando grupos de planos, por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: "{{ route('admin.planos.historico.import') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.close();

            if (response.success) {
                // Mostrar resultados con formato mejorado
                let html = '<div class="text-left">';

                // Resumen exitoso
                html += '<div class="alert alert-success mb-3">';
                html += '<h5 class="mb-2"><i class="fas fa-check-circle"></i> Importación Completada</h5>';
                html += '<div class="row">';
                html += '<div class="col-6">✓ Planos creados: <strong>' + response.resultado.planos_creados + '</strong></div>';
                html += '<div class="col-6">✓ Folios creados: <strong>' + response.resultado.folios_creados + '</strong></div>';
                html += '</div></div>';

                // Warnings (planos con datos incompletos pero importados)
                if (response.resultado.warnings && response.resultado.warnings.length > 0) {
                    html += '<div class="alert alert-warning mb-3">';
                    html += '<h6><i class="fas fa-exclamation-triangle"></i> Advertencias (' + response.resultado.warnings.length + ' plano(s) con datos incompletos - importados igual)</h6>';
                    html += '<div style="max-height: 200px; overflow-y: auto; font-size: 13px;">';
                    response.resultado.warnings.forEach(function(warning) {
                        html += '<div class="border-bottom pb-2 mb-2">';
                        html += '<strong>• Plano ' + warning.numero_plano + '</strong> <span class="badge badge-secondary">Fila Excel: ' + warning.fila_excel + '</span><br>';
                        html += '<small>';
                        html += '&nbsp;&nbsp;Comuna: ' + warning.comuna + ' | ';
                        html += 'Solicitante: ' + warning.solicitante + ' | ';
                        html += 'Folio: ' + warning.folio + '<br>';
                        warning.advertencias.forEach(function(adv) {
                            html += '&nbsp;&nbsp;<span class="text-warning">⚠</span> ' + adv + '<br>';
                        });
                        html += '</small></div>';
                    });
                    html += '</div></div>';
                }

                // Errores críticos (planos NO importados)
                if (response.resultado.errores && response.resultado.errores.length > 0) {
                    html += '<div class="alert alert-danger mb-3">';
                    html += '<h6><i class="fas fa-times-circle"></i> Errores Críticos (' + response.resultado.errores.length + ' plano(s) NO importado(s))</h6>';
                    html += '<div style="max-height: 200px; overflow-y: auto; font-size: 13px;">';
                    response.resultado.errores.forEach(function(error) {
                        html += '<div class="border-bottom pb-2 mb-2">';
                        html += '<strong>• Plano ' + error.numero_plano + '</strong> <span class="badge badge-dark">Fila Excel: ' + error.fila_excel + '</span><br>';
                        html += '<small>';
                        html += '&nbsp;&nbsp;Comuna: ' + error.comuna + ' | ';
                        html += 'Solicitante: ' + error.solicitante + ' | ';
                        html += 'Folio: ' + error.folio + '<br>';
                        error.errores.forEach(function(err) {
                            html += '&nbsp;&nbsp;<span class="text-danger">✖</span> ' + err + '<br>';
                        });
                        html += '</small></div>';
                    });
                    html += '</div></div>';
                }

                html += '</div>';

                Swal.fire({
                    title: '¡Importación Completada!',
                    html: html,
                    icon: 'success',
                    width: 800,
                    confirmButtonText: 'Cerrar'
                });

                // Reset form
                $('#form-historicos-import')[0].reset();
                $('.custom-file-label').eq(1).html('Seleccionar PLANOS-HISTORICOS.xlsx');

                // ACTUALIZAR BADGE DE PLANOS HISTÓRICOS INMEDIATAMENTE
                loadHistoricosCount();

            } else {
                // Error general
                let errorHtml = '<div class="text-left">';
                errorHtml += '<p>' + response.message + '</p>';

                if (response.resultado && response.resultado.errores && response.resultado.errores.length > 0) {
                    errorHtml += '<div class="alert alert-danger mt-2">';
                    errorHtml += '<h6>Errores Críticos:</h6>';
                    errorHtml += '<div style="max-height: 250px; overflow-y: auto; font-size: 13px;">';
                    response.resultado.errores.forEach(function(error) {
                        errorHtml += '<div class="border-bottom pb-2 mb-2">';
                        errorHtml += '<strong>• Plano ' + error.numero_plano + '</strong> <span class="badge badge-dark">Fila: ' + error.fila_excel + '</span><br>';
                        errorHtml += '<small>Comuna: ' + error.comuna + ' | Solicitante: ' + error.solicitante + '<br>';
                        error.errores.forEach(function(err) {
                            errorHtml += '&nbsp;&nbsp;✖ ' + err + '<br>';
                        });
                        errorHtml += '</small></div>';
                    });
                    errorHtml += '</div></div>';
                }
                errorHtml += '</div>';

                Swal.fire({
                    title: 'Error en importación',
                    html: errorHtml,
                    icon: 'error',
                    width: 800
                });
            }
        },
        error: function(xhr) {
            Swal.close();

            // Si el error es 403 (sin control), mostrar mensaje específico
            if (xhr.status === 403 && xhr.responseJSON) {
                const response = xhr.responseJSON;

                if (response.requiere_control) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Control de Numeración Requerido',
                        html: '<p>' + response.message + '</p>' +
                              '<p class="text-muted mt-2">Obtén el control desde el widget superior para poder importar planos históricos.</p>',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        // Actualizar estado del control
                        checkSessionStatus();
                    });
                } else {
                    Swal.fire('Error', response.message || 'No tienes permisos para importar', 'error');
                }
            } else {
                Swal.fire('Error', 'No se pudo completar la importación histórica', 'error');
            }
        }
    });
}

function loadEstadisticas() {
    $.get("{{ route('planos.importacion.estadisticas-matrix') }}")
        .done(function(data) {
            $('#total-matrix').text(data.total.toLocaleString());
            $('#total-matrix-delete').text(data.total.toLocaleString());
        });

    // Cargar total de históricos
    loadHistoricosCount();
}

function loadHistoricosCount() {
    // Contar TODOS los planos, no solo históricos
    $.ajax({
        url: '{{ route("planos.importacion.estadisticas-historicos") }}',
        method: 'GET',
        success: function(response) {
            const total = response.total_planos || 0;
            $('#total-historicos-delete').text(total.toLocaleString());
        },
        error: function(xhr) {
            console.error('Error cargando planos:', xhr);
            $('#total-historicos-delete').text('0');
        }
    });
}

// Confirm import from preview
$('#confirm-import').on('click', function() {
    $('#preview-modal').modal('hide');

    if (currentPreviewType === 'matrix') {
        executeMatrixImport();
    } else if (currentPreviewType === 'historicos') {
        executeHistoricosImport();
    }
});

// ========================================
// GESTIÓN DE DATOS IMPORTADOS
// ========================================

// Eliminar TODOS los datos Matrix
$('#btn-eliminar-matrix').on('click', function() {
    const totalMatrix = parseInt($('#total-matrix-delete').text().replace(/,/g, ''));

    if (totalMatrix === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin Datos',
            text: 'No hay registros Matrix para eliminar'
        });
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar TODOS los Datos Matrix?',
        html: `
            <p class="text-left">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Estás a punto de eliminar <strong>${totalMatrix.toLocaleString()} registros</strong> de la tabla Matrix.
            </p>
            <div class="alert alert-warning text-left mt-3 mb-0">
                <strong>Importante:</strong>
                <ul class="mb-0">
                    <li>Esto NO afecta planos ya creados</li>
                    <li>Solo elimina datos de autocompletado</li>
                    <li>Puedes volver a importar Matrix cuando quieras</li>
                </ul>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, Eliminar Matrix',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarEliminarMatrix();
        }
    });
});

function ejecutarEliminarMatrix() {
    Swal.fire({
        title: 'Eliminando datos Matrix...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("planos.importacion.eliminar-todos-matrix") }}',
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.close();

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Matrix Eliminado',
                    html: `
                        <p>${response.message}</p>
                        <p class="text-muted mb-0">
                            <strong>${response.registros_eliminados.toLocaleString()}</strong> registros eliminados
                        </p>
                    `,
                    confirmButtonText: 'Cerrar'
                });

                // Actualizar contadores
                loadEstadisticas();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error al Eliminar',
                text: xhr.responseJSON?.message || 'No se pudo completar la eliminación'
            });
        }
    });
}

// Eliminar TODOS los Planos
$('#btn-eliminar-historicos').on('click', function() {
    // Primera confirmación
    Swal.fire({
        icon: 'error',
        title: 'ADVERTENCIA: Eliminar TODOS los Planos',
        html: `
            <div class="text-left">
                <p class="text-danger mb-3">
                    <i class="fas fa-skull-crossbones"></i>
                    <strong>Esta acción es IRREVERSIBLE y MUY PELIGROSA</strong>
                </p>
                <p>Eliminarás:</p>
                <ul>
                    <li>TODOS los planos de la tabla</li>
                    <li>TODOS los folios asociados</li>
                    <li>Datos de personas, hectáreas, metros cuadrados</li>
                </ul>
                <p class="text-muted mb-0">
                    <small><i class="fas fa-info-circle"></i> Se eliminará toda la información de planos</small>
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Segunda confirmación - Escribir texto
            mostrarConfirmacionTextoHistoricos();
        }
    });
});

function mostrarConfirmacionTextoHistoricos() {
    Swal.fire({
        icon: 'error',
        title: 'Confirmación Final',
        html: `
            <div class="text-left mb-3">
                <p>Para confirmar, escribe exactamente:</p>
                <p class="text-center">
                    <code class="bg-dark text-white p-2 d-inline-block">BORRAR PLANOS</code>
                </p>
            </div>
            <input type="text" id="confirmacion-texto" class="form-control" placeholder="Escribe aquí...">
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ELIMINAR DEFINITIVAMENTE',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const texto = document.getElementById('confirmacion-texto').value;
            if (texto !== 'BORRAR PLANOS') {
                Swal.showValidationMessage('Debes escribir exactamente: BORRAR PLANOS');
                return false;
            }
            return texto;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarEliminarHistoricos(result.value);
        }
    });
}

function ejecutarEliminarHistoricos(confirmacion) {
    Swal.fire({
        title: 'Eliminando TODOS los planos...',
        text: 'Esta operación puede tardar varios segundos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("planos.importacion.eliminar-historicos") }}',
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            confirmacion: confirmacion
        },
        success: function(response) {
            Swal.close();

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Planos Históricos Eliminados',
                    html: `
                        <div class="text-left">
                            <p>${response.message}</p>
                            <div class="alert alert-info mt-3 mb-0">
                                <strong>Resumen:</strong>
                                <ul class="mb-0">
                                    <li>Planos eliminados: <strong>${response.planos_eliminados.toLocaleString()}</strong></li>
                                    <li>Folios eliminados: <strong>${response.folios_eliminados.toLocaleString()}</strong></li>
                                </ul>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Cerrar',
                    width: 600
                });

                // Actualizar contadores
                loadHistoricosCount();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            Swal.close();

            // Error 403: Sin control de sesión
            if (xhr.status === 403) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Control de Numeración Requerido',
                    html: `
                        <p>${xhr.responseJSON?.message || 'Necesitas tener el control activo'}</p>
                        <p class="text-muted mt-2">
                            <i class="fas fa-info-circle"></i>
                            Solicita el control desde el widget superior o la sección "Agregar Planos"
                        </p>
                    `,
                    confirmButtonText: 'Entendido'
                });
            } else {
                // Otros errores
                Swal.fire({
                    icon: 'error',
                    title: 'Error al Eliminar',
                    text: xhr.responseJSON?.message || 'No se pudo completar la eliminación de históricos'
                });
            }
        }
    });
}
</script>
@endpush