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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_numero_plano">Número Plano</label>
                        <input type="text" class="form-control" id="filtro_numero_plano" name="numero_plano" placeholder="Número completo">
                    </div>
                </div>
                <div class="col-md-3">
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

            <!-- Fila 4: Campos Adicionales (Excel-like) -->
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_archivo">Archivo</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_archivo" name="archivo" placeholder="Archivo">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_tubo">Tubo</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_tubo" name="tubo" placeholder="Tubo">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_tela">Tela</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_tela" name="tela" placeholder="Tela">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filtro_archivo_digital">Archivo Digital</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_archivo_digital" name="archivo_digital" placeholder="Archivo digital">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_observaciones">Observaciones</label>
                        <input type="text" class="form-control form-control-sm" id="filtro_observaciones" name="observaciones" placeholder="Buscar en observaciones">
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
            <!-- Botones DataTables -->
            <div class="btn-group mr-2" id="datatable-buttons">
                <button type="button" class="btn btn-sm btn-primary" id="btn-columns">
                    <i class="fas fa-columns"></i> Columnas
                </button>
                <button type="button" class="btn btn-sm btn-success" id="btn-excel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="btn-pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button type="button" class="btn btn-sm btn-secondary" id="btn-print">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Barra compacta con controles de DataTable -->
        <div class="datatable-controls-bar-compact">
            <div class="d-flex justify-content-between align-items-center">
                <div id="datatable-search-container"></div>
                <div id="datatable-length-container"></div>
            </div>
        </div>

        <!-- DataTable con scroll horizontal inmediato -->
        <div class="datatable-container">
            <table id="planos-table" class="table table-bordered table-striped table-hover table-nowrap">
                <thead>
                    <tr>
                        <th width="100">Acciones</th>
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
                        <th>Observaciones</th>
                        <th>Archivo</th>
                        <th>Tubo</th>
                        <th>Tela</th>
                        <th>Archivo Digital</th>
                        <th>Fecha Creación</th>
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

<!-- Modal Editar Folio Individual -->
@include('admin.planos.modals.edit-folio')

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
let currentEditPlanoId = null;
let currentEditPlanoData = null;

function initPlanosTable() {
    const columnDefs = [
        { "orderable": false, "targets": [0] }, // Acciones
        { "className": "no-export", "targets": [0] }, // No exportar acciones
        { "className": "text-center", "targets": [0] },
        { "className": "nowrap", "targets": "_all" }, // No wrap para mejor visualización
        { "visible": false, "targets": [12, 13, 14, 15, 16] } // Ocultar por defecto las nuevas columnas
    ];

    const columns = [
        { "data": "acciones", "name": "acciones" },
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
        { "data": "observaciones_display", "name": "observaciones_display" },
        { "data": "archivo_display", "name": "archivo_display" },
        { "data": "tubo_display", "name": "tubo_display" },
        { "data": "tela_display", "name": "tela_display" },
        { "data": "archivo_digital_display", "name": "archivo_digital_display" },
        { "data": "created_at_display", "name": "created_at_display" }
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
                d.numero_plano = $('#filtro_numero_plano').val();
                d.archivo = $('#filtro_archivo').val();
                d.tubo = $('#filtro_tubo').val();
                d.tela = $('#filtro_tela').val();
                d.archivo_digital = $('#filtro_archivo_digital').val();
                d.observaciones = $('#filtro_observaciones').val();
            }
        },
        columns: columns,
        columnDefs: columnDefs,
        pageLength: -1,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12"f>>rt<"row"<"col-sm-12"p>><"d-none"B>',
        info: false,      // Ocultar información de registros
        lengthChange: false,  // Ocultar selector de cantidad de registros
        paging: true,     // Activar paginación
        buttons: [
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            }
        ],
        language: {
            "decimal": ",",
            "thousands": ".",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "searchPlaceholder": "Término de búsqueda",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "orderable": "Ordenar por esta columna",
                "orderableReverse": "Ordenar esta columna en orden inverso"
            },
            buttons: {
                colvis: 'Columnas',
                excel: 'Exportar Excel',
                pdf: 'Exportar PDF',
                print: 'Imprimir'
            }
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
        },
        initComplete: function() {
            // Configurar event listeners de botones después de que DataTables esté listo
            setupHeaderButtons();

            // Mover controles de DataTable a la barra fija
            moveDataTableControls();
        }
    });

    // Event listeners para expansión - YA NO NECESARIO, manejado por setupExpandibleRows()
    // Se eliminó el botón .expandir-folios, ahora las filas son clickeables directamente

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

    // Event listener para editar folio individual
    $(document).on('click', '.editar-folio', function() {
        const folioId = $(this).data('folio-id');
        editarFolio(folioId);
    });
    @endif

    // Event listener para ver detalles completos
    $('#planos-table tbody').on('click', '.ver-detalles', function() {
        const id = $(this).data('id');
        verDetallesCompletos(id);
    });

    // El control de paginación ahora es manejado automáticamente por DataTables
}

function moveDataTableControls() {
    // Mover el filtro de búsqueda a la barra fija
    setTimeout(function() {
        const searchElement = $('.dataTables_filter').first().clone();
        const lengthElement = $('.dataTables_length').first().clone();

        if (searchElement.length) {
            $('#datatable-search-container').html(searchElement);

            // Reconectar funcionalidad de búsqueda
            $('#datatable-search-container input').off('keyup').on('keyup', function() {
                const searchValue = this.value;
                planosTable.search(searchValue).draw();

                // Si se borra completamente el campo, asegurarse de recargar todo
                if (searchValue === '') {
                    planosTable.search('').draw();
                }
            });
        }

        if (lengthElement.length) {
            $('#datatable-length-container').html(lengthElement);

            // Reconectar funcionalidad de cambio de entradas
            $('#datatable-length-container select').off('change').on('change', function() {
                planosTable.page.len(this.value).draw();
            });
        }
    }, 100);
}

function setupHeaderButtons() {
    // Event listeners para botones del header - configurados después de initComplete
    $('#btn-columns').off('click').on('click', function() {
        console.log('🔥 BOTÓN COLUMNAS CLICKEADO');
        console.log('planosTable existe:', typeof planosTable);

        // Crear el selector manual
        createManualColumnSelector();
    });

    $('#btn-excel').off('click').on('click', function() {
        planosTable.button('.buttons-excel').trigger();
    });

    $('#btn-pdf').off('click').on('click', function() {
        planosTable.button('.buttons-pdf').trigger();
    });

    $('#btn-print').off('click').on('click', function() {
        planosTable.button('.buttons-print').trigger();
    });
}

