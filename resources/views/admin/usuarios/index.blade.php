@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('page-title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')

<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fas fa-users"></i> Usuarios del Sistema</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-crear">
                <i class="fas fa-plus"></i> Crear Usuario
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="usuarios-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado</th>
                    <th width="100">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @if($usuario->role === 'registro')
                            <span class="badge badge-primary">Registro</span>
                        @else
                            <span class="badge badge-secondary">Consulta</span>
                        @endif
                    </td>
                    <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editar-usuario" data-id="{{ $usuario->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($usuario->id !== auth()->id())
                        <button class="btn btn-sm btn-danger eliminar-usuario" data-id="{{ $usuario->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modal-crear">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title"><i class="fas fa-user-plus"></i> Crear Usuario</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-crear">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select class="form-control" name="role" required>
                            <option value="consulta">Consulta</option>
                            <option value="registro">Registro</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modal-editar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title"><i class="fas fa-edit"></i> Editar Usuario</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-editar">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Nueva Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select class="form-control" id="edit-role" name="role" required>
                            <option value="consulta">Consulta</option>
                            <option value="registro">Registro</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable
    $('#usuarios-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[3, 'desc']]
    });

    // Crear usuario
    $('#form-crear').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("usuarios.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Éxito', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = Object.values(errors).flat().join('<br>');
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Editar usuario
    $('.editar-usuario').on('click', function() {
        let id = $(this).data('id');
        let row = $(this).closest('tr');
        let nombre = row.find('td:eq(0)').text();
        let email = row.find('td:eq(1)').text();
        let rolBadge = row.find('td:eq(2)').text().trim();
        let rol = rolBadge === 'Registro' ? 'registro' : 'consulta';

        $('#edit-id').val(id);
        $('#edit-name').val(nombre);
        $('#edit-email').val(email);
        $('#edit-role').val(rol);
        $('#modal-editar').modal('show');
    });

    $('#form-editar').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit-id').val();
        $.ajax({
            url: '{{ url("/usuarios") }}/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Éxito', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = Object.values(errors).flat().join('<br>');
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Eliminar usuario
    $('.eliminar-usuario').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("/usuarios") }}/' + id,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Eliminado', response.message, 'success');
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
