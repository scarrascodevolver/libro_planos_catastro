@extends('layouts.admin')

@section('title', 'Configuración de PDFs')

@section('page-title', 'Configuración de Rutas de PDFs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Configuración PDFs</li>
@endsection

@section('content')

<!-- Mensajes de éxito/error -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fas fa-folder-open"></i> Rutas de PDFs por Año</h3>
        <div class="card-tools">
            <a href="{{ route('admin.configuracion-pdf.create') }}" class="btn btn-success btn-sm text-white">
                <i class="fas fa-plus"></i> Agregar Ruta
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($configuraciones->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            No hay configuraciones de PDFs. Haz clic en <strong>Agregar Ruta</strong> para crear la primera.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="80">Año</th>
                        <th>Ruta Base</th>
                        <th width="100" class="text-center">Estado</th>
                        <th width="80" class="text-center">Acceso</th>
                        <th width="130">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($configuraciones as $config)
                    <tr>
                        <td class="text-center">
                            <strong>{{ $config->ano }}</strong>
                        </td>
                        <td>
                            <code>{{ $config->ruta_base }}</code>
                        </td>
                        <td class="text-center">
                            @if($config->activo)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Activo
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times"></i> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $existe = file_exists($config->ruta_base);
                                $legible = $existe && is_readable($config->ruta_base);
                            @endphp

                            @if($legible)
                                <span class="badge badge-success" title="Ruta accesible">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @elseif($existe)
                                <span class="badge badge-warning" title="Ruta existe pero no es legible">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                            @else
                                <span class="badge badge-danger" title="Ruta no encontrada">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.configuracion-pdf.edit', $config->id) }}"
                               class="btn btn-sm btn-warning"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button class="btn btn-sm btn-danger eliminar-config"
                                    data-id="{{ $config->id }}"
                                    data-ano="{{ $config->ano }}"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <div class="card-footer">
        <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Las rutas deben apuntar a directorios donde se almacenan los archivos PDF de planos por año.
        </small>
    </div>
</div>

<!-- Información de ayuda -->
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-question-circle"></i> Cómo funciona</h3>
    </div>
    <div class="card-body">
        <ol>
            <li>Cada año de planos debe tener una ruta configurada donde se almacenan los PDFs.</li>
            <li>La ruta puede ser local (ej: <code>C:\PDFs\2025</code>) o de red (ej: <code>Z:\Planos\2025</code>).</li>
            <li>Los archivos PDF deben nombrarse con el número de plano (ej: <code>0810129551SU.pdf</code>).</li>
            <li>El sistema buscará automáticamente los PDFs cuando se haga clic en "Ver PDF" en la tabla general.</li>
            <li>Solo las configuraciones marcadas como <strong>Activo</strong> serán utilizadas.</li>
        </ol>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Eliminar configuración
    $('.eliminar-config').on('click', function() {
        const id = $(this).data('id');
        const ano = $(this).data('ano');

        Swal.fire({
            title: '¿Eliminar configuración?',
            html: `Se eliminará la configuración del año <strong>${ano}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario y enviar DELETE
                const form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ url("admin/configuracion-pdf") }}/' + id
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
