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
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <div class="dropdown-item" id="session-control-info">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title" id="control-status-title">
                                    Control de Numeración
                                </h3>
                                <p class="text-sm" id="control-status-message">
                                    Sin control activo
                                </p>
                                <p class="text-sm text-muted" id="control-next-number">
                                    <i class="far fa-clock mr-1"></i> Próximo: --
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item">
                        <button class="btn btn-sm btn-primary" id="request-control-btn" style="display: none;">
                            Solicitar Control
                        </button>
                        <button class="btn btn-sm btn-warning" id="release-control-btn" style="display: none;">
                            Liberar Control
                        </button>
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
        <a href="{{ route('home') }}" class="brand-link">
            <i class="fas fa-map brand-icon"></i>
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

<!-- Session Control JS (solo para registro) -->
@if(Auth::user()->isRegistro())
<script>
// Session Control Management
let sessionControlInterval;

$(document).ready(function() {
    initSessionControl();
    startSessionControlHeartbeat();
});

function initSessionControl() {
    checkSessionStatus();

    $('#request-control-btn').on('click', requestControl);
    $('#release-control-btn').on('click', releaseControl);
}

function checkSessionStatus() {
    $.get('{{ route("session-control.status") }}')
        .done(function(response) {
            updateSessionUI(response);
        })
        .fail(function() {
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

    if (status.hasControl) {
        icon.removeClass('text-danger fa-lock').addClass('text-success fa-unlock');
        text.removeClass('badge-danger').addClass('badge-success').text('Con Control');
        title.text('Tienes Control');
        message.text('Puedes crear números correlativos');
        nextNumber.html('<i class="fas fa-arrow-right mr-1"></i> Próximo: ' + (status.proximoNumero || 'Cargando...'));
        requestBtn.hide();
        releaseBtn.show();
    } else {
        icon.removeClass('text-success fa-unlock').addClass('text-danger fa-lock');
        text.removeClass('badge-success').addClass('badge-danger').text('Sin Control');
        title.text('Sin Control');

        if (status.whoHasControl) {
            message.text(status.whoHasControl + ' tiene el control');
            requestBtn.hide();
        } else {
            message.text('Ningún usuario tiene control');
            requestBtn.show();
        }

        nextNumber.html('<i class="far fa-clock mr-1"></i> Próximo: --');
        releaseBtn.hide();
    }
}

function requestControl() {
    $.post('{{ route("session-control.request") }}')
        .done(function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success');
                checkSessionStatus();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo solicitar control', 'error');
        });
}

function releaseControl() {
    Swal.fire({
        title: '¿Liberar control?',
        text: 'Otros usuarios podrán solicitar control después',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, liberar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("session-control.release") }}')
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Liberado', response.message, 'success');
                        checkSessionStatus();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'No se pudo liberar control', 'error');
                });
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