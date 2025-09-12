@extends('adminlte::page')

@section('title', 'Sistema Libro de Planos - Región del Biobío')

@section('content_header')
    <h1><i class="fas fa-map"></i> {{ __('planos.sistema_titulo') }}</h1>
    <p class="text-muted">{{ __('planos.region_subtitulo') }}</p>
@stop

@section('content')

<div class="row">
    <!-- Card 1: Total Planos -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-map-marked-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>{{ __('planos.total_planos') }}</b></span>
                <span class="info-box-number">0</span>
                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">{{ __('planos.planos_registrados') }}</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Planos SR (Saneamiento Rural) -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-seedling"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Saneamiento Rural</b></span>
                <span class="info-box-number">0</span>
                <div class="progress">
                    <div class="progress-bar bg-success" style="width: 0%"></div>
                </div>
                <span class="progress-description">Planos tipo SR</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Planos SU (Saneamiento Urbano) -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-city"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Saneamiento Urbano</b></span>
                <span class="info-box-number">0</span>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width: 0%"></div>
                </div>
                <span class="progress-description">Planos tipo SU</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Planos Fiscales -->
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-university"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Planos Fiscales</b></span>
                <span class="info-box-number">0</span>
                <div class="progress">
                    <div class="progress-bar bg-info" style="width: 0%"></div>
                </div>
                <span class="progress-description">Planos CU y CR</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Botón principal: Ir al Sistema -->
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-rocket"></i> Acceso Rápido</h3>
            </div>
            <div class="card-body text-center">
                <p class="lead">Bienvenido al Sistema de Libro de Planos Digital de la Región del Biobío</p>
                <p>Gestiona, importa y consulta planos topográficos de manera eficiente</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="{{ route('planos.index') }}" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-table"></i> Ver Todos los Planos
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('planos.create') }}" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-plus"></i> Crear Nuevo Plano
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sistema-planos.css') }}">
@stop

@section('js')
    <script>
        console.log("Sistema Libro de Planos - Región del Biobío cargado correctamente");
    </script>
@stop