@extends('adminlte::page')

@section('title', 'Importar Planos Históricos')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>📂 Importación Planos Históricos</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.planos.index') }}">Planos</a></li>
                <li class="breadcrumb-item active">Importar Históricos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">

    <!-- Información del proceso -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">📋 Información Importante</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>🎯 Proceso de Importación:</h5>
                            <ul class="list-unstyled">
                                <li>• Agrupación automática por: CODIGO_REGIONAL + CODIGO_COMUNAL + N°_PLANO + URBANO/RURAL</li>
                                <li>• Cada grupo único = 1 plano con múltiples folios</li>
                                <li>• Validación previa antes de importar</li>
                                <li>• Transacción completa (todo o nada)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>📊 Estructura Excel Esperada (24 columnas):</h5>
                            <small class="text-muted">
                                CODIGO_REGIONAL | CODIGO_COMUNAL | N°_PLANO | URBANO/RURAL | FOLIO | SOLICITANTE |
                                PATERNO | MATERNO | COMUNA | HIJ | HA | M² | SITIO | M² | FECHA | AÑO |
                                Responsable | PROYECTO | PROVIDENCIA | ARCHIVO | OBSERVACION | TUBO | TELA | ARCHIVO_DIGITAL
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de importación -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">📤 Seleccionar Archivo Excel</h3>
                </div>
                <div class="card-body">
                    <form id="form-importacion" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="archivo_excel">Archivo Excel (.xlsx/.xls)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls" required>
                                            <label class="custom-file-label" for="archivo_excel">Seleccionar archivo...</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="btn-preview">
                                                <i class="fas fa-eye"></i> Vista Previa
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Máximo 10MB. Solo archivos .xlsx o .xls</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <button type="button" class="btn btn-success btn-lg" id="btn-importar" disabled>
                                    <i class="fas fa-upload"></i> Importar Planos
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de resultados -->
    <div class="row" id="area-resultados" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📊 Resultados de Procesamiento</h3>
                </div>
                <div class="card-body" id="contenido-resultados">
                    <!-- Se llena dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de progreso -->
    <div class="modal fade" id="modal-progreso" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">
                        <i class="fas fa-cog fa-spin"></i> Procesando Importación
                    </h4>
                </div>
                <div class="modal-body text-center">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 100%"></div>
                    </div>
                    <p id="texto-progreso">Analizando archivo Excel...</p>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    let archivoSeleccionado = false;
    let datosPreview = null;

    // Actualizar label del archivo
    $('#archivo_excel').on('change', function() {
        const fileName = $(this)[0].files[0]?.name || 'Seleccionar archivo...';
        $(this).siblings('.custom-file-label').text(fileName);
        archivoSeleccionado = !!$(this)[0].files[0];
        $('#btn-importar').prop('disabled', !archivoSeleccionado);
        limpiarResultados();
    });

    // Vista previa
    $('#btn-preview').click(function() {
        if (!archivoSeleccionado) {
            toastr.warning('Selecciona un archivo primero');
            return;
        }

        const formData = new FormData();
        formData.append('archivo_excel', $('#archivo_excel')[0].files[0]);
        formData.append('_token', $('input[name="_token"]').val());

        mostrarProgreso('Analizando archivo Excel...');

        $.ajax({
            url: '{{ route("admin.planos.historico.preview") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                ocultarProgreso();
                if (response.success) {
                    datosPreview = response;
                    mostrarPreview(response);
                    $('#btn-importar').prop('disabled', response.grupos_validos === 0);
                } else {
                    toastr.error(response.message || 'Error en vista previa');
                }
            },
            error: function(xhr) {
                ocultarProgreso();
                let mensaje = 'Error al procesar archivo';
                if (xhr.responseJSON?.message) {
                    mensaje = xhr.responseJSON.message;
                }
                toastr.error(mensaje);
            }
        });
    });

    // Importar
    $('#btn-importar').click(function() {
        if (!archivoSeleccionado) {
            toastr.warning('Selecciona un archivo primero');
            return;
        }

        // Confirmar importación
        if (!confirm('¿Estás seguro de importar estos planos históricos?\n\nEsta acción no se puede deshacer.')) {
            return;
        }

        const formData = new FormData();
        formData.append('archivo_excel', $('#archivo_excel')[0].files[0]);
        formData.append('_token', $('input[name="_token"]').val());

        mostrarProgreso('Importando planos históricos...');

        $.ajax({
            url: '{{ route("admin.planos.historico.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                ocultarProgreso();
                if (response.success) {
                    mostrarResultadoImportacion(response.resultado);
                    toastr.success(response.message);

                    // Limpiar formulario
                    $('#form-importacion')[0].reset();
                    $('.custom-file-label').text('Seleccionar archivo...');
                    archivoSeleccionado = false;
                    $('#btn-importar').prop('disabled', true);
                } else {
                    mostrarResultadoImportacion(response.resultado);
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                ocultarProgreso();
                let mensaje = 'Error en la importación';
                if (xhr.responseJSON?.message) {
                    mensaje = xhr.responseJSON.message;
                }
                toastr.error(mensaje);
            }
        });
    });

    function mostrarProgreso(texto) {
        $('#texto-progreso').text(texto);
        $('#modal-progreso').modal('show');
    }

    function ocultarProgreso() {
        $('#modal-progreso').modal('hide');
    }

    function limpiarResultados() {
        $('#area-resultados').hide();
        datosPreview = null;
    }

    function mostrarPreview(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-file-excel"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Filas</span>
                            <span class="info-box-number">${data.total_filas}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-primary">
                        <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Grupos Únicos</span>
                            <span class="info-box-number">${data.total_grupos}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Válidos</span>
                            <span class="info-box-number">${data.grupos_validos}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Con Errores</span>
                            <span class="info-box-number">${data.grupos_invalidos}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Mostrar errores si los hay
        if (data.grupos_invalidos > 0) {
            html += '<div class="alert alert-danger"><h5>⚠️ Errores Encontrados:</h5><ul>';
            for (const [grupo, errores] of Object.entries(data.errores)) {
                html += `<li><strong>${grupo}:</strong><ul>`;
                errores.forEach(error => {
                    html += `<li>${error}</li>`;
                });
                html += '</ul></li>';
            }
            html += '</ul></div>';
        }

        // Estado de importación
        if (data.grupos_validos > 0) {
            html += `<div class="alert alert-success">
                ✅ <strong>Listo para importar:</strong> ${data.grupos_validos} grupos válidos serán procesados.
            </div>`;
        } else {
            html += `<div class="alert alert-warning">
                ❌ <strong>No se puede importar:</strong> Corrige los errores antes de proceder.
            </div>`;
        }

        $('#contenido-resultados').html(html);
        $('#area-resultados').show();
    }

    function mostrarResultadoImportacion(resultado) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-map"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Planos Creados</span>
                            <span class="info-box-number">${resultado.planos_creados}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-folder"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Folios Creados</span>
                            <span class="info-box-number">${resultado.folios_creados}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Errores</span>
                            <span class="info-box-number">${resultado.errores_criticos}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Mostrar errores si los hay
        if (resultado.errores_criticos > 0) {
            html += '<div class="alert alert-danger"><h5>❌ Errores Durante Importación:</h5><ul>';
            for (const [grupo, errores] of Object.entries(resultado.errores)) {
                html += `<li><strong>${grupo}:</strong><ul>`;
                errores.forEach(error => {
                    html += `<li>${error}</li>`;
                });
                html += '</ul></li>';
            }
            html += '</ul></div>';
        }

        if (resultado.planos_creados > 0) {
            html += `<div class="alert alert-success">
                ✅ <strong>Importación exitosa:</strong> Se crearon ${resultado.planos_creados} planos con ${resultado.folios_creados} folios.
            </div>`;
        }

        $('#contenido-resultados').html(html);
        $('#area-resultados').show();
    }
});
</script>
@stop

@section('css')
<style>
.info-box {
    margin-bottom: 15px;
}
.progress {
    height: 20px;
}
.card-header {
    position: relative;
}
</style>
@stop