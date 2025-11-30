<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Libro de Planos') - Región del Biobío</title>

    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- AdminLTE CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}">

    <!-- DataTables CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/responsive.bootstrap4.min.css') }}">

    <!-- Select2 CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">

    <!-- SweetAlert2 CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">

    <!-- TEMA OFICIAL GOBIERNO DE CHILE -->
    <link rel="stylesheet" href="{{ asset('css/gobierno-chile-theme.css?v=5') }}">

    <!-- CSS personalizado -->
    <style>
        .child-row {
            background-color: #f8f9fa !important;
        }
        .child-row td {
            border-top: none !important;
            font-size: 0.9em;
            padding: 0.3rem 0.75rem !important;
        }
        .session-control-card {
            border-left: 4px solid #28a745;
        }
        .session-control-card.no-control {
            border-left: 4px solid #dc3545;
        }
        .folio-input-group {
            margin-bottom: 10px;
        }
        .folio-found {
            background-color: #d4edda !important;
        }
        .folio-used {
            background-color: #f8d7da !important;
        }
        .tab-content-custom {
            min-height: 600px;
        }
        .nav-tabs-custom .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff #007bff #fff;
        }
        /* Animación parpadeo para notificaciones */
        .animate-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        .btn-xs {
            padding: 0.15rem 0.35rem;
            font-size: 0.75rem;
        }
    </style>

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Session Control Status (solo para rol registro) -->
            @if(Auth::user()->isRegistro())
            <li class="nav-item dropdown" id="session-control-indicator">
                <a class="nav-link" data-toggle="dropdown" href="#" id="session-status-btn">
                    <i class="fas fa-lock text-danger" id="session-status-icon"></i>
                    <span class="badge badge-danger navbar-badge" id="session-status-text">Sin Control</span>
                    <span class="badge badge-warning navbar-badge animate-pulse" id="pending-requests-count" style="display: none; margin-left: -8px;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 320px;">
                    <!-- Estado actual -->
                    <div class="dropdown-item bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="control-status-title">Sin Control</strong>
                                <p class="text-sm mb-0" id="control-status-message">Verificando...</p>
                            </div>
                            <span class="text-sm text-muted" id="control-next-number"></span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="dropdown-item" id="control-actions">
                        <button class="btn btn-sm btn-success btn-block" id="request-control-btn" style="display: none;">
                            <i class="fas fa-key"></i> Solicitar Control
                        </button>
                        <button class="btn btn-sm btn-info btn-block" id="send-request-btn" style="display: none;">
                            <i class="fas fa-bell"></i> Pedir a Usuario
                        </button>
                        <button class="btn btn-sm btn-warning btn-block" id="release-control-btn" style="display: none;">
                            <i class="fas fa-unlock"></i> Liberar Control
                        </button>
                    </div>

                    <!-- Solicitudes pendientes -->
                    <div id="pending-requests-section" style="display: none;">
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item bg-warning-light">
                            <strong><i class="fas fa-inbox"></i> Solicitudes Pendientes</strong>
                        </div>
                        <div id="pending-requests-list">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>
                </div>
            </li>
            @endif

            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <div class="dropdown-item">
                        <strong>{{ Auth::user()->name }}</strong><br>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="brand-link" style="display: flex; align-items: center; justify-content: center; padding: 0.75rem 1rem;">
            <img src="{{ asset('LOGO_SISTEMA.png') }}" alt="Logo Sistema" class="elevation-3" style="opacity: .8; max-height: 33px; margin-right: 12px;">
            <span class="brand-text font-weight-light">Libro Planos</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                    <small class="text-light">{{ ucfirst(Auth::user()->role) }}</small>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('planos.index') }}" class="nav-link {{ request()->routeIs('planos.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-table"></i>
                            <p>Tabla General</p>
                        </a>
                    </li>

                    @if(Auth::user()->isRegistro())
                    <li class="nav-item">
                        <a href="{{ route('planos.importacion.index') }}" class="nav-link {{ request()->routeIs('planos.importacion.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-import"></i>
                            <p>Importación Masiva</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('planos.crear.index') }}" class="nav-link {{ request()->routeIs('planos.crear.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Agregar Planos</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Gestión de Usuarios</p>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Libro de Planos')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer eliminado para aprovechar máximo espacio vertical -->
    <!--
    <footer class="main-footer">
        <strong>&copy; 2025 <a href="#">Región del Biobío</a>.</strong>
        Sistema de Libro de Planos Topográficos.
        <div class="float-right d-none d-sm-inline-block">
            <b>Versión</b> 1.0.0
        </div>
    </footer>
    -->
</div>

<!-- jQuery (Local) -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

<!-- CSRF Token Setup para todas las peticiones AJAX -->
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>

<!-- Bootstrap 4 (Local) -->
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

<!-- AdminLTE App (Local) -->
<script src="{{ asset('vendor/adminlte/js/adminlte.min.js') }}"></script>

<!-- DataTables & Plugins (Local) -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/responsive.bootstrap4.min.js') }}"></script>

<!-- Select2 (Local) -->
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

<!-- SweetAlert2 (Local) -->
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>

<!-- Toastr-like functions using SweetAlert2 -->
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true
});

const toastr = {
    success: function(message, title = '') {
        Toast.fire({ icon: 'success', title: title || message, text: title ? message : '' });
    },
    error: function(message, title = '') {
        Toast.fire({ icon: 'error', title: title || message, text: title ? message : '' });
    },
    warning: function(message, title = '', options = {}) {
        Toast.fire({
            icon: 'warning',
            title: title || 'Aviso',
            html: message,
            timer: options.timeOut || 5000
        });
    },
    info: function(message, title = '') {
        Toast.fire({ icon: 'info', title: title || message, text: title ? message : '' });
    }
};
</script>

<!-- Session Control JS (solo para registro) -->
@if(Auth::user()->isRegistro())
<script>
// Configurar AJAX para enviar credenciales (cookies de sesión)
// Soluciona error 401 Unauthorized en requests AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
    },
    xhrFields: {
        withCredentials: true  // Envía cookies de sesión
    }
});

