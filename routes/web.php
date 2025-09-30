<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PlanoController;
use App\Http\Controllers\Admin\PlanoCreacionController;
use App\Http\Controllers\Admin\PlanoImportacionController;
use App\Http\Controllers\Admin\PlanoHistoricoController;
use App\Http\Controllers\Admin\SessionControlController;

// Autenticación
Auth::routes();

// Página principal (redirige al dashboard)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas protegidas del sistema de planos
Route::middleware('auth')->group(function () {

    // TAB 1: Tabla General - PlanoController
    Route::get('/planos', [PlanoController::class, 'index'])->name('planos.index');
    Route::get('/planos/contadores', [PlanoController::class, 'getContadores'])->name('planos.contadores');

    // Rutas específicas ANTES de rutas genéricas con {id}
    Route::get('/planos/folios/{folioId}/edit', [PlanoController::class, 'editFolio'])->name('planos.folios.edit');
    Route::put('/planos/folios/{folioId}', [PlanoController::class, 'updateFolio'])->name('planos.folios.update');

    // Rutas con {id} específicas ANTES de show genérico
    Route::get('/planos/{id}/edit', [PlanoController::class, 'edit'])->name('planos.edit');
    Route::get('/planos/{id}/folios-expansion', [PlanoController::class, 'getFoliosExpansion'])->name('planos.folios-expansion');
    Route::get('/planos/{id}/detalles-completos', [PlanoController::class, 'getDetallesCompletos'])->name('planos.detalles-completos');
    Route::get('/planos/{id}/folios-gestion', [PlanoController::class, 'getFoliosParaGestion'])->name('planos.folios.gestion');
    Route::post('/planos/{id}/reasignar', [PlanoController::class, 'reasignar'])->name('planos.reasignar');
    Route::post('/planos/{id}/quitar-folios', [PlanoController::class, 'quitarFolios'])->name('planos.folios.quitar');
    Route::put('/planos/{id}', [PlanoController::class, 'update'])->name('planos.update');
    Route::delete('/planos/{id}', [PlanoController::class, 'destroy'])->name('planos.destroy');

    // Ruta genérica show AL FINAL
    Route::get('/planos/{id}', [PlanoController::class, 'show'])->name('planos.show');

    // TAB 2: Importación - PlanoImportacionController
    Route::get('/planos/importacion/index', [PlanoImportacionController::class, 'index'])->name('planos.importacion.index');
    Route::post('/planos/importacion/preview-matrix', [PlanoImportacionController::class, 'previewMatrix'])->name('planos.importacion.preview-matrix');
    Route::post('/planos/importacion/import-matrix', [PlanoImportacionController::class, 'importMatrix'])->name('planos.importacion.import-matrix');
    Route::get('/planos/importacion/estadisticas-matrix', [PlanoImportacionController::class, 'getEstadisticasMatrix'])->name('planos.importacion.estadisticas-matrix');
    Route::delete('/planos/importacion/limpiar-matrix', [PlanoImportacionController::class, 'limpiarMatrix'])->name('planos.importacion.limpiar-matrix');

    // TAB 3: Crear Planos - PlanoCreacionController
    Route::get('/planos/crear/index', [PlanoCreacionController::class, 'index'])->name('planos.crear.index');
    Route::post('/planos/crear/buscar-folio', [PlanoCreacionController::class, 'buscarFolio'])->name('planos.crear.buscar-folio');
    Route::post('/planos/crear/buscar-folios-masivos', [PlanoCreacionController::class, 'buscarFoliosMasivos'])->name('planos.crear.buscar-folios-masivos');
    Route::post('/planos/crear/validar-folios', [PlanoCreacionController::class, 'validarFolios'])->name('planos.crear.validar-folios');
    Route::post('/planos/crear/store', [PlanoCreacionController::class, 'store'])->name('planos.crear.store');

    // Importación Histórica - PlanoHistoricoController
    Route::get('/planos/historico', [PlanoHistoricoController::class, 'showImportForm'])->name('admin.planos.historico');
    Route::post('/planos/historico/preview', [PlanoHistoricoController::class, 'previewExcel'])->name('admin.planos.historico.preview');
    Route::post('/planos/historico/import', [PlanoHistoricoController::class, 'importHistorico'])->name('admin.planos.historico.import');

    // Control de Sesiones - SessionControlController
    Route::get('/session-control/status', [SessionControlController::class, 'getStatus'])->name('session-control.status');
    Route::post('/session-control/request', [SessionControlController::class, 'requestControl'])->name('session-control.request');
    Route::post('/session-control/release', [SessionControlController::class, 'releaseControl'])->name('session-control.release');
    Route::post('/session-control/consume', [SessionControlController::class, 'consumeCorrelativo'])->name('session-control.consume');
    Route::post('/session-control/generar-numero', [SessionControlController::class, 'generarNumeroPlano'])->name('session-control.generar-numero');
    Route::get('/session-control/heartbeat', [SessionControlController::class, 'heartbeat'])->name('session-control.heartbeat');

});
