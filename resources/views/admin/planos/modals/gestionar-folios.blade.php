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
                        <!-- Alert info dinámico -->
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle"></i>
                            Plano tipo: <strong id="info-tipo-plano">-</strong> |
                            Este plano solo acepta <strong id="info-tipo-inmueble">-</strong>
                        </div>

                        <form id="form-agregar-folio">
                            <input type="hidden" id="agregar_plano_id" name="plano_id">
                            <input type="hidden" id="agregar_tipo_plano" value="">
                            <input type="hidden" id="agregar_es_rural" value="">

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

                            <!-- Datos base del folio -->
                            <h6 class="text-primary mb-2"><i class="fas fa-user"></i> Datos del Folio</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Folio</label>
                                        <input type="text" class="form-control" id="agregar_folio" name="folio" placeholder="Número de folio (opcional)">
                                    </div>
                                </div>
                                <div class="col-md-8">
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

                            <hr>

                            <!-- Selector de cantidad -->
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-list-ol"></i>
                                ¿Cuántas <span id="label-tipo-cantidad">hijuelas/sitios</span> tiene este folio?
                            </h6>
                            <div class="form-group">
                                <select class="form-control" id="cantidad-inmuebles" required>
                                    <option value="">Selecciona cantidad...</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>

                            <hr>

                            <!-- Contenedor dinámico para hijuelas/sitios -->
                            <div id="contenedor-inmuebles">
                                <!-- Aquí se generarán formularios dinámicamente -->
                            </div>

                            <!-- Campos ocultos para Matrix -->
                            <input type="hidden" id="agregar_is_from_matrix" name="is_from_matrix" value="0">
                            <input type="hidden" id="agregar_matrix_folio" name="matrix_folio">

                            <button type="submit" class="btn btn-success btn-lg btn-block" id="btn-submit-agregar" disabled>
                                <i class="fas fa-plus"></i> Agregar Folio(s)
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