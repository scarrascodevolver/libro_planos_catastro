@extends('layouts.admin')

@section('title', 'Importación Masiva')

@section('page-title', 'Importación Masiva')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Importación Masiva</li>
@endsection

@section('content')
<!-- Sección A: Matrix Importer -->
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            <i class="fas fa-file-excel"></i>
            Matrix Importer (Mensual)
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5>Actualización Matrix Mensual</h5>
                <p class="text-muted mb-3">
                    Importa datos desde archivo Excel Matrix para auto-completado de folios.
                    Solo se extraen 8 columnas específicas del archivo original.
                </p>

                <form id="form-matrix-import" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="archivo_matrix">Archivo Excel Matrix</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="archivo_matrix" name="archivo" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="archivo_matrix">Seleccionar archivo MATRIX-YYYY-MM.xlsx</label>
                        </div>
                        <small class="form-text text-muted">
                            Formatos aceptados: .xlsx, .xls (Máx: 10MB)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="batch_name">Nombre del Lote</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name"
                               value="MATRIX-{{ date('Y-m') }}" required maxlength="50">
                        <small class="form-text text-muted">
                            Identificador único para esta importación
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Acción con Duplicados</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="accion_duplicados" id="actualizar" value="actualizar" checked>
                            <label class="form-check-label" for="actualizar">
                                Actualizar registros existentes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="accion_duplicados" id="mantener" value="mantener">
                            <label class="form-check-label" for="mantener">
                                Mantener registros existentes
                            </label>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-info" id="preview-matrix">
                            <i class="fas fa-eye"></i> Vista Previa
                        </button>
                        <button type="submit" class="btn btn-primary" id="import-matrix" disabled>
                            <i class="fas fa-file-import"></i> Importar
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i>
                            Estado Actual
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="info-box-content">
                            <span class="info-box-text">Último Lote:</span>
                            <span class="info-box-number">{{ $ultimoBatch ?? 'Ninguno' }}</span>
                        </div>
                        <hr>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Registros:</span>
                            <span class="info-box-number" id="total-matrix">{{ number_format($totalMatrix) }}</span>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Columnas extraídas:</strong><br>
                            • Tipo Inmueble<br>
                            • Comuna<br>
                            • Nombres<br>
                            • Apellidos<br>
                            • Folio<br>
                            • Responsable<br>
                            • Convenio-Financiamiento
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección B: Historical Importer -->
<div class="card">
    <div class="card-header bg-warning">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Historical Importer (Una vez)
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5>Importación Planos Históricos</h5>
                <p class="text-muted mb-3">
                    Importa planos del sistema anterior (21 columnas completas).
                    Esta importación se realiza una sola vez para migrar datos históricos.
                </p>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> ATENCIÓN</h6>
                    <ul class="mb-0">
                        <li>Esta importación crea registros directamente en las tablas principales</li>
                        <li>Los números de plano deben ser únicos</li>
                        <li>Se recomienda hacer respaldo antes de ejecutar</li>
                        <li>Proceso irreversible - usar con precaución</li>
                    </ul>
                </div>

                <form id="form-historicos-import" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="archivo_historicos">Archivo Excel Históricos</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="archivo_historicos" name="archivo" accept=".xlsx,.xls" required>
                            <label class="custom-file-label" for="archivo_historicos">Seleccionar archivo PLANOS-HISTORICOS.xlsx</label>
                        </div>
                        <small class="form-text text-muted">
                            Formatos aceptados: .xlsx, .xls (Máx: 20MB)
                        </small>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-info" id="preview-historicos">
                            <i class="fas fa-eye"></i> Vista Previa
                        </button>
                        <button type="button" class="btn btn-warning" id="import-historicos" disabled>
                            <i class="fas fa-file-import"></i> Importar (No Implementado)
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-list"></i>
                            Estructura Esperada
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>21 columnas requeridas:</strong><br>
                            • Número Plano<br>
                            • Comuna<br>
                            • Responsable<br>
                            • Proyecto<br>
                            • Folios (múltiples)<br>
                            • Datos personales<br>
                            • Hectáreas y M²<br>
                            • Fechas<br>
                            • Archivos<br>
                            • ... y más campos específicos
                        </small>
                    </div>
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

        formData.append('archivo', archivo);

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
            url: "{{ route('planos.importacion.preview-historicos') }}",
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

        Swal.fire({
            title: '¿Confirmar importación?',
            html: 'Se procesarán <strong>' + $('#batch_name').val() + '</strong><br>' +
                  'Acción con duplicados: <strong>' + $('input[name="accion_duplicados"]:checked').next().text() + '</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, importar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                executeMatrixImport();
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

    let html = '<div class="alert alert-success">';
    html += '<h6><i class="fas fa-check-circle"></i> Archivo válido</h6>';
    html += '<p class="mb-1">' + data.mensaje + '</p>';
    html += '</div>';

    // Headers encontrados
    if (data.headersEncontrados) {
        html += '<h6>Columnas Detectadas:</h6>';
        html += '<div class="row mb-3">';
        Object.keys(data.headersEncontrados).forEach(function(key) {
            html += '<div class="col-md-6"><small><strong>' + key + ':</strong> ' + data.headersEncontrados[key] + '</small></div>';
        });
        html += '</div>';
    }

    // Tabla preview
    html += '<h6>Vista Previa (primeras ' + Math.min(data.preview.length, 10) + ' filas):</h6>';
    html += '<div class="table-responsive">';
    html += '<table class="table table-sm table-bordered">';

    // Headers
    html += '<thead class="thead-light"><tr>';
    data.headers.forEach(function(header) {
        html += '<th style="min-width: 100px;">' + (header || 'Sin nombre') + '</th>';
    });
    html += '</tr></thead>';

    // Data rows
    html += '<tbody>';
    data.preview.forEach(function(row) {
        html += '<tr>';
        row.forEach(function(cell) {
            html += '<td>' + (cell || '') + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table></div>';

    $('#preview-content').html(html);
    $('#confirm-import').show();
    $('#preview-modal').modal('show');
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
        Swal.fire('Info', 'Importación de históricos no implementada aún', 'info');
    }
});
</script>
@endpush