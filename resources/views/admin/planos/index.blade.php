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
                        <th>Observaciones</th>
                        <th>Archivo</th>
                        <th>Tubo</th>
                        <th>Tela</th>
                        <th>Archivo Digital</th>
                        <th>Fecha Creación</th>
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

function initPlanosTable() {
    const columnDefs = [
        @if(Auth::user()->isRegistro())
        { "orderable": false, "targets": [0] }, // Acciones
        { "orderable": false, "targets": [-1, -2] }, // Expandir y Detalles
        { "className": "no-export", "targets": [0, -1, -2] }, // No exportar acciones, expandir y detalles
        @else
        { "orderable": false, "targets": [-1, -2] }, // Expandir y Detalles
        { "className": "no-export", "targets": [-1, -2] }, // No exportar expandir y detalles
        @endif
        { "className": "text-center", "targets": [0, -1, -2] },
        { "className": "nowrap", "targets": "_all" }, // No wrap para mejor visualización
        { "visible": false, "targets": [12, 13, 14, 15, 16] } // Ocultar por defecto las nuevas columnas
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
        { "data": "observaciones_display", "name": "observaciones_display" },
        { "data": "archivo_display", "name": "archivo_display" },
        { "data": "tubo_display", "name": "tubo_display" },
        { "data": "tela_display", "name": "tela_display" },
        { "data": "archivo_digital_display", "name": "archivo_digital_display" },
        { "data": "created_at_display", "name": "created_at_display" },
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
        dom: '<"row"<"col-sm-12 col-md-8"f><"col-sm-12 col-md-4 text-right"l>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"d-none"B>',
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
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
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
            top: 20% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 999999 !important;
            background: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 6px !important;
            padding: 20px !important;
            width: 320px !important;
            max-height: 400px !important;
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
            const btn = $('button[data-id="' + id + '"].toggle-expand');
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
    const btn = $('button[data-id="' + id + '"].toggle-expand');
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

    // EXPANSIÓN SIMPLIFICADA: Solo múltiples folios son clickeables
    $('#planos-table tbody tr').each(function() {
        const $row = $(this);
        const $expandBtn = $row.find('.expandir-folios');

        if ($expandBtn.length) {
            const foliosCount = parseInt($expandBtn.data('folios')) || 0;

            if (foliosCount > 1) {
                // Solo hacer expandible si tiene múltiples folios
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
                // Remover clase expandible si tiene 1 folio
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

    // Mostrar overlay de carga
    $('#edit-loading-overlay').removeClass('d-none').show();

    // Limpiar formulario y errores previos
    $('#form-edit-plano')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    $.get("{{ url('/planos') }}/" + id + "/edit")
        .done(function(response) {
            // Poblar modal de edición - TODOS LOS CAMPOS
            $('#edit-modal #edit_id').val(id);
            // Buscar comuna sin importar mayúsculas/minúsculas
            const comunaPlano = response.plano.comuna.toLowerCase();
            const comunaOption = $('#edit_comuna option').filter(function() {
                return $(this).val().toLowerCase() === comunaPlano;
            });

            if (comunaOption.length > 0) {
                $('#edit-modal #edit_comuna').val(comunaOption.val());
            }
            $('#edit-modal #edit_responsable').val(response.plano.responsable);
            $('#edit-modal #edit_proyecto').val(response.plano.proyecto);
            $('#edit-modal #edit_tipo_saneamiento').val(response.plano.tipo_saneamiento);
            $('#edit-modal #edit_provincia').val(response.plano.provincia);
            $('#edit-modal #edit_mes').val(response.plano.mes);
            $('#edit-modal #edit_ano').val(response.plano.ano);
            $('#edit-modal #edit_total_hectareas').val(response.plano.total_hectareas || '');
            $('#edit-modal #edit_total_m2').val(response.plano.total_m2);
            $('#edit-modal #edit_observaciones').val(response.plano.observaciones || '');
            $('#edit-modal #edit_archivo').val(response.plano.archivo || '');
            $('#edit-modal #edit_tubo').val(response.plano.tubo || '');
            $('#edit-modal #edit_tela').val(response.plano.tela || '');
            $('#edit-modal #edit_archivo_digital').val(response.plano.archivo_digital || '');

            // Actualizar contador de caracteres
            updateObservacionesCount();

            // Ocultar loading y mostrar modal
            $('#edit-loading-overlay').hide();
            $('#edit-modal').modal('show');

            // Ya no necesitamos Select2
        })
        .fail(function(xhr) {
            $('#edit-loading-overlay').hide();
            const message = xhr.responseJSON?.message || 'No se pudo cargar la información del plano';
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        });
}

function reasignarPlano(id) {
    $('#reasignar-modal #reasignar_id').val(id);
    $('#reasignar-modal').modal('show');
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
    $('#edit_responsable, #edit_proyecto, #edit_provincia, #edit_total_m2, #edit_ano').on('blur', function() {
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

    // Limpiar formulario al cerrar modal
    $('#edit-modal').on('hidden.bs.modal', function() {
        $('#form-edit-plano')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#edit-loading-overlay').hide();
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
}

// ===== FUNCIONES AUXILIARES UX EDICIÓN =====

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
    } else if (fieldName === 'total_m2' && (!value || value < 1)) {
        isValid = false;
        errorMessage = 'El total de m² debe ser mayor a 0';
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
        '#edit_ano', '#edit_total_m2'
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
}
</style>
@endpush