<?php
// app/Services/ApiMonitorService.php
namespace App\Services;

use App\Models\ApiMonitor;
use App\Models\ApiMonitorResult;
use App\Mail\ApiMonitorAlert;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApiMonitorService
{
    public function executeMonitor(ApiMonitor $monitor)
    {
        $startTime = microtime(true);

        try {
            $headers = $monitor->headers ?? [];

            // Bearer Token aus .env hinzufügen
            $bearerToken = config('app.api_bearer_token');
            if ($bearerToken) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->when($monitor->method === 'POST', function ($http) use ($monitor) {
                    return $http->post($monitor->url, $monitor->payload ?? []);
                })
                ->when($monitor->method === 'PUT', function ($http) use ($monitor) {
                    return $http->put($monitor->url, $monitor->payload ?? []);
                })
                ->when($monitor->method === 'DELETE', function ($http) use ($monitor) {
                    return $http->delete($monitor->url);
                })
                ->get($monitor->url);

            $responseTime = (microtime(true) - $startTime) * 1000;

            $result = ApiMonitorResult::create([
                'api_monitor_id' => $monitor->id,
                'response_time_ms' => round($responseTime),
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => $response->json(),
                'executed_at' => now(),
            ]);

            // E-Mail-Benachrichtigungen prüfen
            $this->checkForAlerts($monitor, $result);

            Log::info("API Monitor '{$monitor->name}' executed successfully", [
                'response_time' => $result->response_time_ms,
                'status_code' => $result->status_code
            ]);

            return $result;

        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;

            $result = ApiMonitorResult::create([
                'api_monitor_id' => $monitor->id,
                'response_time_ms' => round($responseTime),
                'success' => false,
                'error_message' => $e->getMessage(),
                'executed_at' => now(),
            ]);

            // E-Mail für API-Ausfall senden
            $this->checkForAlerts($monitor, $result);

            Log::error("API Monitor '{$monitor->name}' failed", [
                'error' => $e->getMessage(),
                'response_time' => $result->response_time_ms
            ]);

            return $result;
        }
    }

    private function checkForAlerts(ApiMonitor $monitor, ApiMonitorResult $result)
    {
        // Prüfen ob E-Mail-Alerts für diesen Monitor aktiviert sind
        if (!$monitor->email_alerts_enabled) {
            Log::debug("Email alerts disabled for monitor", [
                'monitor' => $monitor->name,
                'disabled_at' => $monitor->email_alerts_disabled_at,
                'disabled_by' => $monitor->email_alerts_disabled_by,
                'reason' => $monitor->email_alerts_disabled_reason
            ]);
            return;
        }

        $alertRecipients = config('app.api_alert_recipients');
        $slowResponseThreshold = config('app.api_slow_response_threshold', 3000);

        if (empty($alertRecipients)) {
            return;
        }

        $recipients = is_string($alertRecipients)
            ? explode(',', $alertRecipients)
            : $alertRecipients;

        $shouldSendAlert = false;
        $alertType = '';

        // Prüfung auf langsame Antwortzeit
        if ($result->success && $result->response_time_ms > $slowResponseThreshold) {
            $shouldSendAlert = true;
            $alertType = 'slow_response';
        }
        // Prüfung auf HTTP-Fehler (nicht 200)
        elseif ($result->status_code && $result->status_code !== 200) {
            $shouldSendAlert = true;
            $alertType = 'http_error';
        }
        // Prüfung auf kompletten API-Ausfall
        elseif (!$result->success) {
            $shouldSendAlert = true;
            $alertType = 'api_down';
        }

        if ($shouldSendAlert) {
            // Prüfen ob bereits kürzlich eine ähnliche Benachrichtigung gesendet wurde
            // (Rate Limiting um Spam zu vermeiden)
            if ($this->shouldSendAlert($monitor, $alertType)) {
                foreach ($recipients as $recipient) {
                    $recipient = trim($recipient);
                    if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($recipient)->send(new ApiMonitorAlert($monitor, $result, $alertType));

                            Log::info("Alert email sent", [
                                'monitor' => $monitor->name,
                                'recipient' => $recipient,
                                'alert_type' => $alertType,
                                'response_time' => $result->response_time_ms,
                                'status_code' => $result->status_code
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to send alert email", [
                                'monitor' => $monitor->name,
                                'recipient' => $recipient,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function shouldSendAlert(ApiMonitor $monitor, string $alertType): bool
    {
        // Verschiedene Rate Limits je Alert-Typ mit int-Casting
        $rateLimitMinutes = match($alertType) {
            'slow_response' => (int) config('app.api_slow_response_alert_interval', 30),
            'http_error' => (int) config('app.api_http_error_alert_interval', 15),
            'api_down' => (int) config('app.api_down_alert_interval', 5),
            default => 15
        };

        $cacheKey = "api_monitor_alert_{$monitor->id}_{$alertType}";

        if (cache()->has($cacheKey)) {
            Log::debug("Alert rate-limited", [
                'monitor' => $monitor->name,
                'alert_type' => $alertType,
                'cache_key' => $cacheKey
            ]);
            return false;
        }

        // Cache für spezifische Zeit setzen
        cache()->put($cacheKey, true, now()->addMinutes($rateLimitMinutes));

        Log::info("Alert rate limit set", [
            'monitor' => $monitor->name,
            'alert_type' => $alertType,
            'duration_minutes' => $rateLimitMinutes
        ]);

        return true;
    }

    public function executeAllActiveMonitors()
    {
        $monitors = ApiMonitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            $this->executeMonitor($monitor);
        }
    }
}
