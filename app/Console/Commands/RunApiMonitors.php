<?php

namespace App\Console\Commands;

use App\Models\ApiMonitor;
use App\Services\ApiMonitorService;
use Illuminate\Console\Command;

class RunApiMonitors extends Command
{
    protected $signature = 'api:monitor {--monitor-id= : Specific monitor ID to run}';
    protected $description = 'Run API monitors based on their configured intervals';

    public function __construct(private ApiMonitorService $apiMonitorService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($monitorId = $this->option('monitor-id')) {
            $monitor = ApiMonitor::findOrFail($monitorId);
            $this->info("Running monitor: {$monitor->name}");
            $this->apiMonitorService->executeMonitor($monitor);
            return;
        }

        $monitors = ApiMonitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            // Prüfen ob genug Zeit seit dem letzten Aufruf vergangen ist
            $lastResult = $monitor->latestResult;

            if (!$lastResult ||
                $lastResult->executed_at->addMinutes($monitor->interval_minutes)->isPast()) {

                $this->info("Running monitor: {$monitor->name}");
                $this->apiMonitorService->executeMonitor($monitor);
            } else {
                $this->info("Skipping monitor: {$monitor->name} (not due yet)");
            }
        }

        $this->info('API monitoring completed');
    }
}

// Registrierung in app/Console/Kernel.php
// In der schedule() Methode hinzufügen:
// $schedule->command('api:monitor')->everyMinute();
