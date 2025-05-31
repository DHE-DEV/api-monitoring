<?php
// app/Http/Middleware/CheckMonitorAccess.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckMonitorAccess
{
    public function handle(Request $request, Closure $next)
    {
        $monitorId = $request->route('apiMonitor')?->id ?? $request->route('monitor-id');

        if ($monitorId && !Gate::allows('access-monitor', $monitorId)) {
            abort(403, 'Keine Berechtigung für diesen Monitor');
        }

        return $next($request);
    }
}

// app/Http/Kernel.php - Route Middleware hinzufügen
protected $middlewareAliases = [
    // ...
    'monitor.access' => \App\Http\Middleware\CheckMonitorAccess::class,
];