// Session Control Management - Centralizado en Navbar
$(document).ready(function() {
    initSessionControl();
});

function initSessionControl() {
    console.log('🔄 Inicializando control de sesión...');

    // Delay inicial de 500ms para permitir que la sesión se establezca completamente
    // Esto evita errores 401 al recargar la página rápidamente
    setTimeout(function() {
        checkSessionStatus();
        checkPendingRequests();
    }, 500);

    // Bind eventos
    $('#request-control-btn').on('click', requestControl);
    $('#release-control-btn').on('click', releaseControl);
    $('#send-request-btn').on('click', sendControlRequest);

    // Polling cada 5 segundos
    setInterval(function() {
        checkSessionStatus();
        checkPendingRequests();
    }, 5000);

    console.log('✅ Control de sesión inicializado');
}

function checkSessionStatus() {
    $.ajax({
        url: '{{ route("session-control.status") }}',
        method: 'GET',
        statusCode: {
            401: function() {
                // Sesión no disponible - actualizar UI silenciosamente sin mostrar error
                updateSessionUI({hasControl: false, canRequest: false});
            }
        }
    })
    .done(function(response) {
        updateSessionUI(response);
    })
    .fail(function(xhr) {
        // Solo mostrar error si NO es 401 (ya manejado arriba)
        if (xhr.status !== 401) {
            console.error('Error verificando sesión:', xhr.status);
        }
        updateSessionUI({hasControl: false, canRequest: false});
    });
}

function updateSessionUI(status) {
    const icon = $('#session-status-icon');
    const text = $('#session-status-text');
    const title = $('#control-status-title');
    const message = $('#control-status-message');
    const nextNumber = $('#control-next-number');
    const requestBtn = $('#request-control-btn');
    const releaseBtn = $('#release-control-btn');
    const sendRequestBtn = $('#send-request-btn');

    if (status.hasControl) {
        // Tiene control
        icon.removeClass('text-danger fa-lock').addClass('text-success fa-unlock');
        text.removeClass('badge-danger badge-warning').addClass('badge-success').text('Con Control');
        title.text('Tienes Control');
        message.text('Puedes crear planos');
        nextNumber.text(status.proximoCorrelativo || '');

        requestBtn.hide();
        sendRequestBtn.hide();
        releaseBtn.show();

        // Disparar evento para otras páginas
        $(document).trigger('sessionControl:changed', [true]);
    } else {
        // No tiene control
        icon.removeClass('text-success fa-unlock').addClass('text-danger fa-lock');
        text.removeClass('badge-success badge-warning').addClass('badge-danger').text('Sin Control');
        title.text('Sin Control');
        nextNumber.text('');

        releaseBtn.hide();

        if (status.whoHasControl) {
            // Otro usuario tiene control
            message.text(status.whoHasControl + ' tiene el control');
            requestBtn.hide();
            sendRequestBtn.show();
        } else if (status.canRequest) {
            // Nadie tiene control
            message.text('Control disponible');
            requestBtn.show();
            sendRequestBtn.hide();
        } else {
            message.text('No disponible');
            requestBtn.hide();
            sendRequestBtn.hide();
        }

        // Disparar evento para otras páginas
        $(document).trigger('sessionControl:changed', [false]);
    }
}

