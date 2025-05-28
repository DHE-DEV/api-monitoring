{{-- Erstellen Sie diese Datei: resources/views/emails/api-monitor-alert.blade.php --}}
    <!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Monitor Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .alert-box {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-slow { background-color: #fff3cd; border-left: 5px solid #ffc107; }
        .alert-error { background-color: #f8d7da; border-left: 5px solid #dc3545; }
        .alert-down { background-color: #f5c6cb; border-left: 5px solid #721c24; }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 5px;
        }
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background-color: #f1f3f4;
            font-weight: bold;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .emoji {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    @if($alertType === 'slow_response')
        <span class="emoji">⚠️</span>
        <h1>Langsame API-Antwort erkannt</h1>
    @elseif($alertType === 'http_error')
        <span class="emoji">🚨</span>
        <h1>HTTP-Fehler erkannt</h1>
    @elseif($alertType === 'api_down')
        <span class="emoji">🔴</span>
        <h1>API nicht erreichbar</h1>
    @endif
    <p>{{ $monitor->name }}</p>
</div>

<div class="content">
    @if($alertType === 'slow_response')
        <div class="alert-box alert-slow">
            <strong>⚠️ Langsame Antwortzeit!</strong><br>
            Die API-Antwort war mit {{ $result->response_time_ms }}ms langsamer als der konfigurierte Schwellenwert von {{ config('app.api_slow_response_threshold', 3000) }}ms.
        </div>
    @elseif($alertType === 'http_error')
        <div class="alert-box alert-error">
            <strong>🚨 HTTP-Fehler!</strong><br>
            Die API gab einen HTTP-Status-Code {{ $result->status_code }} zurück (erwartet: 200).
        </div>
    @elseif($alertType === 'api_down')
        <div class="alert-box alert-down">
            <strong>🔴 API nicht erreichbar!</strong><br>
            Die API konnte nicht erreicht werden. Bitte prüfen Sie die Verfügbarkeit.
        </div>
    @endif

    <h2>📊 Monitoring-Details</h2>
    <table class="details-table">
        <tr>
            <th>Monitor Name</th>
            <td>{{ $monitor->name }}</td>
        </tr>
        <tr>
            <th>URL</th>
            <td><a href="{{ $monitor->url }}" target="_blank">{{ $monitor->url }}</a></td>
        </tr>
        <tr>
            <th>HTTP-Methode</th>
            <td>{{ $monitor->method }}</td>
        </tr>
        <tr>
            <th>Zeitpunkt</th>
            <td>{{ $result->executed_at ? $result->executed_at->format('d.m.Y H:i:s') : now()->format('d.m.Y H:i:s') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                    <span class="status-badge {{ $result->success ? 'status-success' : 'status-error' }}">
                        {{ $result->success ? 'Erfolgreich' : 'Fehler' }}
                    </span>
            </td>
        </tr>
        <tr>
            <th>Antwortzeit</th>
            <td>
                <strong>{{ $result->response_time_ms }}ms</strong>
                @if($result->response_time_ms > 5000)
                    <span style="color: #dc3545;"> (Sehr langsam)</span>
                @elseif($result->response_time_ms > 3000)
                    <span style="color: #ffc107;"> (Langsam)</span>
                @endif
            </td>
        </tr>
        @if($result->status_code)
            <tr>
                <th>HTTP Status-Code</th>
                <td>
                    <strong>{{ $result->status_code }}</strong>
                    @if($result->status_code >= 500)
                        <span style="color: #dc3545;"> (Server-Fehler)</span>
                    @elseif($result->status_code >= 400)
                        <span style="color: #ffc107;"> (Client-Fehler)</span>
                    @elseif($result->status_code >= 300)
                        <span style="color: #17a2b8;"> (Weiterleitung)</span>
                    @elseif($result->status_code >= 200)
                        <span style="color: #28a745;"> (Erfolgreich)</span>
                    @endif
                </td>
            </tr>
        @endif
        @if($result->error_message)
            <tr>
                <th>Fehlermeldung</th>
                <td style="color: #dc3545; font-family: monospace;">{{ $result->error_message }}</td>
            </tr>
        @endif
    </table>

    @if($monitor->interval_minutes)
        <p><strong>ℹ️ Überwachungsintervall:</strong> Alle {{ $monitor->interval_minutes }} Minuten</p>
    @endif

    <h2>🔧 Empfohlene Aktionen</h2>
    <ul>
        @if($alertType === 'slow_response')
            <li>Prüfen Sie die Server-Performance und Datenbankabfragen</li>
            <li>Überprüfen Sie die Netzwerkverbindung</li>
            <li>Analysieren Sie die API-Logs auf Bottlenecks</li>
        @elseif($alertType === 'http_error')
            <li>Überprüfen Sie die API-Konfiguration</li>
            <li>Prüfen Sie die Server-Logs auf Fehler</li>
            <li>Validieren Sie die Request-Parameter</li>
        @elseif($alertType === 'api_down')
            <li>Überprüfen Sie die Server-Erreichbarkeit</li>
            <li>Prüfen Sie DNS-Auflösung und Netzwerk</li>
            <li>Kontaktieren Sie den API-Anbieter</li>
        @endif
        <li>Überwachen Sie die API kontinuierlich auf weitere Probleme</li>
    </ul>
</div>

<div class="footer">
    <p>Diese Benachrichtigung wurde automatisch vom API-Monitor-System generiert.</p>
    <p>Monitor-ID: {{ $monitor->id }} | Zeitstempel: {{ now()->format('d.m.Y H:i:s') }}</p>
</div>
</body>
</html>
