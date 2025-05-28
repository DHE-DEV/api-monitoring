<?php
namespace App\Mail;

use App\Models\ApiMonitor;
use App\Models\ApiMonitorResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiMonitorAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ApiMonitor $monitor,
        public ApiMonitorResult $result,
        public string $alertType
    ) {}

    public function build()
    {
        $subject = match($this->alertType) {
            'slow_response' => "⚠️ Langsame API-Antwort: {$this->monitor->name}",
            'http_error' => "🚨 HTTP-Fehler: {$this->monitor->name}",
            'api_down' => "🔴 API nicht erreichbar: {$this->monitor->name}",
            default => "⚠️ API-Monitor Alert: {$this->monitor->name}"
        };

        return $this->subject($subject)
            ->view('emails.api-monitor-alert')
            ->with([
                'monitor' => $this->monitor,
                'result' => $this->result,
                'alertType' => $this->alertType,
            ]);
    }
}
