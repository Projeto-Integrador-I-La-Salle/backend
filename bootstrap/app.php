<?php
// Arquivo: bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // Garante que o arquivo api.php seja carregado
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ESTA LINHA ABAIXO É A SOLUÇÃO!
        // Ela configura o grupo de middlewares padrão para a API,
        // que usa o Sanctum em vez de sessões.
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->append(\ErlandMuchasaj\LaravelGzip\Middleware\GzipEncodeResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
