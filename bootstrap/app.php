<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Excluir ruta planos del CSRF para permitir POST de DataTables
        $middleware->validateCsrfTokens(except: [
            'planos',
            'planos/*',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Liberar controles inactivos cada minuto
        $schedule->command('session:release-inactive')->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
