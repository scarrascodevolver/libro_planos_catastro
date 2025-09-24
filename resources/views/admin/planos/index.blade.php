@extends('layouts.admin')

@section('title', 'Tabla General')

@section('page-title', 'Libro de Planos Topográficos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Tabla General</li>
@endsection

@section('content')
<!-- Filtros Card -->
<div class="card collapsed-card" id="filtros-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Filtros Avanzados
            <span class="badge badge-info ml-2" id="filtros-activos-count">0</span>
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" id="toggle-filtros">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body" style="display: none;">
        <form id="filtros-form">
            <!-- Fila 1: Básicos -->
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_comuna">Comuna</label>
                        <select class="form-control select2" id="filtro_comuna" name="comuna">
                            <option value="">Todas</option>
                            @foreach($comunas as $codigo => $nombre)
                                <option value="{{ $nombre }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_ano">Año</label>
                        <select class="form-control" id="filtro_ano" name="ano">
                            <option value="">Todos</option>
                            @foreach($anos as $ano)
                                <option value="{{ $ano }}">{{ $ano }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_mes">Mes</label>
                        <select class="form-control" id="filtro_mes" name="mes">
                            <option value="">Todos</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_responsable">Responsable</label>
                        <select class="form-control select2" id="filtro_responsable" name="responsable">
                            <option value="">Todos</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable }}">{{ $responsable }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_proyecto">Proyecto</label>
                        <select class="form-control select2" id="filtro_proyecto" name="proyecto">
                            <option value="">Todos</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto }}">{{ $proyecto }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fila 2: Personas -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_folio">Folio</label>
                        <input type="text" class="form-control" id="filtro_folio" name="folio" placeholder="Número de folio">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_solicitante">Solicitante</label>
                        <input type="text" class="form-control" id="filtro_solicitante" name="solicitante" placeholder="Nombre solicitante">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_apellido_paterno">Apellido Paterno</label>
                        <input type="text" class="form-control" id="filtro_apellido_paterno" name="apellido_paterno" placeholder="Apellido paterno">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_apellido_materno">Apellido Materno</label>
                        <input type="text" class="form-control" id="filtro_apellido_materno" name="apellido_materno" placeholder="Apellido materno">
                    </div>
                </div>
            </div>

            <!-- Fila 3: Rangos -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Hectáreas</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" step="0.01" class="form-control" id="filtro_hectareas_min" name="hectareas_min" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" step="0.01" class="form-control" id="filtro_hectareas_max" name="hectareas_max" placeholder="Max">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>M²</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" id="filtro_m2_min" name="m2_min" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" id="filtro_m2_max" name="m2_max" placeholder="Max">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group d-block">
                            <button type="button" class="btn btn-primary" id="aplicar-filtros">
                                <i class="fas fa-search"></i> Aplicar Filtros
                            </button>
                            <button type="button" class="btn btn-secondary" id="limpiar-filtros">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla Principal Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-table"></i>
            Registro de Planos
        </h3>
        <div class="card-tools">
            <div class="btn-group">
                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" id="export-excel">
                        <i class="fas fa-file-excel text-success"></i> Excel
                    </a>
                    <a class="dropdown-item" href="#" id="export-pdf">
                        <i class="fas fa-file-pdf text-danger"></i> PDF
                    </a>
                    <a class="dropdown-item" href="#" id="print-table">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                </div>
            </div>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Info de registros -->
        <div class="row mb-3">
            <div class="col-sm-6">
                <div id="planos-table_info" class="dataTables_info"></div>
            </div>
            <div class="col-sm-6">
                <div class="dataTables_length float-right">
                    <label>Mostrar
                        <select id="table-length" class="custom-select custom-select-sm form-control form-control-sm">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="-1">Todos</option>
                        </select> registros
                    </label>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="table-responsive">
            <table id="planos-table" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        @if(Auth::user()->isRegistro())
                        <th width="50">Edit</th>
                        <th width="50">Reasi</th>
                        @endif
                        <th>N° Plano</th>
                        <th>Folios</th>
                        <th>Solicitante</th>
                        <th>Ap. Paterno</th>
                        <th>Ap. Materno</th>
                        <th>Comuna</th>
                        <th>Hectáreas</th>
                        <th>M²</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Responsable</th>
                        <th>Proyecto</th>
                        <th width="30">+/-</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Plano -->
