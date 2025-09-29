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
                            <option value="ENE">Enero</option>
                            <option value="FEB">Febrero</option>
                            <option value="MAR">Marzo</option>
                            <option value="ABR">Abril</option>
                            <option value="MAY">Mayo</option>
                            <option value="JUN">Junio</option>
                            <option value="JUL">Julio</option>
                            <option value="AGO">Agosto</option>
                            <option value="SEP">Septiembre</option>
                            <option value="OCT">Octubre</option>
                            <option value="NOV">Noviembre</option>
                            <option value="DIC">Diciembre</option>
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
            <span class="badge badge-primary ml-2" id="registros-encontrados-count">Cargando...</span>
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
        <!-- DataTable -->
        <div class="table-responsive">
            <table id="planos-table" class="table table-bordered table-striped table-hover table-nowrap">
                <thead>
                    <tr>
                        @if(Auth::user()->isRegistro())
                        <th width="80">Acciones</th>
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
                        <th width="80">Detalles</th>
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

<!-- Modal Detalles Completos -->
<div class="modal fade" id="modal-detalles-completos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-eye"></i>
                    Detalles Completos del Plano
                    <span id="modal-numero-plano" class="badge badge-info ml-2"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-detalles-content">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Cargando detalles...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

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
        { "orderable": false, "targets": [0] }, // Acciones
        { "orderable": false, "targets": [-1, -2] }, // Expandir y Detalles
        @else
        { "orderable": false, "targets": [-1, -2] }, // Expandir y Detalles
        @endif
        { "className": "text-center", "targets": [0, -1, -2] },
        { "className": "nowrap", "targets": "_all" } // No wrap para mejor visualización
    ];

    const columns = [
        @if(Auth::user()->isRegistro())
        { "data": "acciones", "name": "acciones" },
        @endif
        { "data": "numero_plano_completo", "name": "numero_plano_completo" },
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
        { "data": "expandir", "name": "expandir" },
        { "data": "detalles", "name": "detalles" }
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
        dom: '<"row"<"col-sm-12 col-md-8"f><"col-sm-12 col-md-4 text-right"l>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
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

            // Configurar filas expandibles clickeables
            setupExpandibleRows();

            // Actualizar contador de registros
            updateRegistrosCount();
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

    // Event listener para ver detalles completos
    $('#planos-table tbody').on('click', '.ver-detalles', function() {
        const id = $(this).data('id');
        verDetallesCompletos(id);
    });

    // El control de paginación ahora es manejado automáticamente por DataTables
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

function setupExpandibleRows() {
    // Remover event listeners previos para evitar duplicados
    $('#planos-table tbody tr').off('click.expandible');

    // Configurar cada fila según la cantidad de folios del botón expandir
    $('#planos-table tbody tr').each(function() {
        const $row = $(this);
        const $expandBtn = $row.find('.expandir-folios');

        if ($expandBtn.length) {
            const foliosCount = parseInt($expandBtn.data('folios')) || 0;

            if (foliosCount > 1) {
                // Hacer fila expandible
                $row.addClass('expandible-row');

                // Agregar event listener para toda la fila EXCEPTO botones y enlaces
                $row.on('click.expandible', function(e) {
                    // No expandir si se hizo clic en:
                    // - Botones (.btn)
                    // - Enlaces (a)
                    // - Elementos con clase actions-column
                    // - El botón expandir/colapsar específicamente
                    if (!$(e.target).closest('.btn, a, .actions-column, .expandir-folios').length) {
                        $expandBtn.trigger('click');
                    }
                });
            } else {
                // Remover clase expandible si tiene 1 o menos folios
                $row.removeClass('expandible-row');
            }
        }
    });
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

        // NO colapsar automáticamente - el usuario decide manualmente
    });

    // Limpiar filtros
    $('#limpiar-filtros').on('click', function() {
        $('#filtros-form')[0].reset();
        $('.select2').val(null).trigger('change');
        planosTable.draw();
        updateFiltrosCount();
    });

    // Toggle panel de filtros (botón específico)
    $('#toggle-filtros').on('click', function() {
        const icon = $(this).find('i');
        if (icon.hasClass('fa-plus')) {
            icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            icon.removeClass('fa-minus').addClass('fa-plus');
        }
    });

    // Hacer clickeable todo el header de filtros (EXCEPTO el botón)
    $('#filtros-card .card-header').on('click', function(e) {
        // No activar si se hizo clic en el botón específico o sus elementos hijos
        if (!$(e.target).closest('.btn-tool, .card-tools').length) {
            // Simular clic en el botón toggle
            $('#toggle-filtros').trigger('click');
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

function updateRegistrosCount() {
    // Obtener información de DataTables
    const info = planosTable.page.info();
    const total = info.recordsDisplay; // Registros después de filtrar
    const totalSinFiltro = info.recordsTotal; // Total sin filtrar

    let texto = '';
    if (total === totalSinFiltro) {
        // Sin filtros aplicados
        texto = `Total: ${total} registros`;
    } else {
        // Con filtros aplicados
        texto = `Registros encontrados: ${total}`;

        // Cambiar color del badge según si hay filtros
        $('#registros-encontrados-count')
            .removeClass('badge-primary badge-success')
            .addClass('badge-success');
    }

    if (total === totalSinFiltro) {
        $('#registros-encontrados-count')
            .removeClass('badge-success badge-primary')
            .addClass('badge-primary');
    }

    $('#registros-encontrados-count').text(texto);
}

function verDetallesCompletos(id) {
    // Mostrar modal con loading
    $('#modal-detalles-completos').modal('show');

    // Resetear contenido
    $('#modal-detalles-content').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);

    // Cargar datos del plano
    $.get("{{ url('/planos') }}/" + id + "/detalles-completos")
        .done(function(response) {
            if (response.success) {
                $('#modal-numero-plano').text(response.plano.numero_plano_completo);
                $('#modal-detalles-content').html(response.html);
            } else {
                $('#modal-detalles-content').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error: ${response.message}
                    </div>
                `);
            }
        })
        .fail(function() {
            $('#modal-detalles-content').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error al cargar los detalles del plano
                </div>
            `);
        });
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

@push('styles')
<style>
/* Mejoras de visualización para la tabla */
.table-nowrap {
    white-space: nowrap;
}

#planos-table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

#planos-table th {
    white-space: nowrap;
}

/* Columnas específicas */
#planos-table td:nth-child(1) { max-width: 80px; } /* Acciones */
#planos-table td:nth-child(2) { max-width: 130px; } /* N° Plano Completo */
#planos-table td:nth-child(3) { max-width: 120px; } /* Folios */
#planos-table td:nth-child(4) { max-width: 120px; } /* Solicitante */
#planos-table td:nth-child(5) { max-width: 100px; } /* Ap. Paterno */
#planos-table td:nth-child(6) { max-width: 100px; } /* Ap. Materno */
#planos-table td:nth-child(7) { max-width: 100px; } /* Comuna */
#planos-table td:nth-child(8) { max-width: 80px; }  /* Hectáreas */
#planos-table td:nth-child(9) { max-width: 80px; }  /* M² */
#planos-table td:nth-child(10) { max-width: 60px; } /* Mes */
#planos-table td:nth-child(11) { max-width: 60px; } /* Año */
#planos-table td:nth-child(12) { max-width: 120px; } /* Responsable */
#planos-table td:nth-child(13) { max-width: 120px; } /* Proyecto */
#planos-table td:nth-child(14) { max-width: 50px; } /* Expandir */

/* Botones más compactos */
.btn-group .btn {
    padding: 2px 6px;
    margin: 0;
}

/* Tooltip para contenido truncado */
#planos-table td[title] {
    cursor: help;
}

/* Mejorar estilos de búsqueda DataTables */
#planos-table_filter {
    text-align: left !important;
}

