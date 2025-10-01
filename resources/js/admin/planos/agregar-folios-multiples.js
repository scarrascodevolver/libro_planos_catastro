// ========================================
// TAB 2: AGREGAR FOLIO - Múltiples hijuelas/sitios
// ========================================

// BLOQUE 3: Listener select cantidad
$('#cantidad-inmuebles').on('change', function() {
    const cantidad = parseInt($(this).val());
    if (cantidad) {
        generarFormulariosInmuebles(cantidad);
        $('#btn-submit-agregar').prop('disabled', false);
    } else {
        $('#contenedor-inmuebles').html('');
        $('#btn-submit-agregar').prop('disabled', true);
    }
});

// BLOQUE 4: Generar formularios dinámicos
function generarFormulariosInmuebles(cantidad) {
    let html = '';
    const esRural = esRuralGlobal;
    const labelTipo = tipoInmuebleGlobal;

    for (let i = 1; i <= cantidad; i++) {
        html += `
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <strong><i class="fas fa-${esRural ? 'tree' : 'city'}"></i> ${labelTipo} #${i}</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-${esRural ? '3' : '6'}">
                        <div class="form-group">
                            <label>Número ${labelTipo} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control numero-inmueble"
                                   data-index="${i-1}" value="${i}" required min="1">
                        </div>
                    </div>`;

        if (esRural) {
            html += `
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hectáreas</label>
                            <input type="text" class="form-control hectareas-input"
                                   data-index="${i-1}" placeholder="0,0000">
                            <small class="text-muted">Formato: 2,5000</small>
                        </div>
                    </div>`;
        }

        html += `
                    <div class="col-md-${esRural ? '6' : '6'}">
                        <div class="form-group">
                            <label>Metros Cuadrados (m²) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control m2-input"
                                   data-index="${i-1}" placeholder="0,00" required>
                            <small class="text-muted">Formato: 25.000,00</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    $('#contenedor-inmuebles').html(html);

    // Agregar listeners conversión ha ↔ m²
    if (esRural) {
        attachConversionListeners();
    }
}

// BLOQUE 5: Conversión hectáreas ↔ m²
function attachConversionListeners() {
    // Hectáreas -> M²
    $(document).on('input', '.hectareas-input', function() {
        const index = $(this).data('index');
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');

        if (valor && !isNaN(valor)) {
            const ha = parseFloat(valor);
            const m2 = ha * 10000;
            $(`.m2-input[data-index="${index}"]`).val(formatNumber(m2, 2));
        }
    });

    // M² -> Hectáreas
    $(document).on('input', '.m2-input', function() {
        const index = $(this).data('index');
        let valor = $(this).val().replace(/\./g, '').replace(',', '.');

        if (valor && !isNaN(valor) && esRuralGlobal) {
            const m2 = parseFloat(valor);
            const ha = m2 / 10000;
            $(`.hectareas-input[data-index="${index}"]`).val(formatNumber(ha, 4));
        }
    });
}

function formatNumber(num, decimals) {
    return num.toFixed(decimals).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Búsqueda en Matrix (opcional)
$('#btn-buscar-matrix').on('click', function() {
    const folio = $('#buscar-matrix-folio').val().trim();

    if (!folio) {
        Swal.fire('Atención', 'Ingresa un número de folio para buscar', 'warning');
        return;
    }

    const $btn = $(this);
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
        url: '{{ route("api.matrix.buscar") }}',
        method: 'GET',
        data: { folio: folio },
        success: function(response) {
            if (response.encontrado) {
                // Autocompletar datos del formulario
                $('#agregar_folio').val(response.data.folio || '');
                $('#agregar_solicitante').val(response.data.nombres || '');
                $('#agregar_apellido_paterno').val(response.data.apellido_paterno || '');
                $('#agregar_apellido_materno').val(response.data.apellido_materno || '');

                // Marcar como proveniente de Matrix
                $('#agregar_is_from_matrix').val('1');
                $('#agregar_matrix_folio').val(folio);

                Swal.fire({
                    icon: 'success',
                    title: '¡Folio encontrado!',
                    text: 'Datos autocompletados desde Matrix',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Folio no encontrado',
                    text: 'Puedes ingresar los datos manualmente',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al buscar en Matrix', 'error');
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalText);
        }
    });
});

// BLOQUE 6: Submit formulario
$('#form-agregar-folio').on('submit', function(e) {
    e.preventDefault();

    const planoId = $('#modal-gestionar-folios').data('plano-id');

    if (!planoId) {
        Swal.fire('Error', 'No se pudo identificar el plano', 'error');
        return;
    }

    // Validar que haya seleccionado cantidad
    if ($('.numero-inmueble').length === 0) {
        Swal.fire('Atención', 'Debes seleccionar cuántas ' + tipoInmuebleGlobal.toLowerCase() + 's tiene este folio', 'warning');
        return;
    }

    // Recolectar datos base
    const formData = {
        folio: $('#agregar_folio').val().trim(),
        solicitante: $('#agregar_solicitante').val().trim(),
        apellido_paterno: $('#agregar_apellido_paterno').val().trim(),
        apellido_materno: $('#agregar_apellido_materno').val().trim(),
        is_from_matrix: $('#agregar_is_from_matrix').val() === '1',
        matrix_folio: $('#agregar_matrix_folio').val() || null,
        inmuebles: [],
        _token: '{{ csrf_token() }}'
    };

    // Validar solicitante
    if (!formData.solicitante) {
        Swal.fire('Atención', 'El campo Solicitante es obligatorio', 'warning');
        return;
    }

    // Recolectar inmuebles dinámicamente
    let errores = [];
    $('.numero-inmueble').each(function() {
        const index = $(this).data('index');
        const numero = parseInt($(this).val());
        const m2Input = $(`.m2-input[data-index="${index}"]`).val();

        if (!numero || numero < 1) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: Número inválido`);
            return;
        }

        if (!m2Input) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: M² es obligatorio`);
            return;
        }

        const m2 = parseFloat(m2Input.replace(/\./g, '').replace(',', '.'));

        if (isNaN(m2) || m2 <= 0) {
            errores.push(`${tipoInmuebleGlobal} #${index+1}: M² inválido`);
            return;
        }

        const inmueble = {
            numero_inmueble: numero,
            m2: m2
        };

        // Agregar hectáreas solo si es RURAL
        if (esRuralGlobal) {
            const haInput = $(`.hectareas-input[data-index="${index}"]`).val();
            if (haInput) {
                inmueble.hectareas = parseFloat(haInput.replace(/\./g, '').replace(',', '.'));
            }
        }

        formData.inmuebles.push(inmueble);
    });

    // Mostrar errores si los hay
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores en el formulario',
            html: errores.join('<br>')
        });
        return;
    }

    // Deshabilitar botón
    const $submitBtn = $('#btn-submit-agregar');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Agregando...');

    // Enviar AJAX
    $.ajax({
        url: `{{ url('/planos') }}/${planoId}/agregar-folio`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Folios agregados!',
                    html: response.message,
                    timer: 2500,
                    showConfirmButton: false
                });

                // Cerrar modal y recargar tabla
                $('#modal-gestionar-folios').modal('hide');
                planosTable.draw(false);

                // Limpiar formulario
                $('#form-agregar-folio')[0].reset();
                $('#contenedor-inmuebles').html('');
                $('#cantidad-inmuebles').val('');
                $('#btn-submit-agregar').prop('disabled', true);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            let mensaje = 'Error al agregar folios';

            if (xhr.responseJSON?.message) {
                mensaje = xhr.responseJSON.message;
            } else if (xhr.responseJSON?.errors) {
                const errores = Object.values(xhr.responseJSON.errors).flat();
                mensaje = errores.join('<br>');
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: mensaje
            });
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
