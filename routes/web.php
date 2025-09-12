<?php

use Illuminate\Support\Facades\Route;

// Autenticación
Auth::routes();

// Página principal (redirige al dashboard)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas protegidas del sistema de planos
Route::middleware('auth')->group(function () {
    
    // Rutas principales del sistema de planos (se crearán después)
    Route::get('/planos', function() {
        return view('home'); // Temporal, después será PlanoController@index
    })->name('planos.index');
    
    Route::get('/planos/create', function() {
        return view('home'); // Temporal, después será PlanoController@create  
    })->name('planos.create');
    
});
