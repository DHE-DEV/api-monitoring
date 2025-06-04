<?php

namespace App\Console\Commands;

use App\Models\ApiMonitor;
use App\Services\ApiMonitorService;
use Illuminate\Console\Command;

class RunApiMonitors extends Command
{
    protected $signature = 'api:monitor {--monitor-id= : Specific monitor ID to run} {--daemon : Run continuously} {--interval=60 : Daemon check interval in seconds}';
    protected $description = 'Run API monitors based on their configured intervals';

    public function __construct(private ApiMonitorService $apiMonitorService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Einzelner Monitor
        if ($monitorId = $this->option('monitor-id')) {
            $monitor = ApiMonitor::findOrFail($monitorId);
            $this->info("Running monitor: {$monitor->name}");
            $this->apiMonitorService->executeMonitor($monitor);
            return Command::SUCCESS;
        }

        // Daemon-Modus für kontinuierliches Monitoring
        if ($this->option('daemon')) {
            return $this->runDaemon();
        }

        // Einmalige Ausführung
        return $this->runOnce();
    }

    private function runDaemon(): int
    {
        $interval = (int) $this->option('interval');

        $this->info("Starting API monitoring daemon (checking every {$interval} seconds)");
        $this->info("Press Ctrl+C to stop");

        while (true) {
            try {
                $executed = $this->runOnce(false); // false = no completion message

                if ($executed > 0) {
                    $this->line("[" . now()->format('H:i:s') . "] Executed {$executed} monitors");
                }

                sleep($interval);
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                sleep($interval);
            }
        }

        return Command::SUCCESS;
    }

    private function runOnce(bool $showCompletion = true): int
    {
        $monitors = ApiMonitor::where('is_active', true)->get();
        $executedCount = 0;

        foreach ($monitors as $monitor) {
            // Prüfen ob genug Zeit seit dem letzten Aufruf vergangen ist
            $lastResult = $monitor->latestResult;

            if (!$lastResult ||
                $lastResult->executed_at->addMinutes($monitor->interval_minutes)->isPast()) {

                $this->info("Running monitor: {$monitor->name}");
                $this->apiMonitorService->executeMonitor($monitor);
                $executedCount++;
            } else {
                $this->line("Skipping monitor: {$monitor->name} (next run: " .
                    $lastResult->executed_at->addMinutes($monitor->interval_minutes)->format('H:i:s') . ")");
            }
        }

        if ($showCompletion) {
            $this->info('API monitoring completed');
        }

        return $executedCount;
    }
}
