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

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo Inmueble <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agregar_tipo_inmueble" name="tipo_inmueble" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="HIJUELA">HIJUELA</option>
                                            <option value="SITIO">SITIO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Número Inmueble</label>
                                        <input type="number" class="form-control" id="agregar_numero_inmueble" name="numero_inmueble">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Hectáreas (solo HIJUELA)</label>
                                        <input type="number" step="0.0001" class="form-control" id="agregar_hectareas" name="hectareas">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>M² <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="agregar_m2" name="m2" required>
                                    </div>
                                </div>
                            </div>

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