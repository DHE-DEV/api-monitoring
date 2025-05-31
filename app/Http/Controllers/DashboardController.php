<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\ApiMonitor;
use App\Models\ApiMonitorResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ENTFERNEN: public function __construct() mit middleware()

    public function index()
    {
        $stats = [
            'total_monitors' => ApiMonitor::count(),
            'active_monitors' => ApiMonitor::where('is_active', true)->count(),
            'success_rate_24h' => $this->getSuccessRate24h(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'error_count_1h' => $this->getErrorCountLastHour(),
        ];

        $recentResults = ApiMonitorResult::with('apiMonitor')
            ->orderBy('executed_at', 'desc')
            ->limit(10)
            ->get();

        $monitors = ApiMonitor::with('latestResult')
            ->where('is_active', true)
            ->get();

        return view('dashboard.index', compact('stats', 'recentResults', 'monitors'));
    }

    private function getSuccessRate24h()
    {
        $total = ApiMonitorResult::where('executed_at', '>=', now()->subDay())->count();
        $successful = ApiMonitorResult::where('executed_at', '>=', now()->subDay())
            ->where('success', true)->count();

        return $total > 0 ? round(($successful / $total) * 100, 1) : 100;
    }

    private function getAverageResponseTime()
    {
        return round(ApiMonitorResult::where('executed_at', '>=', now()->subHour())
            ->where('success', true)
            ->avg('response_time_ms') ?? 0);
    }

    private function getErrorCountLastHour()
    {
        return ApiMonitorResult::where('executed_at', '>=', now()->subHour())
            ->where('success', false)
            ->count();
    }
}
