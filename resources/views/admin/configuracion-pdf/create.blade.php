@extends('layouts.admin')

@section('title', 'Crear Configuración PDF')

@section('page-title', 'Agregar Ruta de PDFs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.configuracion-pdf.index') }}">Configuración PDFs</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Nueva Configuración de Ruta</h3>
            </div>

            <form action="{{ route('admin.configuracion-pdf.store') }}" method="POST" id="form-crear">
                @csrf

                <div class="card-body">
                    <!-- Año -->
                    <div class="form-group">
                        <label for="ano">
                            Año <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               class="form-control @error('ano') is-invalid @enderror"
                               id="ano"
                               name="ano"
                               min="2000"
                               max="2100"
                               value="{{ old('ano', date('Y')) }}"
                               required
                               placeholder="Ej: 2025">
                        @error('ano')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Año al que corresponde esta configuración de ruta.
                        </small>
                    </div>

                    <!-- Ruta Base -->
                    <div class="form-group">
                        <label for="ruta_base">
                            Ruta Base <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control @error('ruta_base') is-invalid @enderror"
                                   id="ruta_base"
                                   name="ruta_base"
                                   value="{{ old('ruta_base') }}"
                                   required
                                   placeholder="Ej: Z:\Planos\2025 o C:\PDFs\2025">
                            <div class="input-group-append">
                                <button type="button"
                                        class="btn btn-info"
                                        id="btn-verificar"
                                        title="Verificar si la ruta existe">
                                    <i class="fas fa-check-circle"></i> Verificar
                                </button>
                            </div>
                            @error('ruta_base')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Ruta completa donde se encuentran los archivos PDF.
                            Puede ser local (<code>C:\...</code>) o de red (<code>Z:\...</code>).
                        </small>
                        <div id="resultado-verificacion" class="mt-2"></div>
                    </div>

                    <!-- Estado Activo -->
                    <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-on-success">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="activo"
                                   name="activo"
                                   {{ old('activo', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="activo">
                                Configuración activa
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Solo las configuraciones activas serán utilizadas para buscar PDFs.
                        </small>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                    <a href="{{ route('admin.configuracion-pdf.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Panel de ayuda -->
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Ayuda</h3>
            </div>
            <div class="card-body">
                <h6><strong>Ejemplos de rutas:</strong></h6>
                <ul>
                    <li><code>C:\PDFs\2025</code></li>
                    <li><code>Z:\Planos\2025</code></li>
                    <li><code>\\servidor\compartida\planos\2025</code></li>
                </ul>

                <hr>

                <h6><strong>Requisitos:</strong></h6>
                <ul>
                    <li>La ruta debe ser accesible desde el servidor</li>
                    <li>Debe tener permisos de lectura</li>
                    <li>Los PDFs deben nombrarse con el número de plano</li>
                </ul>

                <hr>

                <h6><strong>Nombrado de archivos:</strong></h6>
                <p class="small mb-0">
                    Los PDFs pueden tener varios formatos de nombre:
                </p>
                <ul class="small">
                    <li><code>0810129551SU.pdf</code></li>
                    <li><code>0810129551SU copia.pdf</code></li>
                    <li><code>copia 0810129551SU.pdf</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Verificar ruta cuando se hace clic en el botón
    $('#btn-verificar').on('click', function() {
        const ruta = $('#ruta_base').val().trim();
        const btn = $(this);
        const resultadoDiv = $('#resultado-verificacion');

        if (!ruta) {
            resultadoDiv.html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Por favor ingresa una ruta</div>');
            return;
        }

        // Deshabilitar botón y mostrar loading
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

        $.ajax({
            url: '{{ route("admin.configuracion-pdf.verificar-ruta") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ruta: ruta
            },
            success: function(response) {
                if (response.existe && response.legible) {
                    resultadoDiv.html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> ${response.mensaje}
                        </div>
                    `);
                } else if (response.existe) {
                    resultadoDiv.html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> ${response.mensaje}
                        </div>
                    `);
                } else {
                    resultadoDiv.html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> ${response.mensaje}
                        </div>
                    `);
                }
            },
            error: function() {
                resultadoDiv.html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Error al verificar la ruta
                    </div>
                `);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Verificar');
            }
        });
    });
});
</script>
@endpush
