<!-- Modal Reasignar Número -->
<div class="modal fade" id="reasignar-modal" tabindex="-1" role="dialog" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="reasignarModalLabel">
                    <i class="fas fa-exchange-alt"></i>
                    Reasignar Número de Plano
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-reasignar-plano">
                @csrf
                <input type="hidden" id="reasignar_id" name="id">

                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> ¡Atención!</h6>
                        Esta acción cambiará permanentemente el número del plano. Asegúrese de que el nuevo número sea correcto y esté disponible.
                    </div>

                    <div class="form-group">
                        <label for="nuevo_numero">Nuevo Número de Plano <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nuevo_numero" name="nuevo_numero" required maxlength="50"
                               placeholder="Ej: 0830329999SU" pattern="^08[0-9]{8}[A-Z]{2}$">
                        <small class="form-text text-muted">
                            Formato: 08 + código comuna (3) + correlativo (6) + tipo (2)<br>
                            Ejemplo: <code>0830329999SU</code>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="motivo_reasignacion">Motivo de la Reasignación</label>
                        <textarea class="form-control" id="motivo_reasignacion" name="motivo" rows="3"
                                  placeholder="Describe brevemente por qué se reasigna este número..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-exchange-alt"></i>
                        Reasignar Número
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>