.dataTables_filter {
    text-align: left !important;
}

.dataTables_filter label {
    float: left !important;
    text-align: left !important;
}

.dataTables_filter input {
    margin-left: 0 !important;
    margin-right: 0.5em;
    border-radius: 0.25rem;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
    display: inline-block !important;
}

.dataTables_length {
    text-align: right !important;
    margin-top: 0;
}

.dataTables_length label {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    margin-bottom: 0 !important;
    font-weight: normal !important;
}

.dataTables_length select {
    margin: 0 0.5em !important;
    border-radius: 0.25rem;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
    min-width: 80px;
}

/* Mejorar espaciado general entre filtro y length */
.dataTables_filter {
    margin-bottom: 0 !important;
}

.dataTables_filter label {
    display: flex !important;
    align-items: center !important;
    margin-bottom: 0 !important;
    font-weight: normal !important;
}

/* Filas expandibles clickeables */
tr.expandible-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

tr.expandible-row:hover {
    background-color: #f8f9fa !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Los botones de acción mantienen su cursor default */
tr.expandible-row .btn {
    cursor: pointer; /* Mantener cursor de botón */
}

/* Header de filtros clickeable */
#filtros-card .card-header {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

#filtros-card .card-header:hover {
    background-color: #f8f9fa !important;
}

/* El botón de toggle mantiene su cursor normal */
#filtros-card .card-header .btn-tool {
    cursor: pointer; /* Mantener cursor de botón */
}
</style>
@endpush