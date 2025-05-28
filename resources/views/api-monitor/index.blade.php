{{-- resources/views/api-monitor/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">API Monitore</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervall</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail Alerts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letzte Antwortzeit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($monitors as $monitor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $monitor->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></div>
                                <div class="text-sm font-medium text-gray-900">{{ $monitor->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $monitor->url }}</div>
                            <div class="text-sm text-gray-500">{{ $monitor->method }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $monitor->interval_minutes }} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($monitor->latestResult)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $monitor->latestResult->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $monitor->latestResult->success ? 'OK' : 'Fehler' }}
                            </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Noch nicht getestet
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($monitor->email_alerts_enabled)
                                    <button onclick="quickToggleEmailAlerts({{ $monitor->id }}, false)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200 transition duration-200 cursor-pointer"
                                            title="Klicken zum Deaktivieren">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                        Aktiv
                                    </button>
                                @else
                                    <button onclick="quickToggleEmailAlerts({{ $monitor->id }}, true)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 hover:bg-red-200 transition duration-200 cursor-pointer"
                                            title="Klicken zum Aktivieren">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path fill-rule="evenodd" d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14z"></path>
                                        </svg>
                                        Deaktiviert
                                    </button>
                                @endif
                            </div>
                            @if(!$monitor->email_alerts_enabled && $monitor->email_alerts_disabled_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    Seit {{ $monitor->email_alerts_disabled_at->diffForHumans() }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($monitor->latestResult)
                                {{ $monitor->latestResult->response_time_ms }}ms
                                @if($monitor->latestResult->status_code)
                                    ({{ $monitor->latestResult->status_code }})
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('api-monitor.show', $monitor) }}" class="text-blue-600 hover:text-blue-900">Details</a>
                                <a href="{{ route('api-monitor.edit', $monitor) }}" class="text-indigo-600 hover:text-indigo-900">Bearbeiten</a>
                                <button onclick="testMonitor({{ $monitor->id }})" class="text-green-600 hover:text-green-900">Testen</button>
                                <form action="{{ route('api-monitor.destroy', $monitor) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Sind Sie sicher?')">Löschen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Keine API Monitore vorhanden.
                            <a href="{{ route('api-monitor.create') }}" class="text-blue-600 hover:text-blue-900">Erstellen Sie den ersten Monitor</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function testMonitor(monitorId) {
            try {
                const response = await fetch(`/api-monitor/${monitorId}/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(`Test erfolgreich!\nAntwortzeit: ${result.response_time_ms}ms\nStatus Code: ${result.status_code}`);
                } else {
                    alert(`Test fehlgeschlagen!\nFehler: ${result.error_message}\nAntwortzeit: ${result.response_time_ms}ms`);
                }

                // Seite neu laden um aktuellen Status zu zeigen
                location.reload();
            } catch (error) {
                alert('Fehler beim Testen: ' + error.message);
            }
        }

        async function quickToggleEmailAlerts(monitorId, enable) {
            const action = enable ? 'enable' : 'disable';
            const actionText = enable ? 'aktivieren' : 'deaktivieren';

            let reason = '';
            if (!enable) {
                reason = prompt(`E-Mail-Alerts ${actionText}?\n\nGrund (optional):`);
                if (reason === null) return; // User cancelled
            }

            try {
                const response = await fetch(`/api-monitor/${monitorId}/toggle-email-alerts`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: JSON.stringify({
                        action: action,
                        reason: reason || `Schnell ${action === 'enable' ? 'aktiviert' : 'deaktiviert'} über Dashboard`
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Erfolgs-Meldung mit Icon
                    const icon = enable ? '✅' : '🚫';
                    alert(`${icon} E-Mail-Alerts erfolgreich ${actionText}!`);

                    // Seite neu laden um Status zu aktualisieren
                    location.reload();
                } else {
                    alert('❌ Fehler beim Aktualisieren der E-Mail-Einstellungen');
                }

            } catch (error) {
                alert('❌ Fehler: ' + error.message);
            }
        }
    </script>
@endsection
