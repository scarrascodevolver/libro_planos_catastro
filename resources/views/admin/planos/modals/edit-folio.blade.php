<!-- Modal Editar Folio Individual -->
<div class="modal fade" id="edit-folio-modal" tabindex="-1" role="dialog" aria-labelledby="editFolioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="editFolioModalLabel">
                    <i class="fas fa-file-alt"></i>
                    Editar Folio Individual
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-folio">
                @csrf
                <input type="hidden" id="edit_folio_id" name="folio_id">

                <!-- Loading overlay -->
                <div id="edit-folio-loading-overlay" class="d-none" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <div class="mt-2">Cargando datos del folio...</div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Folio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_folio_numero">Folio</label>
                                <input type="text" class="form-control" id="edit_folio_numero" name="folio" maxlength="50" placeholder="Número de folio">
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Puede estar vacío para planos fiscales</small>
                            </div>
                        </div>

                        <!-- Tipo Inmueble -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_folio_tipo_inmueble">Tipo <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_folio_tipo_inmueble" name="tipo_inmueble" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="HIJUELA">HIJUELA</option>
                                    <option value="SITIO">SITIO</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Solicitante -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_folio_solicitante">Solicitante <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_folio_solicitante" name="solicitante" required maxlength="255" placeholder="Nombre del solicitante">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Apellido Paterno -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_folio_apellido_paterno">Apellido Paterno</label>
                                <input type="text" class="form-control" id="edit_folio_apellido_paterno" name="apellido_paterno" maxlength="255" placeholder="Apellido paterno">
                                <small class="form-text text-muted">Opcional para casos fiscales</small>
                            </div>
                        </div>

                        <!-- Apellido Materno -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_folio_apellido_materno">Apellido Materno</label>
                                <input type="text" class="form-control" id="edit_folio_apellido_materno" name="apellido_materno" maxlength="255" placeholder="Apellido materno">
                                <small class="form-text text-muted">Opcional para casos fiscales</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Número Inmueble (solo para folio con 1 inmueble) -->
                        <div class="col-md-4" id="edit_folio_numero_inmueble_container">
                            <div class="form-group">
                                <label for="edit_folio_numero_inmueble" id="edit_folio_numero_inmueble_label">Número Inmueble</label>
                                <input type="number" class="form-control" id="edit_folio_numero_inmueble" name="numero_inmueble" min="1" placeholder="Número">
                                <small class="form-text text-muted">Identificador numérico</small>
                            </div>
                        </div>

                        <!-- Hectáreas (solo para HIJUELA) -->
                        <div class="col-md-4" id="edit_folio_hectareas_container">
                            <div class="form-group">
                                <label for="edit_folio_hectareas">Hectáreas</label>
                                <input type="text" class="form-control" id="edit_folio_hectareas" name="hectareas" placeholder="0,00" inputmode="decimal" onkeypress="return validarNumeroDecimalHectareas(event)">
                                <small class="form-text text-muted">Solo para hijuelas</small>
                            </div>
                        </div>

                        <!-- M² -->
                        <div class="col-md-4" id="edit_folio_m2_container">
                            <div class="form-group">
                                <label for="edit_folio_m2">M² <span class="text-danger" id="edit_folio_m2_required">*</span></label>
                                <input type="text" class="form-control" id="edit_folio_m2" name="m2" placeholder="0,00" inputmode="decimal" onkeypress="return validarNumeroDecimal(event)">
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-info d-none" id="edit_folio_rural_hint">
                                    <i class="fas fa-info-circle"></i> Para rurales: Complete hectáreas o m² (al menos uno)
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Inmuebles (solo para folios con múltiples sitios/hijuelas) -->
                    <div class="row d-none" id="edit_folio_inmuebles_section">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list"></i>
                                        <span id="edit_folio_inmuebles_title">Sitios/Hijuelas de este Folio</span>
                                        <span class="badge badge-info ml-2" id="edit_folio_inmuebles_count">0</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center" style="width: 80px;">#</th>
                                                    <th class="text-right" style="width: 150px;">Hectáreas</th>
                                                    <th class="text-right" style="width: 150px;">M²</th>
                                                </tr>
                                            </thead>
                                            <tbody id="edit_folio_inmuebles_tbody">
                                                <!-- Se llenará dinámicamente con JavaScript -->
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th class="text-right">TOTAL:</th>
                                                    <th class="text-right" id="edit_folio_total_hectareas">-</th>
                                                    <th class="text-right" id="edit_folio_total_m2">-</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos técnicos ocultos -->
                    <input type="hidden" id="edit_folio_matrix_folio" name="matrix_folio">
                    <input type="hidden" id="edit_folio_is_from_matrix" name="is_from_matrix">
                </div>

                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Los campos marcados con * son obligatorios</small>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-info" id="btn-guardar-folio">
                                    <i class="fas fa-save"></i>
                                    Guardar Folio
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>