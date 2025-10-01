<!-- Modal Gestionar Folios (Agregar/Quitar con Tabs) -->
<div class="modal fade" id="modal-gestionar-folios" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">
                    <i class="fas fa-list"></i>
                    Gestionar Folios
                    <span id="gestion-numero-plano" class="badge badge-light ml-2"></span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="gestion-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="quitar-tab" data-toggle="tab" href="#quitar-folios-content" role="tab">
                            <i class="fas fa-minus text-danger"></i> Quitar Folios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agregar-tab" data-toggle="tab" href="#agregar-folio-content" role="tab">
                            <i class="fas fa-plus text-success"></i> Agregar Folio
                        </a>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content mt-3" id="gestion-tabs-content">

                    <!-- TAB 1: QUITAR FOLIOS -->
                    <div class="tab-pane fade show active" id="quitar-folios-content" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Selecciona los folios que deseas eliminar. Debe quedar al menos <strong>1 folio</strong> en el plano.
                        </div>

                        <div id="quitar-loading" class="text-center py-4" style="display: none;">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Cargando folios...</p>
                        </div>

                        <div id="quitar-folios-lista">
                            <!-- Aquí se cargará la lista de folios dinámicamente -->
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-danger" id="btn-confirmar-quitar" disabled>
                                <i class="fas fa-trash"></i> Eliminar Seleccionados (<span id="count-seleccionados">0</span>)
                            </button>
                            <small class="text-muted ml-2">
                                Total folios: <strong id="total-folios-plano">0</strong>
                            </small>
                        </div>
                    </div>

                    <!-- TAB 2: AGREGAR FOLIO -->
                    <div class="tab-pane fade" id="agregar-folio-content" role="tabpanel">
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle"></i>
                            Ingresa los datos del nuevo folio manualmente o búscalo en la base de datos Matrix.
                        </div>

                        <form id="form-agregar-folio">
                            <input type="hidden" id="agregar_plano_id" name="plano_id">

                            <!-- Búsqueda opcional en Matrix -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-search"></i> Buscar en Matrix (Opcional)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buscar-matrix-folio" placeholder="Ingresa número de folio">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="btn-buscar-matrix">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Si el folio existe en Matrix, se autocompletarán los datos</small>
                                </div>
                            </div>

                            <!-- Formulario manual -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Folio</label>
                                        <input type="text" class="form-control" id="agregar_folio" name="folio" placeholder="Número de folio">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Solicitante <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="agregar_solicitante" name="solicitante" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Apellido Paterno</label>
                                        <input type="text" class="form-control" id="agregar_apellido_paterno" name="apellido_paterno">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Apellido Materno</label>
                                        <input type="text" class="form-control" id="agregar_apellido_materno" name="apellido_materno">
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo Inmueble (Hijuela/Sitio) -->
                            <div class="form-group">
                                <label>Tipo Inmueble <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_inmueble" id="agregar_hijuela" value="HIJUELA" required>
                                    <label class="form-check-label" for="agregar_hijuela">
                                        <i class="fas fa-tree text-success"></i> HIJUELA (Rural - con hectáreas)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_inmueble" id="agregar_sitio" value="SITIO">
                                    <label class="form-check-label" for="agregar_sitio">
                                        <i class="fas fa-city text-primary"></i> SITIO (Urbano - solo m²)
                                    </label>
                                </div>
                            </div>

                            <!-- Número Inmueble -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><span id="label_numero_inmueble">Número</span> <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="agregar_numero_inmueble" name="numero_inmueble" required min="1">
                                        <small class="text-muted">Número de la hijuela o sitio</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Hectáreas (solo HIJUELA) -->
                            <div class="row" id="div_hectareas" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <small>Ingresa <strong>hectáreas</strong> o <strong>m²</strong>. El otro campo se calculará automáticamente (1 ha = 10.000 m²).</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hectáreas</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="agregar_hectareas" name="hectareas" placeholder="0,0000">
                                            <div class="input-group-append">
                                                <span class="input-group-text">ha</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">Formato: 7,2200 (máx 4 decimales)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>M² (calculado)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="agregar_m2_desde_ha" placeholder="0" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- M² (para ambos tipos) -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Metros Cuadrados (m²) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="agregar_m2" name="m2" required placeholder="0,00">
                                            <div class="input-group-append">
                                                <span class="input-group-text">m²</span>
                                            </div>
                                        </div>
                                        <small class="text-muted" id="hint_m2">Formato: 72200,00 o 72.200,00 (se guardará con 2 decimales)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos ocultos para Matrix -->
                            <input type="hidden" id="agregar_is_from_matrix" name="is_from_matrix" value="0">
                            <input type="hidden" id="agregar_matrix_folio" name="matrix_folio">

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Agregar Folio
                            </button>
                        </form>
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