@include('admin.planos.modals.edit-plano')

<!-- Modal Reasignar Número -->
@include('admin.planos.modals.reasignar-numero')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    initPlanosTable();
    initFiltros();
    initModals();
});

// Variables globales
let planosTable;
let expandedRows = {};

function initPlanosTable() {
    const columnDefs = [
        @if(Auth::user()->isRegistro())
        { "orderable": false, "targets": [0, 1] }, // Edit, Reasignar
        { "orderable": false, "targets": -1 }, // Expandir
        @else
        { "orderable": false, "targets": -1 }, // Expandir
        @endif
        { "className": "text-center", "targets": [0, 1, -1] }
    ];

    const columns = [
        @if(Auth::user()->isRegistro())
        { "data": "acciones", "name": "acciones" },
        { "data": "acciones", "name": "acciones" },
        @endif
        { "data": "numero_plano", "name": "numero_plano" },
        { "data": "folios_display", "name": "folios_display" },
        { "data": "solicitante_display", "name": "solicitante_display" },
        { "data": "apellido_paterno_display", "name": "apellido_paterno_display" },
        { "data": "apellido_materno_display", "name": "apellido_materno_display" },
        { "data": "comuna", "name": "comuna" },
        { "data": "hectareas_display", "name": "hectareas_display" },
        { "data": "m2_display", "name": "m2_display" },
        { "data": "mes_display", "name": "mes_display" },
        { "data": "ano_display", "name": "ano_display" },
        { "data": "responsable", "name": "responsable" },
        { "data": "proyecto", "name": "proyecto" },
        { "data": "expandir", "name": "expandir" }
    ];

    planosTable = $('#planos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('planos.index') }}",
            data: function(d) {
                // Añadir filtros a la consulta
                d.comuna = $('#filtro_comuna').val();
                d.ano = $('#filtro_ano').val();
                d.mes = $('#filtro_mes').val();
                d.responsable = $('#filtro_responsable').val();
                d.proyecto = $('#filtro_proyecto').val();
                d.folio = $('#filtro_folio').val();
                d.solicitante = $('#filtro_solicitante').val();
                d.apellido_paterno = $('#filtro_apellido_paterno').val();
                d.apellido_materno = $('#filtro_apellido_materno').val();
                d.hectareas_min = $('#filtro_hectareas_min').val();
                d.hectareas_max = $('#filtro_hectareas_max').val();
                d.m2_min = $('#filtro_m2_min').val();
                d.m2_max = $('#filtro_m2_max').val();
            }
        },
        columns: columns,
        columnDefs: columnDefs,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        drawCallback: function() {
            // Reabrir filas expandidas
            Object.keys(expandedRows).forEach(function(id) {
                if (expandedRows[id]) {
                    expandRow(id);
                }
            });
        }
    });

    // Event listeners para expansión
    $('#planos-table tbody').on('click', '.expandir-folios', function() {
        const btn = $(this);
        const id = btn.data('id');
        const icon = btn.find('i');

        if (icon.hasClass('fa-plus')) {
            expandRow(id);
        } else {
            collapseRow(id);
        }
    });

    @if(Auth::user()->isRegistro())
    // Event listeners para edición
    $('#planos-table tbody').on('click', '.editar-plano', function() {
        const id = $(this).data('id');
        editarPlano(id);
    });

    $('#planos-table tbody').on('click', '.reasignar-plano', function() {
        const id = $(this).data('id');
        reasignarPlano(id);
    });
    @endif

    // Control de paginación externa
    $('#table-length').on('change', function() {
        planosTable.page.len($(this).val()).draw();
    });
}

