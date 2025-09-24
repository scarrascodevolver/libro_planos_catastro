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

                <div class="modal-body">
                    <div class="row">
                        <!-- Comuna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_comuna">Comuna <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_comuna" name="comuna" required>
                                    @foreach($comunas as $codigo => $nombre)
                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Responsable -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_responsable">Responsable <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_responsable" name="responsable" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Proyecto -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_proyecto">Proyecto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_proyecto" name="proyecto" required maxlength="255" placeholder="Ej: CONVENIO-FINANCIAMIENTO">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Observaciones -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_observaciones">Observaciones</label>
                                <textarea class="form-control" id="edit_observaciones" name="observaciones" rows="3" placeholder="Observaciones adicionales del plano..."></textarea>
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
                                        <label for="edit_archivo">Archivo</label>
                                        <input type="text" class="form-control" id="edit_archivo" name="archivo" maxlength="255" placeholder="Nombre/ubicación del archivo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tubo">Tubo</label>
                                        <input type="text" class="form-control" id="edit_tubo" name="tubo" maxlength="255" placeholder="Identificación del tubo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_tela">Tela</label>
                                        <input type="text" class="form-control" id="edit_tela" name="tela" maxlength="255" placeholder="Identificación de la tela">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_archivo_digital">Archivo Digital</label>
                                        <input type="text" class="form-control" id="edit_archivo_digital" name="archivo_digital" maxlength="255" placeholder="Ubicación archivo digital">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>