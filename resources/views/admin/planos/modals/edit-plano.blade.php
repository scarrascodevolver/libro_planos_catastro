<!-- Modal Editar Plano -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit"></i>
                    Editar Plano
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-plano">
                @csrf
                <input type="hidden" id="edit_id" name="id">

                <!-- Loading overlay -->
                <div id="edit-loading-overlay" class="d-none" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <div class="mt-2">Cargando datos del plano...</div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Comuna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_comuna">Comuna <span class="text-danger">*</span></label>
                                <select class="form-control select2-comuna" id="edit_comuna" name="comuna" required>
                                    <option value="">Seleccionar comuna...</option>
                                    @foreach($comunas as $codigo => $nombre)
                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Responsable -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_responsable">Responsable <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_responsable" name="responsable" required maxlength="255" placeholder="Nombre del responsable">
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Persona responsable del proyecto</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Proyecto -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_proyecto">Proyecto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_proyecto" name="proyecto" required maxlength="255" placeholder="Ej: CONVENIO-FINANCIAMIENTO">
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Tipo de financiamiento o convenio asociado</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Observaciones -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_observaciones">Observaciones</label>
                                <textarea class="form-control" id="edit_observaciones" name="observaciones" rows="3" placeholder="Observaciones adicionales del plano..." maxlength="500"></textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="form-text text-muted">Información adicional relevante</small>
                                    <small class="form-text text-muted"><span id="observaciones-count">0</span>/500 caracteres</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Archivos -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-archive"></i>
                                Información de Archivos
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_archivo">Archivo <i class="fas fa-info-circle text-info" title="Identificación del archivo físico"></i></label>
                                        <input type="text" class="form-control" id="edit_archivo" name="archivo" maxlength="255" placeholder="Ej: ARC-2025-001">
                                        <small class="form-text text-muted">Código o ubicación del archivo físico</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tubo">Tubo <i class="fas fa-info-circle text-info" title="Contenedor de planos enrollados"></i></label>
                                        <input type="text" class="form-control" id="edit_tubo" name="tubo" maxlength="255" placeholder="Ej: TUBO-A-15">
                                        <small class="form-text text-muted">Identificador del tubo de almacenamiento</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tela">Tela <i class="fas fa-info-circle text-info" title="Material donde se imprime el plano"></i></label>
                                        <input type="text" class="form-control" id="edit_tela" name="tela" maxlength="255" placeholder="Ej: TELA-001">
                                        <small class="form-text text-muted">Referencia del material del plano</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_archivo_digital">Archivo Digital <i class="fas fa-info-circle text-info" title="Ubicación del archivo digital"></i></label>
                                        <input type="text" class="form-control" id="edit_archivo_digital" name="archivo_digital" maxlength="255" placeholder="Ej: /server/planos/2025/archivo.dwg">
                                        <small class="form-text text-muted">Ruta o ubicación del archivo digital</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <button type="submit" class="btn btn-primary" id="btn-guardar-plano">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>