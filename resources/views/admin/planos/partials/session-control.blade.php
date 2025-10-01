<!-- Control de Numeración (Solo para rol registro) -->
@if(Auth::user()->isRegistro())
<div class="card session-control-card no-control" id="session-control-widget">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lock" id="session-icon"></i>
            Control de Numeración Correlativa
        </h3>
        <div class="card-tools">
            <span class="badge badge-danger" id="session-badge">Sin Control</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-icon">
                        <i class="fas fa-hashtag text-primary"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Último Correlativo</span>
                        <span class="info-box-number" id="ultimo-correlativo">---</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-icon">
                        <i class="fas fa-arrow-right text-success"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Próximo Número</span>
                        <span class="info-box-number" id="proximo-numero">---</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info" id="session-status-message">
                    <i class="fas fa-info-circle"></i>
                    <span id="status-text">Necesitas obtener control de numeración para crear planos</span>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" id="request-control-widget-btn" style="display: none;">
                        <i class="fas fa-key"></i> Solicitar Control
                    </button>
                    <button type="button" class="btn btn-warning" id="release-control-widget-btn" style="display: none;">
                        <i class="fas fa-unlock"></i> Liberar Control
                    </button>
                    <button type="button" class="btn btn-info" id="refresh-status-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar Estado
                    </button>
                </div>
            </div>
        </div>

        <!-- Información adicional cuando tiene control -->
        <div class="row mt-3" id="control-info" style="display: none;">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Tienes Control de Numeración</h6>
                    <ul class="mb-0">
                        <li>Puedes crear números correlativos secuenciales</li>
                        <li>Solo tú puedes generar números mientras tengas control</li>
                        <li>Libera el control cuando termines para permitir que otros usuarios trabajen</li>
                        <li>El control se mantiene activo durante tu sesión</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    initSessionControlWidget();
});

function initSessionControlWidget() {
    checkSessionStatusWidget();

    $('#request-control-widget-btn').on('click', requestControlWidget);
    $('#release-control-widget-btn').on('click', releaseControlWidget);
    $('#refresh-status-btn').on('click', checkSessionStatusWidget);
}

function checkSessionStatusWidget() {
    $('#refresh-status-btn').html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

    $.get('{{ route("session-control.status") }}')
        .done(function(response) {
            updateSessionWidgetUI(response);
        })
        .fail(function() {
            updateSessionWidgetUI({hasControl: false, canRequest: false});
        })
        .always(function() {
            $('#refresh-status-btn').html('<i class="fas fa-sync-alt"></i> Actualizar Estado');
        });
}

function updateSessionWidgetUI(status) {
    const card = $('#session-control-widget');
    const icon = $('#session-icon');
    const badge = $('#session-badge');
    const statusText = $('#status-text');
    const requestBtn = $('#request-control-widget-btn');
    const releaseBtn = $('#release-control-widget-btn');
    const controlInfo = $('#control-info');
    const ultimoCorrelativo = $('#ultimo-correlativo');
    const proximoNumero = $('#proximo-numero');

    if (status.hasControl) {
        // Usuario tiene control
        card.removeClass('no-control').addClass('session-control-card');
        icon.removeClass('fa-lock text-danger').addClass('fa-unlock text-success');
        badge.removeClass('badge-danger').addClass('badge-success').text('Con Control');
        statusText.text('Tienes control activo - Puedes crear planos');
        $('#session-status-message').removeClass('alert-info').addClass('alert-success');

        requestBtn.hide();
        releaseBtn.show();
        controlInfo.show();

        ultimoCorrelativo.text(status.proximoCorrelativo - 1 || '---');
        proximoNumero.text(status.proximoNumero || 'Cargando...');

    } else {
        // Usuario no tiene control
        card.removeClass('session-control-card').addClass('no-control');
        icon.removeClass('fa-unlock text-success').addClass('fa-lock text-danger');
        badge.removeClass('badge-success').addClass('badge-danger').text('Sin Control');
        controlInfo.hide();

        ultimoCorrelativo.text('---');
        proximoNumero.text('---');

        if (status.whoHasControl) {
            statusText.text(status.whoHasControl + ' tiene el control actualmente');
            $('#session-status-message').removeClass('alert-success').addClass('alert-warning');
            requestBtn.hide();
        } else {
            statusText.text('Ningún usuario tiene control - Puedes solicitarlo');
            $('#session-status-message').removeClass('alert-success alert-warning').addClass('alert-info');
            requestBtn.show();
        }

        releaseBtn.hide();
    }
}

function requestControlWidget() {
    $('#request-control-widget-btn').html('<i class="fas fa-spinner fa-spin"></i> Solicitando...');

    $.post('{{ route("session-control.request") }}')
        .done(function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success');
                checkSessionStatusWidget();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo solicitar control', 'error');
        })
        .always(function() {
            $('#request-control-widget-btn').html('<i class="fas fa-key"></i> Solicitar Control');
        });
}

function releaseControlWidget() {
    Swal.fire({
        title: '¿Liberar control de numeración?',
        text: 'Otros usuarios podrán solicitar control después de liberarlo',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, liberar',
        cancelButtonText: 'Mantener control'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#release-control-widget-btn').html('<i class="fas fa-spinner fa-spin"></i> Liberando...');

            $.post('{{ route("session-control.release") }}')
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Control Liberado', response.message, 'success');
                        checkSessionStatusWidget();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudo liberar control', 'error');
                })
                .always(function() {
                    $('#release-control-widget-btn').html('<i class="fas fa-unlock"></i> Liberar Control');
                });
        }
    });
}

// Auto-refresh cada minuto
setInterval(function() {
    checkSessionStatusWidget();
}, 60000);
</script>
@endpush

@endif