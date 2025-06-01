<?php
// bootstrap/app.php - Mit Rate Limiter für Laravel 12

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Rate Limiter definieren
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('web', function (Request $request) {
                return Limit::perMinute(1000)->by($request->ip());
            });
        }
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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
