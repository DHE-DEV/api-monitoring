<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum Middleware für API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Permission Middleware Aliases
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // API Middleware Group
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Inertia-Zeile entfernt - wir verwenden Alpine.js + Blade
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
