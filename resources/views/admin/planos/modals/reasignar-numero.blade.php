<!-- Modal Reasignar Número -->
<div class="modal fade" id="reasignar-modal" tabindex="-1" role="dialog" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="reasignarModalLabel">
                    <i class="fas fa-copy"></i>
                    Reasignar Plano a Nuevo Número
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-reasignar-plano">
                @csrf
                <input type="hidden" id="reasignar_id" name="id">

                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> ¿Qué hace esta acción?</h6>
                        <ul class="mb-0 pl-3">
                            <li>Crea un <strong>nuevo plano</strong> con el próximo número correlativo</li>
                            <li>Copia todos los datos y folios del plano original</li>
                            <li>El plano original se mantiene como registro histórico</li>
                            <li>Podrás editar el nuevo plano después de crearlo</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Número Actual</label>
                                <input type="text" class="form-control bg-light" id="numero_actual" readonly>
                            </div>
                        </div>
                        <div class="col-2 text-center d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-right fa-2x text-warning"></i>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Nuevo Número</label>
                                <input type="text" class="form-control bg-success text-white font-weight-bold" id="nuevo_numero" name="nuevo_numero" readonly
                                       placeholder="Generando...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Folios que se copiarán: <span id="cantidad_folios_reasignar" class="badge badge-primary">0</span></label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" id="btn-confirmar-reasignar">
                        <i class="fas fa-copy"></i>
                        Crear Nuevo Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>