function expandRow(id) {
    $.get("{{ url('/planos') }}/" + id + "/folios-expansion")
        .done(function(response) {
            const btn = $('button[data-id="' + id + '"]');
            const row = btn.closest('tr');

            // Cambiar icono
            btn.find('i').removeClass('fa-plus').addClass('fa-minus');

            // Insertar filas hijas
            $(response.html).insertAfter(row);

            // Marcar como expandido
            expandedRows[id] = true;
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los folios', 'error');
        });
}

function collapseRow(id) {
    const btn = $('button[data-id="' + id + '"]');
    const row = btn.closest('tr');

    // Cambiar icono
    btn.find('i').removeClass('fa-minus').addClass('fa-plus');

    // Remover filas hijas
    row.nextUntil(':not(.child-row)').remove();

    // Marcar como colapsado
    expandedRows[id] = false;
}

function initFiltros() {
    // Select2 para filtros
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccionar...'
    });

    // Aplicar filtros
    $('#aplicar-filtros').on('click', function() {
        planosTable.draw();
        updateFiltrosCount();

        // Colapsar panel de filtros
        $('#filtros-card').CardWidget('collapse');
    });

    // Limpiar filtros
    $('#limpiar-filtros').on('click', function() {
        $('#filtros-form')[0].reset();
        $('.select2').val(null).trigger('change');
        planosTable.draw();
        updateFiltrosCount();
    });

    // Toggle panel de filtros
    $('#toggle-filtros').on('click', function() {
        const icon = $(this).find('i');
        if (icon.hasClass('fa-plus')) {
            icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });
}

function updateFiltrosCount() {
    let count = 0;
    $('#filtros-form input, #filtros-form select').each(function() {
        if ($(this).val() && $(this).val() !== '') {
            count++;
        }
    });
    $('#filtros-activos-count').text(count);
}

@if(Auth::user()->isRegistro())
function editarPlano(id) {
    $.get("{{ url('/planos') }}/" + id + "/edit")
        .done(function(response) {
            // Poblar modal de edición
            $('#edit-modal #edit_id').val(id);
            $('#edit-modal #edit_comuna').val(response.plano.comuna);
            $('#edit-modal #edit_responsable').val(response.plano.responsable);
            $('#edit-modal #edit_proyecto').val(response.plano.proyecto);
            $('#edit-modal #edit_observaciones').val(response.plano.observaciones);
            $('#edit-modal #edit_archivo').val(response.plano.archivo);
            $('#edit-modal #edit_tubo').val(response.plano.tubo);
            $('#edit-modal #edit_tela').val(response.plano.tela);
            $('#edit-modal #edit_archivo_digital').val(response.plano.archivo_digital);

            $('#edit-modal').modal('show');
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo cargar la información del plano', 'error');
        });
}

function reasignarPlano(id) {
    $('#reasignar-modal #reasignar_id').val(id);
    $('#reasignar-modal').modal('show');
}

function initModals() {
    // Modal Editar - Submit
    $('#form-edit-plano').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: "{{ url('/planos') }}/" + id,
            method: 'PUT',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire('¡Éxito!', response.message, 'success');
                    $('#edit-modal').modal('hide');
                    planosTable.draw(false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo actualizar el plano', 'error');
            }
        });
    });

    // Modal Reasignar - Submit
    $('#form-reasignar-plano').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reasignar_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: "{{ url('/planos') }}/" + id + "/reasignar",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire('¡Éxito!', response.message, 'success');
                    $('#reasignar-modal').modal('hide');
                    planosTable.draw(false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo reasignar el número', 'error');
            }
        });
    });
}
@endif

// Exportar funciones
$('#export-excel').on('click', function(e) {
    e.preventDefault();
    planosTable.button('.buttons-excel').trigger();
});

$('#export-pdf').on('click', function(e) {
    e.preventDefault();
    planosTable.button('.buttons-pdf').trigger();
});

$('#print-table').on('click', function(e) {
    e.preventDefault();
    planosTable.button('.buttons-print').trigger();
});
</script>
@endpush