let lastPendingCount = 0;

function checkPendingRequests() {
    $.ajax({
        url: '{{ route("session-control.pending-requests") }}',
        method: 'GET',
        statusCode: {
            401: function() {
                // Sesión no disponible - ocultar secciones silenciosamente
                $('#pending-requests-count').hide();
                $('#pending-requests-section').hide();
            }
        }
    })
    .done(function(response) {
        // console.log('📬 Solicitudes pendientes:', response);

        const section = $('#pending-requests-section');
        const list = $('#pending-requests-list');
        const countBadge = $('#pending-requests-count');

        if (response.count > 0) {
            // Notificar si hay nuevas solicitudes - abrir dropdown automáticamente
            if (response.count > lastPendingCount) {
                // Abrir el dropdown para mostrar la solicitud
                $('#session-status-btn').dropdown('show');
            }
            lastPendingCount = response.count;

            // Mostrar contador en badge
            countBadge.text(response.count).show();

            // Construir lista de solicitudes
            let html = '';
            response.requests.forEach(function(req) {
                html += `
                    <div class="dropdown-item py-2" style="white-space: normal;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${req.from_user_name}</strong>
                                <small class="d-block text-muted">${req.message || 'Solicita control'}</small>
                            </div>
                            <div class="btn-group btn-group-sm ml-2">
                                <button class="btn btn-success btn-xs" onclick="respondRequest(${req.id}, 'accept')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-danger btn-xs" onclick="respondRequest(${req.id}, 'reject')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            list.html(html);
            section.show();
        } else {
            lastPendingCount = 0;
            countBadge.hide();
            section.hide();
        }
    })
    .fail(function(xhr) {
        // Solo mostrar error si NO es 401 (ya manejado arriba)
        if (xhr.status !== 401) {
            console.error('Error verificando solicitudes pendientes:', xhr.status);
        }
        $('#pending-requests-count').hide();
        $('#pending-requests-section').hide();
    });
}

function requestControl() {
    const btn = $('#request-control-btn');
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

    $.post('{{ route("session-control.request") }}')
        .done(function(response) {
            if (response.success) {
                checkSessionStatus();
                // Mantener dropdown abierto para mostrar el nuevo estado
                setTimeout(function() {
                    $('#session-status-btn').dropdown('show');
                }, 100);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('No se pudo solicitar control');
        })
        .always(function() {
            btn.html('<i class="fas fa-key"></i> Solicitar Control').prop('disabled', false);
        });
}

function releaseControl() {
    const btn = $('#release-control-btn');
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

    $.post('{{ route("session-control.release") }}')
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                checkSessionStatus();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('No se pudo liberar control');
        })
        .always(function() {
            btn.html('<i class="fas fa-unlock"></i> Liberar Control').prop('disabled', false);
        });
}

function sendControlRequest() {
    const btn = $('#send-request-btn');
    btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

    $.ajax({
        url: '{{ route("session-control.send-request") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            message: 'Necesito el control de numeración'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
            } else {
                toastr.warning(response.message);
            }
        },
        error: function() {
            toastr.error('Error al enviar solicitud');
        },
        complete: function() {
            btn.html('<i class="fas fa-bell"></i> Pedir a Usuario').prop('disabled', false);
        }
    });
}

function respondRequest(requestId, action) {
    $.ajax({
        url: '{{ url("session-control/respond-request") }}/' + requestId,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: action
        },
        success: function(response) {
            if (response.success) {
                if (action === 'accept') {
                    toastr.success(response.message);
                } else {
                    toastr.info('Solicitud rechazada');
                }
                checkSessionStatus();
                checkPendingRequests();
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Error al procesar respuesta');
        }
    });
}

function startSessionControlHeartbeat() {
    sessionControlInterval = setInterval(function() {
        $.get('{{ route("session-control.heartbeat") }}')
            .done(function(response) {
                updateSessionUI(response);
            });
    }, 30000); // Cada 30 segundos
}
</script>
@endif

@stack('scripts')

</body>
</html>