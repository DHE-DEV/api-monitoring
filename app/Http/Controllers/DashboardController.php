<?php
// app/Http/Controllers/DashboardController.php - Mit korrekten Spaltennamen

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Dashboard anzeigen
     */
    public function index()
    {
        $user = auth()->user();

        // Basis-Statistiken sammeln
        $stats = $this->getStatistics($user);

        // Kürzliche Monitore (falls ApiMonitor Model existiert)
        $recentMonitors = $this->getRecentMonitors($user);

        return view('dashboard.index', compact('stats', 'recentMonitors'));
    }

    /**
     * Dashboard-Daten als JSON (für AJAX-Updates)
     */
    public function data()
    {
        $user = auth()->user();

        $stats = $this->getStatistics($user);
        $recentMonitors = $this->getRecentMonitors($user);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_monitors' => $recentMonitors,
            'user' => [
                'name' => $user->name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'last_login_at' => $user->last_login_at?->format('d.m.Y H:i'),
            ],
            'system' => [
                'online' => true,
                'uptime' => '99.9%',
                'last_updated' => now()->format('d.m.Y H:i:s'),
            ]
        ]);
    }

    /**
     * Statistiken basierend auf Benutzer-Berechtigungen sammeln
     */
    private function getStatistics($user)
    {
        $stats = [];

        // Benutzer-Statistiken (nur wenn berechtigt)
        if ($user->can('view-users')) {
            $stats['users_count'] = User::count();
            $stats['active_users'] = User::where('is_active', true)->count();
        }

        // Monitor-Statistiken (nur wenn berechtigt)
        if ($user->can('view-monitors')) {
            $stats = array_merge($stats, $this->getMonitorStatistics($user));
        }

        return $stats;
    }

    /**
     * Monitor-spezifische Statistiken (mit korrekten Spaltennamen)
     */
    private function getMonitorStatistics($user)
    {
        $stats = [];

        // Prüfen ob ApiMonitor Model existiert
        if (class_exists('App\Models\ApiMonitor')) {
            try {
                $apiMonitorClass = 'App\Models\ApiMonitor';

                // Basis-Monitor Statistiken
                $stats['monitors_count'] = $apiMonitorClass::count();
                $stats['active_monitors'] = $apiMonitorClass::where('is_active', true)->count();

                // Erfolgsraten (falls Ergebnisse vorhanden)
                if (class_exists('App\Models\ApiMonitorResult')) {
                    $stats = array_merge($stats, $this->getResultStatistics());
                } else {
                    // Fallback-Werte wenn Model fehlt
                    $stats['success_rate_24h'] = 0;
                    $stats['avg_response_time'] = 0;
                    $stats['errors_24h'] = 0;
                }

                // Monitor nach Methoden
                $stats['monitors_by_method'] = $apiMonitorClass::select('method', DB::raw('count(*) as count'))
                    ->groupBy('method')
                    ->pluck('count', 'method')
                    ->toArray();

            } catch (\Exception $e) {
                // Fehler beim Zugriff auf Monitor-Daten
                \Log::warning('Dashboard: Fehler beim Laden der Monitor-Statistiken: ' . $e->getMessage());
                $stats['monitors_count'] = 0;
                $stats['active_monitors'] = 0;
                $stats['success_rate_24h'] = 0;
                $stats['avg_response_time'] = 0;
                $stats['errors_24h'] = 0;
            }
        } else {
            // Fallback wenn ApiMonitor nicht existiert
            $stats['monitors_count'] = 0;
            $stats['active_monitors'] = 0;
            $stats['success_rate_24h'] = 0;
            $stats['avg_response_time'] = 0;
            $stats['errors_24h'] = 0;
        }

        return $stats;
    }

    /**
     * Ergebnis-Statistiken (mit korrekten Spaltennamen)
     */
    private function getResultStatistics()
    {
        try {
            $resultClass = 'App\Models\ApiMonitorResult';

            // Erfolgsrate der letzten 24 Stunden
            // Verwendung der korrekten Spalte "success" statt "is_successful"
            $totalTests = $resultClass::where('created_at', '>=', now()->subDay())->count();
            $successfulTests = $resultClass::where('created_at', '>=', now()->subDay())
                ->where('success', true)->count();

            $successRate = $totalTests > 0 ? round(($successfulTests / $totalTests) * 100, 1) : 0;

            // Durchschnittliche Antwortzeit (nur erfolgreiche Tests)
            // Verwendung der korrekten Spalte "response_time_ms" statt "response_time"
            $avgResponseTime = $resultClass::where('created_at', '>=', now()->subDay())
                ->where('success', true)
                ->avg('response_time_ms') ?? 0;

            // Fehler der letzten 24 Stunden
            $errors24h = $resultClass::where('created_at', '>=', now()->subDay())
                ->where('success', false)->count();

            return [
                'success_rate_24h' => $successRate,
                'avg_response_time' => round($avgResponseTime),
                'errors_24h' => $errors24h
            ];

        } catch (\Exception $e) {
            \Log::warning('Dashboard: Fehler beim Laden der Ergebnis-Statistiken: ' . $e->getMessage());
            return [
                'success_rate_24h' => 0,
                'avg_response_time' => 0,
                'errors_24h' => 0
            ];
        }
    }

    /**
     * Kürzliche Monitore abrufen (sicher)
     */
    private function getRecentMonitors($user)
    {
        if (!$user->can('view-monitors') || !class_exists('App\Models\ApiMonitor')) {
            return collect([]);
        }

        try {
            $apiMonitorClass = 'App\Models\ApiMonitor';

            return $apiMonitorClass::select('id', 'name', 'url', 'method', 'is_active', 'created_at')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($monitor) {
                    return [
                        'id' => $monitor->id,
                        'name' => $monitor->name,
                        'url' => $monitor->url,
                        'method' => $monitor->method,
                        'is_active' => $monitor->is_active,
                        'created_at' => $monitor->created_at->format('d.m.Y H:i'),
                        'created_ago' => $monitor->created_at->diffForHumans(),
                    ];
                });
        } catch (\Exception $e) {
            \Log::warning('Dashboard: Fehler beim Laden der kürzlichen Monitore: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * System-weite Statistiken für Super Admin
     */
    public function systemStats()
    {
        $user = auth()->user();

        if (!$user->hasRole('Super Admin')) {
            abort(403, 'Keine Berechtigung für System-Statistiken');
        }

        $stats = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => $this->getDatabaseSize(),
            'memory_usage' => $this->getMemoryUsage(),
            'uptime' => $this->getSystemUptime(),
        ];

        return response()->json([
            'success' => true,
            'system_stats' => $stats
        ]);
    }

    /**
     * Datenbank-Größe ermitteln
     */
    private function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            $result = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$databaseName]);

            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Speicher-Nutzung ermitteln
     */
    private function getMemoryUsage()
    {
        return [
            'current_mb' => round(memory_get_usage() / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2),
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * System-Uptime ermitteln (vereinfacht)
     */
    private function getSystemUptime()
    {
        return "Online";
    }

    /**
     * Quick Actions - Häufig verwendete Aktionen
     */
    public function quickActions()
    {
        $user = auth()->user();

        $actions = [];

        if ($user->can('create-monitors')) {
            $actions[] = [
                'name' => 'Neuer Monitor',
                'description' => 'API-Monitor erstellen',
                'url' => route('api-monitor.create'),
                'icon' => 'plus',
                'color' => 'indigo'
            ];
        }

        if ($user->can('view-results')) {
            $actions[] = [
                'name' => 'Ergebnisse anzeigen',
                'description' => 'Monitor-Ergebnisse einsehen',
                'url' => route('api-monitor.index'),
                'icon' => 'chart-bar',
                'color' => 'green'
            ];
        }

        if ($user->can('create-users')) {
            $actions[] = [
                'name' => 'Neuer Benutzer',
                'description' => 'Benutzer hinzufügen',
                'url' => route('users.index'),
                'icon' => 'user-plus',
                'color' => 'blue'
            ];
        }

        return response()->json([
            'success' => true,
            'actions' => $actions
        ]);
    }
}
