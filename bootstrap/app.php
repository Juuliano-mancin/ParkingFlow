<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // ← Certifique-se que esta linha existe
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Adicione o middleware CORS globalmente
        $middleware->api(prepend: [
            HandleCors::class,
        ]);
        
        // Ou se quiser adicionar para todas as rotas (web e api):
        $middleware->web(prepend: [
            HandleCors::class,
        ]);
        
        $middleware->alias([
            'cors' => HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();