function createManualColumnSelector() {
    console.log('🚀 CREANDO SELECTOR MANUAL');

    // MÉTODO DIRECTO: Crear elemento visible SIN clases CSS conflictivas

    // Remover dropdown existente
    const existing = document.querySelector('.super-visible-dropdown');
    if (existing) existing.remove();

    // Crear elemento completamente nuevo
    const dropdown = document.createElement('div');
    dropdown.className = 'super-visible-dropdown';
    dropdown.innerHTML = `
        <div style="
            position: fixed !important;
            top: 130px !important;
            right: 20px !important;
            transform: none !important;
            z-index: 999999 !important;
            background: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 6px !important;
            padding: 15px !important;
            width: 280px !important;
            max-height: 350px !important;
            font-size: 14px !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            color: #212529 !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            overflow-y: auto !important;
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                <h5 style="margin: 0; color: #495057 !important;">
                    <i class="fas fa-columns" style="margin-right: 8px; color: #007bff;"></i>
                    Seleccionar Columnas
                </h5>
                <button onclick="this.closest('.super-visible-dropdown').remove()"
                        style="background: none; border: none; font-size: 18px; color: #6c757d; cursor: pointer; padding: 0; width: 20px; height: 20px;">
                    ×
                </button>
            </div>
            <div id="columns-list" style="max-height: 280px; overflow-y: auto;"></div>
        </div>
    `;

    // Agregar al body
    document.body.appendChild(dropdown);

    // Generar lista de columnas
    const columnsList = document.getElementById('columns-list');
    const headers = planosTable.columns().header().toArray();

    headers.forEach(function(header, index) {
        const title = $(header).text().trim();
        const isVisible = planosTable.column(index).visible();
        const isExportable = !$(header).hasClass('no-export');

        if (isExportable && title) {
            const label = document.createElement('label');
            label.style.cssText = `
                display: flex !important;
                align-items: center !important;
                padding: 6px 8px !important;
                margin-bottom: 2px !important;
                cursor: pointer !important;
                border-radius: 4px !important;
                transition: background-color 0.2s !important;
            `;
            label.innerHTML = `
                <input type="checkbox" ${isVisible ? 'checked' : ''}
                       onchange="toggleColumn(${index}, this.checked)"
                       style="margin-right: 10px !important; transform: scale(1.1) !important;">
                <span style="color: #495057 !important; font-weight: 500 !important;">${title}</span>
            `;

            // Hover effect
            label.onmouseenter = function() {
                this.style.backgroundColor = '#f8f9fa';
            };
            label.onmouseleave = function() {
                this.style.backgroundColor = 'transparent';
            };

            columnsList.appendChild(label);
        }
    });

    console.log('✅ DROPDOWN SÚPER VISIBLE CREADO');
}

// Función global para toggle de columnas
window.toggleColumn = function(columnIndex, isVisible) {
    planosTable.column(columnIndex).visible(isVisible);
    console.log(`Columna ${columnIndex} ${isVisible ? 'mostrada' : 'ocultada'}`);
};

