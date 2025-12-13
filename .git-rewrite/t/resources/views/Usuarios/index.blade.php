@extends('adminlte::page')

@section('title', 'Usuarios iTracking Desk Chile')

@section('content_header')
    <h1>Usuarios</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-4 col-sm-4 col-12">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fa fa-address-card"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><b>Usuarios Activos</b></span>
                    <span class="info-box-number">12</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-4 col-sm-4 col-12">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fa fa-eraser"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><b>Usuarios Inactivos</b></span>
                    <span class="info-box-number">2</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

    </div>

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-info"></i> <b>Creación</b> de Usuarios</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-sm-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Nombre del Usuario</label>
                            <input type="text" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Pertenece a Unidad</label>
                            <select class="form-control">
                                <option>Departamento - Unidad</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-address-card"></i> Grilla de Usuarios <b>Activos</b></h3>
            <div class="card-tools">
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-block bg-gradient-warning btn-sm"><i class="fa fa-archive"></i>
                        Unidades Inactivas
                    </button>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="example1"
                               class="table table-bordered table-striped dataTable dtr-inline collapsed"
                               aria-describedby="example1_info">
                            <thead>
                            <tr>
                                <td><b>Acciones</b></td>
                                <td><b>Fecha / Hora</b></td>
                                <td><b>Nombre del Funcionario/Trabajador</b></td>
                                <td><b>Nombre de Usuario</b></td>
                                <td><b>Perfil</b></td>
                                <td><b>Unidad</b></td>
                                <td><b>Creado por</b></td>
                                <td><b>Estado</b></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <center>
                                        <button class="btn btn-xs bg-success"><i class="fa fa-eye"></i></button>
                                        <button class="btn btn-xs bg-warning"><i class="fa fa-edit"></i></button>
                                    </center>
                                </td>
                                <td>16-03-2025 17:06:22</td>
                                <td>Juan Perez Lopez</td>
                                <td>jperez</td>
                                <td>Usuario Terreno</td>
                                <td>Unidad de Multas</td>
                                <td>Juan Perez Lopez</td>
                                <td>Activo</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td><b>Acciones</b></td>
                                <td><b>Fecha / Hora</b></td>
                                <td><b>Nombre del Funcionario/Trabajador</b></td>
                                <td><b>Nombre de Usuario</b></td>
                                <td><b>Perfil</b></td>
                                <td><b>Unidad</b></td>
                                <td><b>Creado por</b></td>
                                <td><b>Estado</b></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
@stop

@section('css')
    <link rel="stylesheet"
          href="https://adminlte.io/themes/v3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
          href="https://adminlte.io/themes/v3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet"
          href="https://adminlte.io/themes/v3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@stop

@section('js')

    <script src="https://adminlte.io/themes/v3/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/jszip/jszip.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script>
        $(function () {
            $(function () {
                $("#example1").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": [
                        {
                            extend: 'copy',
                            text: 'Copiar',
                        },
                        {
                            extend: 'csv',
                            text: 'CSV'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel'
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF'
                        },
                        {
                            extend: 'print',
                            text: 'Imprimir'
                        },
                        {
                            extend: 'colvis',
                            text: 'Visibilidad de Columnas'
                        }
                    ],
                    "language": {
                        "sEmptyTable": "No hay datos disponibles en la tabla",
                        "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                        "sInfoFiltered": "(filtrado de _MAX_ entradas en total)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

                $('#example2').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "buttons": [
                        {
                            extend: 'copy',
                            text: 'Copiar',
                        },
                        {
                            extend: 'csv',
                            text: 'CSV'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel'
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF'
                        },
                        {
                            extend: 'print',
                            text: 'Imprimir'
                        },
                        {
                            extend: 'colvis',
                            text: 'Visibilidad de Columnas'
                        }
                    ],
                    "language": {
                        "sEmptyTable": "No hay datos disponibles en la tabla",
                        "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                        "sInfoFiltered": "(filtrado de _MAX_ entradas en total)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                });
            });

        });
    </script>
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
