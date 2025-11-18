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
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-info" id="preview-matrix">
                                    <i class="fas fa-eye"></i> Vista Previa
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary" id="import-matrix" disabled>
                                    <i class="fas fa-upload"></i> Importar
                                </button>
                            </div>
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
                            <button type="button" class="btn btn-sm btn-outline-info mr-1" id="preview-historicos">
                                <i class="fas fa-eye"></i> Vista Previa
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" id="import-historicos" disabled>
                                <i class="fas fa-upload"></i> Importar
                            </button>
                        </div>
                    </form>
                </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    initImportForms();
    loadEstadisticas();
});

let currentPreviewType = null;

function initImportForms() {
    // Matrix Preview
    $('#preview-matrix').on('click', function() {
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
                    $('#import-matrix').prop('disabled', false);
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

    // Históricos Preview
    $('#preview-historicos').on('click', function() {
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
                    $('#import-historicos').prop('disabled', false);
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

    // Matrix Import
    $('#form-matrix-import').on('submit', function(e) {
        e.preventDefault();
        executeMatrixImport();
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
                $('.custom-file-label').html('Seleccionar archivo MATRIX-YYYY-MM.xlsx');
                $('#import-matrix').prop('disabled', true);

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
                // Mostrar resultados
                let html = '<div class="alert alert-success">';
                html += '<h6><i class="fas fa-check-circle"></i> Importación histórica completada</h6>';
                html += '<p>' + response.message + '</p>';
                html += '<ul>';
                html += '<li>Planos creados: <strong>' + response.resultado.planos_creados + '</strong></li>';
                html += '<li>Folios creados: <strong>' + response.resultado.folios_creados + '</strong></li>';
                html += '<li>Errores críticos: <strong>' + response.resultado.errores_criticos + '</strong></li>';
                html += '</ul></div>';

                if (response.resultado.errores && Object.keys(response.resultado.errores).length > 0) {
                    html += '<div class="alert alert-warning">';
                    html += '<h6>Errores encontrados:</h6>';
                    html += '<div style="max-height: 200px; overflow-y: auto;">';
                    Object.keys(response.resultado.errores).forEach(function(grupo) {
                        html += '<h6 class="mt-2">' + grupo + ':</h6>';
                        html += '<ul>';
                        response.resultado.errores[grupo].forEach(function(error) {
                            html += '<li>' + error + '</li>';
                        });
                        html += '</ul>';
                    });
                    html += '</div></div>';
                }

                Swal.fire({
                    title: '¡Importación Completada!',
                    html: html,
                    icon: 'success',
                    width: 700,
                    confirmButtonText: 'Cerrar'
                });

                // Reset form
                $('#form-historicos-import')[0].reset();
                $('.custom-file-label').eq(1).html('Seleccionar archivo PLANOS-HISTORICOS.xlsx');
                $('#import-historicos').prop('disabled', true);

            } else {
                let errorHtml = '<p>' + response.message + '</p>';
                if (response.resultado && response.resultado.errores) {
                    errorHtml += '<div class="alert alert-danger mt-2">';
                    errorHtml += '<h6>Errores críticos:</h6>';
                    errorHtml += '<div style="max-height: 150px; overflow-y: auto;">';
                    Object.keys(response.resultado.errores).forEach(function(grupo) {
                        errorHtml += '<strong>' + grupo + ':</strong><br>';
                        response.resultado.errores[grupo].forEach(function(error) {
                            errorHtml += '• ' + error + '<br>';
                        });
                    });
                    errorHtml += '</div></div>';
                }

                Swal.fire({
                    title: 'Error en importación',
                    html: errorHtml,
                    icon: 'error',
                    width: 600
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'No se pudo completar la importación histórica', 'error');
        }
    });
}

function loadEstadisticas() {
    $.get("{{ route('planos.importacion.estadisticas-matrix') }}")
        .done(function(data) {
            $('#total-matrix').text(data.total.toLocaleString());
        });
}

// Confirm import from preview
$('#confirm-import').on('click', function() {
    $('#preview-modal').modal('hide');

    if (currentPreviewType === 'matrix') {
        $('#form-matrix-import').submit();
    } else if (currentPreviewType === 'historicos') {
        executeHistoricosImport();
    }
});
</script>
@endpush