function expandRow(id) {
    $.get("{{ url('/planos') }}/" + id + "/folios-expansion")
        .done(function(response) {
            // Buscar la fila por el data-id que asignamos
            const row = $(`tr[data-id="${id}"]`);

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
    // Buscar la fila por el data-id que asignamos
    const row = $(`tr[data-id="${id}"]`);

    // Remover filas hijas
    row.nextUntil(':not(.child-row)').remove();

    // Marcar como colapsado
    expandedRows[id] = false;
}

function setupExpandibleRows() {
    // Remover event listeners previos para evitar duplicados
    $('#planos-table tbody tr').off('click.expandible');

    // EXPANSIÓN SIMPLIFICADA: Solo múltiples folios son clickeables
    $('#planos-table tbody tr').each(function() {
        const $row = $(this);

        // Obtener cantidad de folios desde el HTML directamente
        @if(Auth::user()->isRegistro())
        const foliosText = $row.find('td:nth-child(3)').text(); // Columna Folios para rol registro
        @else
        const foliosText = $row.find('td:nth-child(2)').text(); // Columna Folios para rol consulta
        @endif
        const hasMultipleFolios = foliosText.includes('+') || foliosText.includes(',');

        if (hasMultipleFolios) {
            // Solo hacer expandible si tiene múltiples folios
            $row.addClass('expandible-row');

            // Obtener ID del data attribute o de los botones
            const id = $row.find('.ver-detalles').data('id') || $row.find('.editar-plano').data('id');
            $row.attr('data-id', id);

            // Agregar event listener para toda la fila EXCEPTO botones
            $row.on('click.expandible', function(e) {
                // No expandir si se hizo clic en botones o enlaces
                if ($(e.target).closest('.btn, a, .btn-group').length > 0) {
                    return;
                }

                const rowId = $(this).data('id');
                if (expandedRows[rowId]) {
                    collapseRow(rowId);
                } else {
                    expandRow(rowId);
                }
            });
        } else {
            // Remover clase expandible si tiene 1 folio
            $row.removeClass('expandible-row');
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
        // 1. Limpiar formulario de filtros avanzados
        $('#filtros-form')[0].reset();

        // 2. Limpiar Select2 correctamente
        $('.select2').val(null).trigger('change');

        // 3. Limpiar búsqueda global de DataTable
        planosTable.search('').columns().search('');

        // 4. Limpiar input de búsqueda visual
        $('#datatable-search-container input').val('');
        $('.dataTables_filter input').val('');

        // 5. Pequeño delay para asegurar que todo se limpió
        setTimeout(function() {
            // 6. Recargar tabla con draw(false) para mantener página actual
            planosTable.draw(false);

            // 7. Actualizar contador
            updateFiltrosCount();
        }, 100);
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

    // Actualizar también el contador de registros
    setTimeout(function() {
        updateRegistrosCount();
    }, 200);
}

function obtenerFiltrosActivos() {
    return {
        comuna: $('#filtro_comuna').val() || '',
        ano: $('#filtro_ano').val() || '',
        mes: $('#filtro_mes').val() || '',
        responsable: $('#filtro_responsable').val() || '',
        proyecto: $('#filtro_proyecto').val() || '',
        folio: $('#filtro_folio').val() || '',
        solicitante: $('#filtro_solicitante').val() || '',
        apellido_paterno: $('#filtro_apellido_paterno').val() || '',
        apellido_materno: $('#filtro_apellido_materno').val() || '',
        hectareas_min: $('#filtro_hectareas_min').val() || '',
        hectareas_max: $('#filtro_hectareas_max').val() || '',
        m2_min: $('#filtro_m2_min').val() || '',
        m2_max: $('#filtro_m2_max').val() || '',
        numero_plano: $('#filtro_numero_plano').val() || '',
        archivo: $('#filtro_archivo').val() || '',
        tubo: $('#filtro_tubo').val() || '',
        tela: $('#filtro_tela').val() || '',
        archivo_digital: $('#filtro_archivo_digital').val() || '',
        observaciones: $('#filtro_observaciones').val() || ''
    };
}

function updateRegistrosCount() {
    // Obtener datos actuales de los filtros aplicados
    const filtrosData = obtenerFiltrosActivos();
    const busquedaGlobal = $('#tabla-buscar').val();

    // Agregar búsqueda global a los filtros
    const requestData = {
        ...filtrosData,
        search: busquedaGlobal || ''
    };

    // Llamar al endpoint de contadores
    $.ajax({
        url: '{{ route("planos.contadores") }}',
        method: 'GET',
        data: requestData,
        success: function(response) {
            const { totalPlanos, totalFolios, message } = response;

            // Obtener información de DataTables para comparar
            const info = planosTable.page.info();
            const totalSinFiltro = info.recordsTotal;

            let texto = message;

            // Cambiar color del badge según si hay filtros
            if (totalPlanos === totalSinFiltro) {
                // Sin filtros aplicados
                $('#registros-encontrados-count')
                    .removeClass('badge-success')
                    .addClass('badge-primary');
            } else {
                // Con filtros aplicados
                $('#registros-encontrados-count')
                    .removeClass('badge-primary')
                    .addClass('badge-success');
            }

            $('#registros-encontrados-count').text(texto);
        },
        error: function() {
            // Fallback al método anterior si hay error
            const info = planosTable.page.info();
            const total = info.recordsDisplay;
            $('#registros-encontrados-count').text(`Registros: ${total}`);
        }
    });
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

// ===== EDITAR PLANO =====
function editarPlano(id) {
    // Verificar control de sesión antes de permitir edición
    $.get('{{ route("session-control.status") }}')
        .done(function(response) {
            if (!response.hasControl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Control Requerido',
                    html: `<p>Necesitas tener control de numeración para editar planos.</p>
                           <p>Esto evita conflictos si otro usuario está editando.</p>
                           <p>Estado actual: <strong>${response.whoHasControl || 'Nadie tiene control'}</strong></p>`,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            // Si tiene control, cargar datos del plano
            $.get("{{ url('/planos') }}/" + id + "/edit")
                .done(function(editResponse) {
                    var plano = editResponse.plano;

                    // Llenar datos del plano
                    $('#edit_plano_id').val(plano.id);
                    $('#edit-numero-plano').text(plano.numero_plano || '');

                    // Buscar comuna case-insensitive y sin tildes
                    var comunaValue = plano.comuna || '';
                    var comunaEncontrada = '';

                    // Función para normalizar: quitar tildes y convertir a mayúsculas
                    function normalizeComuna(str) {
                        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase();
                    }

                    var comunaNormalizada = normalizeComuna(comunaValue);

                    $('#edit_comuna option').each(function() {
                        if (normalizeComuna($(this).val()) === comunaNormalizada) {
                            comunaEncontrada = $(this).val();
                            return false; // break
                        }
                    });
                    $('#edit_comuna').val(comunaEncontrada).trigger('change');

                    $('#edit_tipo_saneamiento').val(plano.tipo_saneamiento || 'SR');
                    $('#edit_provincia').val(plano.provincia || '');
                    $('#edit_responsable').val(plano.responsable || '');
                    $('#edit_mes').val(plano.mes || 'ENE');
                    $('#edit_ano').val(plano.ano || 2025);
                    $('#edit_proyecto').val(plano.proyecto || '');
                    $('#edit_observaciones').val(plano.observaciones || '');

                    // Llenar folios
                    var tbody = $('#folios-tbody');
                    tbody.empty();

                    var folios = plano.folios || [];
                    for (var i = 0; i < folios.length; i++) {
                        agregarFilaFolio(folios[i]);
                    }

                    // Si no hay folios, agregar una fila vacía
                    if (folios.length === 0) {
                        agregarFilaFolio();
                    }

                    actualizarResumenEdit();
                    $('#edit-modal').modal('show');

                    // Activar filtrado de comunas por provincia después de cargar datos
                    setTimeout(function() {
                        filtrarComunasPorProvincia();
                    }, 100);
                })
                .fail(function(xhr) {
                    Swal.fire('Error', 'No se pudo cargar el plano', 'error');
                });
        });
}

// Agregar fila de folio a la tabla
function agregarFilaFolio(folio) {
    folio = folio || {};

    var html = '<tr class="folio-row">';
    html += '<td><input type="text" class="form-control form-control-sm folio-num" value="' + (folio.folio || '') + '"></td>';
    html += '<td><input type="text" class="form-control form-control-sm folio-solicitante" value="' + (folio.solicitante || '') + '"></td>';
    html += '<td><input type="text" class="form-control form-control-sm folio-ap-pat" value="' + (folio.apellido_paterno || '') + '"></td>';
    html += '<td><input type="text" class="form-control form-control-sm folio-ap-mat" value="' + (folio.apellido_materno || '') + '"></td>';
    html += '<td><select class="form-control form-control-sm folio-tipo">';
    html += '<option value="HIJUELA"' + (folio.tipo_inmueble === 'HIJUELA' ? ' selected' : '') + '>HIJUELA</option>';
    html += '<option value="SITIO"' + (folio.tipo_inmueble === 'SITIO' ? ' selected' : '') + '>SITIO</option>';
    html += '</select></td>';
    html += '<td><input type="number" step="0.01" class="form-control form-control-sm folio-ha" value="' + (folio.hectareas || '') + '" onchange="actualizarResumenEdit()"></td>';
    html += '<td><input type="number" class="form-control form-control-sm folio-m2" value="' + (folio.m2 || '') + '" onchange="actualizarResumenEdit()"></td>';
    html += '<td><button type="button" class="btn btn-xs btn-danger" onclick="eliminarFilaFolio(this)" title="Eliminar"><i class="fas fa-trash"></i></button>';
    html += '<input type="hidden" class="folio-id" value="' + (folio.id || '') + '"></td>';
    html += '</tr>';

    $('#folios-tbody').append(html);
    actualizarResumenEdit();
}

// Eliminar fila de folio
function eliminarFilaFolio(btn) {
    var filas = $('#folios-tbody tr').length;
    if (filas <= 1) {
        Swal.fire('Aviso', 'Debe quedar al menos un folio', 'warning');
        return;
    }
    $(btn).closest('tr').remove();
    actualizarResumenEdit();
}

// Actualizar resumen de totales
function actualizarResumenEdit() {
    var totalFolios = $('#folios-tbody tr').length;
    var totalHa = 0;
    var totalM2 = 0;

    $('#folios-tbody tr').each(function() {
        var ha = parseFloat($(this).find('.folio-ha').val()) || 0;
        var m2 = parseInt($(this).find('.folio-m2').val()) || 0;
        totalHa += ha;
        totalM2 += m2;
    });

    $('#edit-total-folios').text(totalFolios);
    $('#resumen-folios').text(totalFolios);
    $('#resumen-hectareas').text(totalHa.toFixed(2));
    $('#resumen-m2').text(totalM2.toLocaleString());
}

// Actualizar número de plano cuando cambia la comuna
function actualizarNumeroPlanoPorComuna() {
    var selectedOption = $('#edit_comuna option:selected');
    var nuevoCodigo = selectedOption.attr('data-codigo');
    var numeroActual = $('#edit-numero-plano').text().trim();

    if (!nuevoCodigo || !numeroActual || numeroActual.length < 8) {
        return; // No hacer nada si no hay datos suficientes
    }

    // Extraer partes del número: 08 + 303 + 29272 + SU
    var codigoRegion = numeroActual.substring(0, 2); // 08
    var correlativo = numeroActual.substring(5, numeroActual.length - 2); // 29272
    var tipo = numeroActual.slice(-2); // SU

    // Construir nuevo número con el nuevo código de comuna
    var nuevoNumero = codigoRegion + nuevoCodigo.padStart(3, '0') + correlativo + tipo;

    // Actualizar display
    $('#edit-numero-plano').text(nuevoNumero);
}

// Event listener para cambio de comuna en modal de edición
$(document).ready(function() {
    $(document).on('change', '#edit_comuna', function() {
        actualizarNumeroPlanoPorComuna();
    });
});

// Guardar plano completo (plano + todos los folios)
function guardarPlanoCompleto() {
    var planoId = $('#edit_plano_id').val();

    // Recolectar datos del plano
    var planoData = {
        _token: '{{ csrf_token() }}',
        comuna: $('#edit_comuna').val(),
        tipo_saneamiento: $('#edit_tipo_saneamiento').val(),
        provincia: $('#edit_provincia').val(),
        responsable: $('#edit_responsable').val(),
        mes: $('#edit_mes').val(),
        ano: $('#edit_ano').val(),
        proyecto: $('#edit_proyecto').val(),
        observaciones: $('#edit_observaciones').val(),
        folios: []
    };

    // Recolectar datos de todos los folios y validar
    var erroresFolios = [];
    var numeroFila = 0;
    var esFiscal = planoData.tipo_saneamiento === 'CR' || planoData.tipo_saneamiento === 'CU';

    $('#folios-tbody tr').each(function() {
        numeroFila++;
        var fila = $(this);

        var folioData = {
            id: fila.find('.folio-id').val() || null,
            folio: fila.find('.folio-num').val(),
            solicitante: fila.find('.folio-solicitante').val(),
            apellido_paterno: fila.find('.folio-ap-pat').val(),
            apellido_materno: fila.find('.folio-ap-mat').val(),
            tipo_inmueble: fila.find('.folio-tipo').val(),
            hectareas: fila.find('.folio-ha').val() || null,
            m2: fila.find('.folio-m2').val() || null
        };

        // Validar campos obligatorios
        var camposFaltantes = [];

        if (!esFiscal && !folioData.folio) {
            camposFaltantes.push('Folio');
        }
        if (!folioData.solicitante || folioData.solicitante.trim() === '') {
            camposFaltantes.push('Solicitante');
        }
        if (!folioData.apellido_paterno || folioData.apellido_paterno.trim() === '') {
            camposFaltantes.push('Apellido Paterno');
        }
        if (!folioData.apellido_materno || folioData.apellido_materno.trim() === '') {
            camposFaltantes.push('Apellido Materno');
        }
        if (!folioData.m2 || folioData.m2 <= 0) {
            camposFaltantes.push('M²');
        }

        if (camposFaltantes.length > 0) {
            erroresFolios.push({
                fila: numeroFila,
                campos: camposFaltantes
            });
        }

        planoData.folios.push(folioData);
    });

    // Validar que haya al menos un folio
    if (planoData.folios.length === 0) {
        Swal.fire('Error', 'Debe agregar al menos un folio', 'error');
        return;
    }

    // Si hay errores de validación, mostrar mensaje detallado
    if (erroresFolios.length > 0) {
        var mensajeError = '<div class="text-left"><p><strong>Los siguientes folios tienen campos vacíos:</strong></p><ul>';
        erroresFolios.forEach(function(error) {
            mensajeError += '<li><strong>Fila #' + error.fila + ':</strong> Falta ' + error.campos.join(', ') + '</li>';
        });
        mensajeError += '</ul><p class="mt-2">Complete los datos o elimine las filas vacías antes de guardar.</p></div>';

        Swal.fire({
            icon: 'warning',
            title: 'Folios Incompletos',
            html: mensajeError,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f39c12'
        });
        return;
    }

    // Enviar al servidor
    $.ajax({
        url: "{{ url('/planos') }}/" + planoId + "/update-completo",
        method: 'POST',
        data: planoData,
        success: function(response) {
            if (response.success) {
                $('#edit-modal').modal('hide');
                planosTable.draw(false);
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: response.message || 'Plano actualizado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', response.message || 'Error al guardar', 'error');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar';
            Swal.fire('Error', msg, 'error');
        }
    });
}

// ===== FILTRADO PROVINCIA-COMUNA DEPENDIENTE =====
function filtrarComunasPorProvincia() {
    var provinciaSeleccionada = $('#edit_provincia').val();
    var comunaActual = $('#edit_comuna').val();

    // Mostrar todas las comunas primero
    $('#edit_comuna option').show();

    // Si hay provincia seleccionada, filtrar
    if (provinciaSeleccionada) {
        $('#edit_comuna option').each(function() {
            var option = $(this);
            var provinciaCom = option.data('provincia');

            // No filtrar el option vacío
            if (option.val() === '') {
                return;
            }

            // Ocultar comunas que no pertenecen a la provincia seleccionada
            if (provinciaCom !== provinciaSeleccionada) {
                option.hide();

                // Si la comuna actual no pertenece a la nueva provincia, resetear
                if (option.val() === comunaActual) {
                    $('#edit_comuna').val('');
                }
            }
        });
    }
}

// Event listener para cambio de provincia
$(document).on('change', '#edit_provincia', function() {
    filtrarComunasPorProvincia();
});

// También actualizar provincia automáticamente cuando se selecciona comuna
$(document).on('change', '#edit_comuna', function() {
    var comunaSeleccionada = $(this).val();
    if (comunaSeleccionada) {
        var provinciaComuna = $(this).find('option:selected').data('provincia');
        if (provinciaComuna) {
            $('#edit_provincia').val(provinciaComuna);
        }
    }
});

function reasignarPlano(id) {
    // Verificar control de sesión antes de permitir reasignación
    $.get('{{ route("session-control.status") }}')
        .done(function(response) {
            if (!response.hasControl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Control Requerido',
                    html: `<p>Necesitas tener control de numeración para reasignar planos.</p>
                           <p>Estado actual: <strong>${response.whoHasControl || 'Nadie tiene control'}</strong></p>`,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            // Si tiene control, obtener datos del plano original
            $.get('{{ url("/planos") }}/' + id + '/detalles-completos')
                .done(function(planoData) {
                    const plano = planoData.plano;
                    // Usar numero_plano_completo que tiene el formato completo (0830329272SR)
                    const numeroActual = plano.numero_plano_completo || plano.numero_plano || '';
                    let codigoComuna = plano.codigo_comuna || '303';
                    let tipoPlano = plano.tipo_saneamiento || 'SU';

                    // Si no tenemos codigo_comuna directo, extraerlo del número
                    if (!plano.codigo_comuna && numeroActual.length >= 7) {
                        codigoComuna = numeroActual.substring(2, 5);
                        tipoPlano = numeroActual.slice(-2);
                    }

                    // Llenar datos del modal
                    $('#reasignar-modal #reasignar_id').val(id);
                    $('#numero_actual').val(numeroActual);
                    $('#cantidad_folios_reasignar').text(planoData.folios ? planoData.folios.length : 0);
                    $('#nuevo_numero').val('Generando...').removeClass('bg-success').addClass('bg-light');

                    // Generar automáticamente el próximo número
                    $.ajax({
                        url: '{{ route("session-control.generar-numero") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            codigo_comuna: codigoComuna,
                            tipo_plano: tipoPlano
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#nuevo_numero').val(response.numeroCompleto)
                                    .removeClass('bg-light')
                                    .addClass('bg-success');
                            } else {
                                $('#nuevo_numero').val('Error al generar');
                                Swal.fire('Error', response.message || 'No se pudo generar el número automático', 'error');
                            }
                        },
                        error: function(xhr) {
                            $('#nuevo_numero').val('Error al generar');
                            const errorMsg = xhr.responseJSON?.message || 'No se pudo generar el número automático';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });

                    $('#reasignar-modal').modal('show');
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudieron obtener los datos del plano', 'error');
                });
        })
        .fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo verificar el estado del control de sesión',
                confirmButtonColor: '#dc3545'
            });
        });
}

function initModals() {
    // Modal Editar - Submit con validación avanzada
    $('#form-edit-plano').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $btnGuardar = $('#btn-guardar-plano');
        const originalBtnText = $btnGuardar.html();

        // Limpiar errores previos
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Validar formulario localmente primero
        if (!validateEditForm()) {
            return false;
        }

        // Deshabilitar botón y mostrar loading
        $btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        const id = $('#edit_id').val();
        const formData = $form.serialize();

        $.ajax({
            url: "{{ url('/planos') }}/" + id,
            method: 'PUT',
            data: formData,
            success: function(response) {

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Plano actualizado!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#edit-modal').modal('hide');
                    planosTable.draw(false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr) {

                if (xhr.status === 422) {
                    // Errores de validación del servidor
                    const errors = xhr.responseJSON.errors;
                    displayValidationErrors(errors);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Errores de validación',
                        text: 'Por favor revisa los campos marcados',
                        confirmButtonColor: '#ffc107'
                    });
                } else {
                    const message = xhr.responseJSON?.message || 'No se pudo actualizar el plano';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            complete: function() {
                // Restaurar botón
                $btnGuardar.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Contador de caracteres para observaciones
    $('#edit_observaciones').on('input', updateObservacionesCount);

    // Validación en tiempo real
    $('#edit_responsable, #edit_proyecto, #edit_provincia, #edit_ano').on('blur', function() {
        validateField($(this));
    });

    $('#edit_comuna, #edit_tipo_saneamiento, #edit_mes').on('change', function() {
        validateField($(this));
    });

    // Modal Reasignar - Submit
    $('#form-reasignar-plano').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reasignar_id').val();
        const formData = $(this).serialize();
        const $btn = $('#btn-confirmar-reasignar');
        const originalBtnText = $btn.html();

        // Deshabilitar botón y mostrar loading
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

        $.ajax({
            url: "{{ url('/planos') }}/" + id + "/reasignar",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Cerrar modal de reasignación
                    $('#reasignar-modal').modal('hide');

                    // Recargar tabla
                    planosTable.draw(false);

                    // Mostrar notificación breve y abrir modal de edición automáticamente
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    // Abrir modal de edición del nuevo plano automáticamente
                    if (response.nuevo_plano_id) {
                        setTimeout(function() {
                            editarPlano(response.nuevo_plano_id);
                        }, 600); // Delay para cerrar modal de reasignar primero
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'No se pudo reasignar el número';
                Swal.fire('Error', message, 'error');
            },
            complete: function() {
                // Restaurar botón
                $btn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Limpiar formulario al cerrar modal editar plano
    $('#edit-modal').on('hidden.bs.modal', function() {
        // Limpiar campos del plano
        $('#edit_plano_id').val('');
        $('#edit-numero-plano').text('');
        $('#edit_comuna').val('');
        $('#edit_tipo_saneamiento').val('SR');
        $('#edit_provincia').val('');
        $('#edit_responsable').val('');
        $('#edit_mes').val('ENE');
        $('#edit_ano').val('');
        $('#edit_proyecto').val('');
        $('#edit_observaciones').val('');

        // Limpiar tabla de folios
        $('#folios-tbody').empty();
        $('#edit-total-folios').text('0');
        $('#resumen-folios').text('0');
        $('#resumen-hectareas').text('0');
        $('#resumen-m2').text('0');

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    // Modal Editar Folio - Submit
    $('#form-edit-folio').on('submit', function(e) {
        e.preventDefault();
    
        const $form = $(this);
        const $btnGuardar = $('#btn-guardar-folio');
        const originalBtnText = $btnGuardar.html();

        // Limpiar errores previos
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Deshabilitar botón y mostrar loading
        $btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        const folioId = $('#edit_folio_id').val();
        const formData = $form.serialize();

        $.ajax({
            url: "{{ url('/planos/folios') }}/" + folioId,
            method: 'PUT',
            data: formData,
            success: function(response) {

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Folio actualizado!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#edit-folio-modal').modal('hide');
                    planosTable.draw(false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr) {

                if (xhr.status === 422) {
                    // Errores de validación del servidor
                    const errors = xhr.responseJSON.errors;
                    displayFolioValidationErrors(errors);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Errores de validación',
                        text: 'Por favor revisa los campos marcados',
                        confirmButtonColor: '#ffc107'
                    });
                } else {
                    const message = xhr.responseJSON?.message || 'No se pudo actualizar el folio';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            complete: function() {
                // Restaurar botón
                $btnGuardar.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Limpiar formulario folio al cerrar modal
    $('#edit-folio-modal').on('hidden.bs.modal', function() {
        $('#form-edit-folio')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#edit-folio-loading-overlay').hide();
    });

    // TODO: Implementar handlers para edición de folios después de reasignar
}

// ===== FUNCIONES AUXILIARES =====

function updateObservacionesCount() {
    const $textarea = $('#edit_observaciones');
    const count = $textarea.val().length;
    $('#observaciones-count').text(count);

    if (count > 450) {
        $('#observaciones-count').addClass('text-warning');
    } else if (count >= 500) {
        $('#observaciones-count').addClass('text-danger');
    } else {
        $('#observaciones-count').removeClass('text-warning text-danger');
    }
}

function validateField($field) {
    const fieldName = $field.attr('name');
    const value = ($field.val() || '').trim();
    let isValid = true;
    let errorMessage = '';

    // Validaciones específicas
    if (fieldName === 'responsable' && (!value || value.length < 2)) {
        isValid = false;
        errorMessage = 'El responsable debe tener al menos 2 caracteres';
    } else if (fieldName === 'proyecto' && (!value || value.length < 3)) {
        isValid = false;
        errorMessage = 'El proyecto debe tener al menos 3 caracteres';
    } else if (fieldName === 'comuna' && !value) {
        isValid = false;
        errorMessage = 'Debe seleccionar una comuna';
    } else if (fieldName === 'tipo_saneamiento' && !value) {
        isValid = false;
        errorMessage = 'Debe seleccionar el tipo de saneamiento';
    } else if (fieldName === 'provincia' && (!value || value.length < 2)) {
        isValid = false;
        errorMessage = 'La provincia debe tener al menos 2 caracteres';
    } else if (fieldName === 'mes' && !value) {
        isValid = false;
        errorMessage = 'Debe seleccionar un mes';
    } else if (fieldName === 'ano' && (!value || value < 2020 || value > 2030)) {
        isValid = false;
        errorMessage = 'El año debe estar entre 2020 y 2030';
    }

    // Aplicar estilos de validación
    if (isValid) {
        $field.removeClass('is-invalid').addClass('is-valid');
        $field.siblings('.invalid-feedback').text('');
    } else {
        $field.removeClass('is-valid').addClass('is-invalid');
        $field.siblings('.invalid-feedback').text(errorMessage);
    }

    return isValid;
}

function validateEditForm() {
    let isValid = true;

    // Validar campos requeridos
    const requiredFields = [
        '#edit_comuna', '#edit_responsable', '#edit_proyecto',
        '#edit_tipo_saneamiento', '#edit_provincia', '#edit_mes',
        '#edit_ano'
    ];

    requiredFields.forEach(function(selector) {
        const $field = $(selector);
        if (!validateField($field)) {
            isValid = false;
        }
    });

    return isValid;
}

function displayValidationErrors(errors) {

    Object.keys(errors).forEach(function(fieldName) {
        const $field = $(`#edit_${fieldName}`);
        if ($field.length) {
            $field.addClass('is-invalid');
            $field.siblings('.invalid-feedback').text(errors[fieldName][0]);
        }
    });
}

function initEditModalSelect2() {
    // Obtener valor actual antes de reinicializar
    const currentValue = $('#edit_comuna').val();

    // Destruir Select2 anterior si existe
    if ($('#edit_comuna').hasClass('select2-hidden-accessible')) {
        $('#edit_comuna').select2('destroy');
    }

    // Inicializar Select2 para comuna en modal de edición
    $('#edit_comuna').select2({
        dropdownParent: $('#edit-modal'),
        placeholder: 'Seleccionar comuna...',
        allowClear: true,
        width: '100%',
        theme: 'bootstrap4'
    });

    // Restaurar valor si existía
    if (currentValue) {
        $('#edit_comuna').val(currentValue).trigger('change');
    }
}

function editarFolio(folioId) {

    // Mostrar overlay de carga
    $('#edit-folio-loading-overlay').removeClass('d-none').show();

    // Limpiar formulario y errores previos
    $('#form-edit-folio')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    $.get("{{ url('/planos/folios') }}/" + folioId + "/edit")
        .done(function(response) {

            // Poblar modal de edición de folio
            $('#edit_folio_id').val(folioId);
            $('#edit_folio_numero').val(response.folio.folio || '');
            $('#edit_folio_solicitante').val(response.folio.solicitante);
            $('#edit_folio_apellido_paterno').val(response.folio.apellido_paterno || '');
            $('#edit_folio_apellido_materno').val(response.folio.apellido_materno || '');
            $('#edit_folio_tipo_inmueble').val(response.folio.tipo_inmueble);
            $('#edit_folio_numero_inmueble').val(response.folio.numero_inmueble || '');
            $('#edit_folio_hectareas').val(response.folio.hectareas || '');
            $('#edit_folio_m2').val(response.folio.m2);
            $('#edit_folio_matrix_folio').val(response.folio.matrix_folio || '');
            $('#edit_folio_is_from_matrix').val(response.folio.is_from_matrix ? '1' : '0');

            // Ocultar loading y mostrar modal
            $('#edit-folio-loading-overlay').hide();
            $('#edit-folio-modal').modal('show');
        })
        .fail(function(xhr) {
            $('#edit-folio-loading-overlay').hide();
            const message = xhr.responseJSON?.message || 'No se pudo cargar la información del folio';
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar folio',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        });
}

function displayFolioValidationErrors(errors) {

    Object.keys(errors).forEach(function(fieldName) {
        const $field = $(`#edit_folio_${fieldName}`);
        if ($field.length) {
            $field.addClass('is-invalid');
            $field.siblings('.invalid-feedback').text(errors[fieldName][0]);
        }
    });
}

@endif

// ===== GESTIONAR FOLIOS (AGREGAR/QUITAR) =====

@if(Auth::user()->isRegistro())
// ===== FUNCIONALIDAD GESTIONAR FOLIOS ELIMINADA =====
// Ahora se usa el botón EDITAR para agregar/quitar folios

function abrirModalGestionFolios_OBSOLETO(planoId) {
    // Resetear modal
    $('#gestion-numero-plano').text('');
    $('#quitar-folios-lista').html('');
    $('#count-seleccionados').text('0');
    $('#btn-confirmar-quitar').prop('disabled', true);

    // Resetear tab a "Quitar"
    $('#quitar-tab').tab('show');

    // Mostrar loading
    $('#quitar-loading').show();

    // Abrir modal
    $('#modal-gestionar-folios').modal('show');

    // Cargar datos
    $.get(`{{ url('/planos') }}/${planoId}/folios-gestion`)
        .done(function(response) {
            if (response.success) {
                $('#gestion-numero-plano').text(response.plano.numero_plano_completo);
                $('#total-folios-plano').text(response.plano.cantidad_folios);

                // Guardar ID del plano
                $('#modal-gestionar-folios').data('plano-id', planoId);
                $('#agregar_plano_id').val(planoId);

                // NUEVO: Guardar datos del tipo de plano
                tipoPlanoGlobal = response.plano.tipo_saneamiento;
                esRuralGlobal = response.plano.es_rural;
                tipoInmuebleGlobal = response.plano.tipo_inmueble;

                $('#agregar_tipo_plano').val(tipoPlanoGlobal);
                $('#agregar_es_rural').val(esRuralGlobal ? '1' : '0');

                // NUEVO: Actualizar textos informativos
                $('#info-tipo-plano').text(tipoPlanoGlobal);
                $('#info-tipo-inmueble').text(tipoInmuebleGlobal + 'S');
                $('#label-tipo-cantidad').text(tipoInmuebleGlobal.toLowerCase() + 's');

                // Renderizar lista de folios
                renderizarListaFolios(response.folios, response.plano.cantidad_folios);
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los folios', 'error');
            $('#modal-gestionar-folios').modal('hide');
        })
        .always(function() {
            $('#quitar-loading').hide();
        });
}

function renderizarListaFolios(folios, totalFolios) {
    if (folios.length === 0) {
        $('#quitar-folios-lista').html(`
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No hay folios para mostrar
            </div>
        `);
        return;
    }

    let html = '<div class="list-group">';

    folios.forEach(function(folio) {
        const hectareasDisplay = folio.hectareas ? `${folio.hectareas} ha` : '-';
        const m2Display = folio.m2 ? folio.m2.toLocaleString() : '0';

        html += `
            <label class="list-group-item d-flex align-items-center folio-checkbox-item">
                <input type="checkbox"
                       class="folio-checkbox mr-3"
                       data-folio-id="${folio.id}"
                       ${totalFolios === 1 ? 'disabled' : ''}>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong>Folio: ${folio.folio}</strong>
                        <span class="badge badge-info">${folio.tipo_inmueble} ${folio.numero_inmueble || ''}</span>
                    </div>
                    <div class="text-muted small">
                        ${folio.nombre_completo}
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-ruler-combined"></i> ${hectareasDisplay} |
                        <i class="fas fa-square"></i> ${m2Display} m²
                    </div>
                </div>
            </label>
        `;
    });

    html += '</div>';

    if (totalFolios === 1) {
        html += `
            <div class="alert alert-warning mt-2 mb-0">
                <i class="fas fa-info-circle"></i>
                Este plano solo tiene 1 folio, no puede ser eliminado.
            </div>
        `;
    }

    $('#quitar-folios-lista').html(html);

    // Event listener para checkboxes
    $('.folio-checkbox').on('change', function() {
        actualizarContadorSeleccionados();
    });
}

function actualizarContadorSeleccionados() {
    const seleccionados = $('.folio-checkbox:checked').length;
    const total = $('#total-folios-plano').text();

    $('#count-seleccionados').text(seleccionados);

    // Validar que quede al menos 1 folio
    const quedaranFolios = parseInt(total) - seleccionados;

    if (seleccionados > 0 && quedaranFolios >= 1) {
        $('#btn-confirmar-quitar').prop('disabled', false);
    } else {
        $('#btn-confirmar-quitar').prop('disabled', true);
    }
}

// Confirmar eliminar folios
$('#btn-confirmar-quitar').on('click', function() {
    const planoId = $('#modal-gestionar-folios').data('plano-id');
    const seleccionados = $('.folio-checkbox:checked');
    const foliosIds = [];

    seleccionados.each(function() {
        foliosIds.push($(this).data('folio-id'));
    });

    if (foliosIds.length === 0) {
        Swal.fire('Atención', 'No has seleccionado folios para eliminar', 'warning');
        return;
    }

    Swal.fire({
        title: '¿Confirmar eliminación?',
        html: `Se eliminarán <strong>${foliosIds.length}</strong> folio(s) del plano.<br><br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarFolios(planoId, foliosIds);
        }
    });
});

function eliminarFolios(planoId, foliosIds) {
    const $btnEliminar = $('#btn-confirmar-quitar');
    const originalText = $btnEliminar.html();

    $btnEliminar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');

    $.ajax({
        url: `{{ url('/planos') }}/${planoId}/quitar-folios`,
        method: 'POST',
        data: {
            folios_ids: foliosIds,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Folios eliminados!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#modal-gestionar-folios').modal('hide');
                planosTable.draw(false);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Error al eliminar folios';
            Swal.fire('Error', message, 'error');
        },
        complete: function() {
            $btnEliminar.prop('disabled', false).html(originalText);
        }
    });
}

// Limpiar modal al cerrar
$('#modal-gestionar-folios').on('hidden.bs.modal', function() {
    $('#quitar-folios-lista').html('');
    $('#form-agregar-folio')[0].reset();
    $('.folio-checkbox').prop('checked', false);
    $('#contenedor-inmuebles').html('');
    $('#btn-submit-agregar').prop('disabled', true);
    $('#cantidad-inmuebles').val('');
    $('#agregar_is_from_matrix').val('0');
    $('#agregar_matrix_folio').val('');
});

// ========================================
// TAB 2: AGREGAR FOLIO - Nueva lógica múltiples inmuebles
// ========================================

let tipoPlanoGlobal = '';
let esRuralGlobal = false;
let tipoInmuebleGlobal = '';

// ========================================
// TAB 2: AGREGAR FOLIO - Múltiples hijuelas/sitios
// ========================================

// BLOQUE 3: Listener select cantidad
$('#cantidad-inmuebles').on('change', function() {
    const cantidad = parseInt($(this).val());
    if (cantidad) {
        generarFormulariosInmuebles(cantidad);
        $('#btn-submit-agregar').prop('disabled', false);
    } else {
        $('#contenedor-inmuebles').html('');
        $('#btn-submit-agregar').prop('disabled', true);
    }
});

// BLOQUE 4: Generar formularios dinámicos
function generarFormulariosInmuebles(cantidad) {
    let html = '';
    const esRural = esRuralGlobal;
    const labelTipo = tipoInmuebleGlobal;

    for (let i = 1; i <= cantidad; i++) {
        html += `
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <strong><i class="fas fa-${esRural ? 'tree' : 'city'}"></i> ${labelTipo} #${i}</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-${esRural ? '3' : '6'}">
                        <div class="form-group">
                            <label>Número ${labelTipo} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control numero-inmueble"
                                   data-index="${i-1}" value="${i}" required min="1">
                        </div>
                    </div>`;

        if (esRural) {
            html += `
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hectáreas</label>
                            <input type="text" class="form-control hectareas-input"
                                   data-index="${i-1}" placeholder="0,0000">
                            <small class="text-muted">Formato: 2,5000</small>
                        </div>
                    </div>`;
        }

        html += `
                    <div class="col-md-${esRural ? '6' : '6'}">
                        <div class="form-group">
                            <label>Metros Cuadrados (m²) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control m2-input"
                                   data-index="${i-1}" placeholder="0,00" required>
                            <small class="text-muted">Formato: 25.000,00</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    $('#contenedor-inmuebles').html(html);

    // Agregar listeners conversión ha ↔ m²
    if (esRural) {
        attachConversionListeners();
    }
}

// BLOQUE 5: Conversión hectáreas ↔ m²
function attachConversionListeners() {
    // Hectáreas -> M²
    $(document).on('input', '.hectareas-input', function() {
        const index = $(this).data('index');
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');

        if (valor && !isNaN(valor)) {
            const ha = parseFloat(valor);
            const m2 = ha * 10000;
            $(`.m2-input[data-index="${index}"]`).val(formatNumber(m2, 2));
        }
    });

    // M² -> Hectáreas
    $(document).on('input', '.m2-input', function() {
        const index = $(this).data('index');
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');

        if (valor && !isNaN(valor) && esRuralGlobal) {
            const m2 = parseFloat(valor);
            const ha = m2 / 10000;
            $(`.hectareas-input[data-index="${index}"]`).val(formatNumber(ha, 4));
        }
    });
}

function formatNumber(num, decimals) {
    return num.toFixed(decimals).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Búsqueda en Matrix (opcional)
$('#btn-buscar-matrix').on('click', function() {
    const folio = $('#buscar-matrix-folio').val().trim();

    if (!folio) {
        Swal.fire('Atención', 'Ingresa un número de folio para buscar', 'warning');
        return;
    }

    const $btn = $(this);
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
        url: '{{ route("api.matrix.buscar") }}',
        method: 'GET',
        data: { folio: folio },
        success: function(response) {
            if (response.encontrado) {
                // Autocompletar datos del formulario
                $('#agregar_folio').val(response.data.folio || '');
                $('#agregar_solicitante').val(response.data.nombres || '');
                $('#agregar_apellido_paterno').val(response.data.apellido_paterno || '');
                $('#agregar_apellido_materno').val(response.data.apellido_materno || '');

                // Marcar como proveniente de Matrix
                $('#agregar_is_from_matrix').val('1');
                $('#agregar_matrix_folio').val(folio);

                Swal.fire({
                    icon: 'success',
                    title: '¡Folio encontrado!',
                    text: 'Datos autocompletados desde Matrix',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Folio no encontrado',
                    text: 'Puedes ingresar los datos manualmente',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al buscar en Matrix', 'error');
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalText);
        }
    });
});

// BLOQUE 6: Submit formulario
$('#form-agregar-folio').on('submit', function(e) {
    e.preventDefault();

    const planoId = $('#modal-gestionar-folios').data('plano-id');

    if (!planoId) {
        Swal.fire('Error', 'No se pudo identificar el plano', 'error');
        return;
    }

    // Validar que haya seleccionado cantidad
    if ($('.numero-inmueble').length === 0) {
        Swal.fire('Atención', 'Debes seleccionar cuántas ' + tipoInmuebleGlobal.toLowerCase() + 's tiene este folio', 'warning');
        return;
    }

    // Recolectar datos base
    const formData = {
        folio: $('#agregar_folio').val().trim(),
        solicitante: $('#agregar_solicitante').val().trim(),
        apellido_paterno: $('#agregar_apellido_paterno').val().trim(),
        apellido_materno: $('#agregar_apellido_materno').val().trim(),
        is_from_matrix: $('#agregar_is_from_matrix').val() === '1' ? 1 : 0,
        matrix_folio: $('#agregar_matrix_folio').val() || null,
        inmuebles: []
    };

    // Validar solicitante
    if (!formData.solicitante) {
        Swal.fire('Atención', 'El campo Solicitante es obligatorio', 'warning');
        return;
    }

    // Recolectar inmuebles dinámicamente
    let errores = [];
    $('.numero-inmueble').each(function() {
        const index = $(this).data('index');
        const numero = parseInt($(this).val());
        const m2Input = $(`.m2-input[data-index="${index}"]`).val();

        if (!numero || numero < 1) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: Número inválido`);
            return;
        }

        if (!m2Input) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: M² es obligatorio`);
            return;
        }

        const m2 = parseFloat(m2Input.replace(/\./g, '').replace(',', '.'));

        if (isNaN(m2) || m2 <= 0) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: M² inválido`);
            return;
        }

        const inmueble = {
            numero_inmueble: numero,
            m2: m2
        };

        // Agregar hectáreas solo si es RURAL
        if (esRuralGlobal) {
            const haInput = $(`.hectareas-input[data-index="${index}"]`).val();
            if (haInput) {
                inmueble.hectareas = parseFloat(haInput.replace(/\./g, '').replace(',', '.'));
            }
        }

        formData.inmuebles.push(inmueble);
    });

    // Mostrar errores si los hay
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores.join('<br>')
        });
        return;
    }

    // Deshabilitar botón
    const $submitBtn = $('#btn-submit-agregar');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Agregando...');

    // Enviar AJAX
    $.ajax({
        url: `{{ url('/planos') }}/${planoId}/agregar-folio`,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Folios agregados!',
                    html: response.message,
                    timer: 2500,
                    showConfirmButton: false
                });

                // Cerrar modal y recargar tabla
                $('#modal-gestionar-folios').modal('hide');
                planosTable.draw(false);

                // Limpiar formulario
                $('#form-agregar-folio')[0].reset();
                $('#contenedor-inmuebles').html('');
                $('#cantidad-inmuebles').val('');
                $('#btn-submit-agregar').prop('disabled', true);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            let mensaje = 'Error al agregar folios';

            if (xhr.responseJSON?.message) {
                mensaje = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors) {
                const errores = Object.values(xhr.responseJSON.errors).flat();
                mensaje = errores.join('<br>');
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: mensaje
            });
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
});

@endif

// Los botones de exportar ahora están integrados nativamente en DataTables
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
#planos-table td:nth-child(1) { max-width: 100px; } /* Acciones */
#planos-table td:nth-child(2) { max-width: 130px; } /* N° Plano Completo */

/* Dropdown de acciones */
#planos-table .dropdown-menu {
    min-width: 160px;
    font-size: 0.875rem;
    z-index: 1050;
}

#planos-table .dropdown {
    position: static;
}

#planos-table tbody tr {
    position: relative;
}

#planos-table .dropdown-menu.show {
    position: absolute;
    z-index: 1050 !important;
}

#planos-table .dropdown-item {
    padding: 0.5rem 1rem;
}

#planos-table .dropdown-item i {
    width: 16px;
}
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

/* Estilos para Select2 en filtros avanzados */
.select2-container--bootstrap4 .select2-selection {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem;
    min-height: calc(1.5em + 0.75rem + 2px);
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px) !important;
}

.select2-container--bootstrap4 .select2-selection__rendered {
    line-height: calc(1.5em + 0.75rem) !important;
    padding-left: 0.75rem !important;
}

.select2-container--bootstrap4 .select2-selection__arrow {
    height: calc(1.5em + 0.75rem) !important;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Alineación vertical de filtros */
#filtros-form .row {
    align-items: flex-end;
    margin-left: -8px;
    margin-right: -8px;
}

#filtros-form .row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}

#filtros-form .form-group {
    margin-bottom: 1rem;
}

#filtros-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 400;
    min-height: 1.5em;
}

/* Asegurar que select2 y select normales tengan exactamente la misma altura */
#filtros-form .form-control,
#filtros-form .select2-container {
    height: calc(1.5em + 0.75rem + 2px) !important;
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

/* Estilos para expansión híbrida */
.child-row.bg-info {
    border-left: 4px solid #17a2b8 !important;
}

.child-row.bg-info td {
    padding: 15px !important;
    vertical-align: top;
}

.child-row.bg-info .col-md-4 {
    padding: 10px;
    border-right: 1px solid rgba(255,255,255,0.2);
}

.child-row.bg-info .col-md-4:last-child {
    border-right: none;
}

.child-row.bg-info strong {
    font-size: 0.9em;
    margin-bottom: 5px;
    display: block;
}

.child-row.bg-info .text-light {
    font-size: 0.85em;
    line-height: 1.4;
}

/* ===== ESTILOS MODAL EDICIÓN MEJORADO ===== */

/* Loading overlay para modal */
#edit-loading-overlay {
    border-radius: 0.375rem;
}

#edit-loading-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Modal de edición más amplio */
#edit-modal .modal-dialog {
    max-width: 900px;
}

/* Campos de formulario con mejor UX */
#edit-modal .form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

#edit-modal .form-control {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

#edit-modal .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Estados de validación mejorados */
#edit-modal .form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.75-.75L5.2 3.83 4.45 3.08 3.05 4.48 2.3 3.73z'/%3e%3c/svg%3e");
}

#edit-modal .form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m6 3v4m0 0h.01'/%3e%3c/svg%3e");
}

/* Invalid feedback más visible */
#edit-modal .invalid-feedback {
    display: block;
    font-size: 0.875rem;
    color: #dc3545;
    margin-top: 0.25rem;
}

/* Card de archivos con mejor apariencia */
#edit-modal .card {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

#edit-modal .card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 0.75rem 1rem;
}

#edit-modal .card-header h6 {
    color: #5a5c69;
    font-weight: 600;
}

#edit-modal .card-body {
    padding: 1rem;
}

/* Tooltips informativos */
.fa-info-circle {
    cursor: help;
    margin-left: 4px;
}

/* Contador de caracteres */
#observaciones-count {
    font-weight: 500;
}

#observaciones-count.text-warning {
    color: #ffc107 !important;
}

#observaciones-count.text-danger {
    color: #dc3545 !important;
}

/* Small text mejorado */
#edit-modal .form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* Footer con mejor layout */
#edit-modal .modal-footer .row {
    width: 100%;
    margin: 0;
}

#edit-modal .modal-footer .col-6 {
    padding: 0;
}

/* Botones con mejor UX */
#edit-modal .btn {
    font-weight: 500;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    transition: all 0.15s ease-in-out;
}

#edit-modal .btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

#edit-modal .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

#edit-modal .btn-primary:hover:not(:disabled) {
    background-color: #0056b3;
    border-color: #004085;
    transform: translateY(-1px);
}

#edit-modal .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

#edit-modal .btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
}

/* Select2 en modal */
#edit-modal .select2-container .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px);
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

#edit-modal .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: calc(1.5em + 0.75rem);
    padding-left: 0.75rem;
    color: #495057;
}

#edit-modal .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.75rem);
}

/* Animaciones suaves */
#edit-modal .modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== CONTENEDOR DATATABLE CON SCROLL HORIZONTAL ===== */

/* Wrapper con altura dinámica hasta el fondo */
.datatable-container {
    height: calc(100vh - 320px); /* Altura dinámica: 100% viewport - espacio para header/navbar/controles */
    overflow: auto; /* Scroll vertical y horizontal */
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background: white;
    position: relative;
}

/* Tabla con ancho mínimo para forzar scroll horizontal */
.datatable-container .table {
    margin-bottom: 0;
    min-width: 1800px; /* Ancho mínimo para garantizar scroll horizontal */
    white-space: nowrap; /* Evita que el contenido se envuelva */
}

/* Mejorar barras de scroll */
.datatable-container::-webkit-scrollbar {
    width: 12px;  /* Barra vertical un poco más ancha */
    height: 16px; /* Barra horizontal más amplia para mejor usabilidad */
}

.datatable-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.datatable-container::-webkit-scrollbar-thumb {
    background: #6c757d;
    border-radius: 4px;
    border: 1px solid #f1f1f1;
}

.datatable-container::-webkit-scrollbar-thumb:hover {
    background: #495057;
}

/* Header fijo para mejor navegación */
.datatable-container .table thead th {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
    border-bottom: 2px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Asegurar que el contenido no se superponga */
.datatable-container .table tbody tr td {
    vertical-align: middle;
}

/* ===== BARRA COMPACTA DE CONTROLES DATATABLE ===== */

/* Barra de controles ultracompacta */
.datatable-controls-bar-compact {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem 0.375rem 0 0;
    padding: 8px 12px; /* Padding reducido drásticamente */
    position: sticky;
    top: 0;
    z-index: 20;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    min-height: 40px; /* Altura mínima muy reducida */
}

/* Contenedores de controles reubicados y compactos */
#datatable-search-container .dataTables_filter {
    margin-bottom: 0 !important;
    text-align: left !important;
}

#datatable-search-container .dataTables_filter label {
    margin-bottom: 0 !important;
    font-size: 0.9rem;
}

#datatable-search-container .dataTables_filter input {
    padding: 4px 8px !important;
    font-size: 0.9rem;
    height: 32px;
}

#datatable-length-container .dataTables_length {
    margin-bottom: 0 !important;
    text-align: right !important;
}

#datatable-length-container .dataTables_length label {
    margin-bottom: 0 !important;
    font-size: 0.9rem;
}

#datatable-length-container .dataTables_length select {
    padding: 4px 8px !important;
    font-size: 0.9rem;
    height: 32px;
}

/* Ocultar controles originales de DataTable */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length {
    display: none !important;
}

/* Ajustar el contenedor de la tabla */
.datatable-container {
    border-radius: 0 0 0.375rem 0.375rem;
    border-top: none;
}

/* ===== ESTILOS CONTROL DE SESIONES ===== */

/* Widget de control de sesiones */
.session-control-card {
    border-left: 4px solid #28a745 !important;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
}

.no-control {
    border-left: 4px solid #dc3545 !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
}

#session-control-widget .info-box {
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

#session-control-widget .info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Badges más prominentes */
#session-badge {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
}

/* Botones de control */
#session-control-widget .btn-group .btn {
    margin-right: 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #edit-modal .modal-dialog {
        max-width: 95%;
        margin: 0.5rem auto;
    }

    #edit-modal .modal-footer .col-6 {
        text-align: center !important;
        margin-bottom: 0.5rem;
    }

    #session-control-widget .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    #session-control-widget .btn-group .btn {
        margin-right: 0;
        width: 100%;
    }
}

/* ===== BOTONES ACCIONES COMPACTOS ===== */
#planos-table .btn-group-sm .btn {
    padding: 2px 6px;
    font-size: 12px;
}

#planos-table .btn-group {
    white-space: nowrap;
}
</style>
@endpush