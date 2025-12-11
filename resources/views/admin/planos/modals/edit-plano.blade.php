<!-- Modal Editar Plano -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Editar Plano
                    <span id="edit-numero-plano" class="badge badge-light ml-2"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit_plano_id">

                <!-- DATOS GENERALES DEL PLANO -->
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <strong><i class="fas fa-file-alt"></i> Datos Generales</strong>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Comuna</label>
                                    <select class="form-control form-control-sm" id="edit_comuna">
                                        <option value="">Seleccionar...</option>
                                        @foreach($comunas as $codigo => $nombre)
                                            <option value="{{ $nombre }}" data-codigo="{{ $codigo }}" data-provincia="{{ $comunasProvincia[$nombre] ?? '' }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Tipo</label>
                                    <select class="form-control form-control-sm" id="edit_tipo_saneamiento">
                                        <option value="SR">SR - Saneamiento Rural</option>
                                        <option value="SU">SU - Saneamiento Urbano</option>
                                        <option value="CR">CR - Fiscal Rural</option>
                                        <option value="CU">CU - Fiscal Urbano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Provincia</label>
                                    <select class="form-control form-control-sm" id="edit_provincia">
                                        <option value="">Seleccionar provincia...</option>
                                        @foreach($provincias as $provincia)
                                            <option value="{{ $provincia }}">{{ $provincia }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Responsable</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_responsable">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Mes</label>
                                    <select class="form-control form-control-sm" id="edit_mes">
                                        <option value="ENERO">Enero</option>
                                        <option value="FEBRERO">Febrero</option>
                                        <option value="MARZO">Marzo</option>
                                        <option value="ABRIL">Abril</option>
                                        <option value="MAYO">Mayo</option>
                                        <option value="JUNIO">Junio</option>
                                        <option value="JULIO">Julio</option>
                                        <option value="AGOSTO">Agosto</option>
                                        <option value="SEPTIEMBRE">Septiembre</option>
                                        <option value="OCTUBRE">Octubre</option>
                                        <option value="NOVIEMBRE">Noviembre</option>
                                        <option value="DICIEMBRE">Diciembre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Año</label>
                                    <input type="number" class="form-control form-control-sm" id="edit_ano" min="2020" max="2030">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Proyecto</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_proyecto">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small mb-1">Observaciones</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_observaciones">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOLIOS -->
                <div class="card">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-list"></i> Folios <span id="edit-total-folios" class="badge badge-primary">0</span></strong>
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarFilaFolio()">
                            <i class="fas fa-plus"></i> Agregar Folio
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="tabla-folios-edit">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="120">Folio</th>
                                        <th width="150">Solicitante</th>
                                        <th width="120">Ap. Paterno</th>
                                        <th width="120">Ap. Materno</th>
                                        <th width="100">Tipo</th>
                                        <th width="110">Hectáreas <small class="text-muted">(auto)</small></th>
                                        <th width="120">M²</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody id="folios-tbody">
                                    <!-- Filas de folios se agregan aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-light p-2 border-top">
                            <div class="row text-center small">
                                <div class="col-4">
                                    <span class="text-muted">Total Folios:</span>
                                    <strong id="resumen-folios">0</strong>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted">Total Hectáreas:</span>
                                    <strong id="resumen-hectareas">0</strong>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted">Total M²:</span>
                                    <strong id="resumen-m2">0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarPlanoCompleto()">
                    <i class="fas fa-save"></i> Guardar Todo
                </button>
            </div>
        </div>
    </div>
</div>
