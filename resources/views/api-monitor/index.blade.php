{{-- resources/views/api-monitor/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">API Monitore</h1>
                        <p class="text-gray-600">Verwalten Sie Ihre API-Überwachungen</p>
                    </div>

                    @can('create-monitors')
                        <div class="flex space-x-3">
                            <!-- Column Settings Button -->
                            <button onclick="openColumnSettings()"
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                Spalten
                            </button>

                            <!-- New Monitor Button -->
                            <a href="{{ route('api-monitor.create') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Neuer Monitor
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-lg shadow-sm border mb-6">
                <div class="p-4">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <input type="text"
                                   name="search"
                                   placeholder="Suche nach Name, URL..."
                                   value="{{ request('search') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Alle Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Nur Aktive</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inaktive</option>
                            </select>
                        </div>

                        <!-- Group Filter -->
                        <div>
                            <select name="group" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Alle Gruppen</option>
                                <!-- Groups werden später dynamisch geladen -->
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <div>
                            <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                                🔍 Filtern
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Monitors Table -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="name">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="name" checked>
                                    Name
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="url">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="url" checked>
                                    URL & Methode
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="interval">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="interval" checked>
                                    Intervall
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="status">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="status" checked>
                                    Status
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="alerts">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="alerts" checked>
                                    E-Mail Alerts
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="response-time">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="response-time" checked>
                                    Antwortzeit
                                </div>
                            </th>
                            <th class="column-toggle px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="groups">
                                <div class="flex items-center">
                                    <input type="checkbox" class="column-checkbox mr-2" data-column="groups">
                                    Gruppen
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monitors as $monitor)
                            <tr class="hover:bg-gray-50">
                                <!-- Name -->
                                <td class="column-data px-6 py-4" data-column="name">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3 {{ $monitor->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $monitor->name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $monitor->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- URL & Method -->
                                <td class="column-data px-6 py-4" data-column="url">
                                    <div>
                                        <div class="text-sm text-gray-900 font-mono">
                                            {{ Str::limit(parse_url($monitor->url, PHP_URL_HOST) . parse_url($monitor->url, PHP_URL_PATH), 40) }}
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                {{ $monitor->method === 'GET' ? 'bg-green-100 text-green-800' :
                                                   ($monitor->method === 'POST' ? 'bg-blue-100 text-blue-800' :
                                                   ($monitor->method === 'PUT' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ $monitor->method }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Interval -->
                                <td class="column-data px-6 py-4 text-sm text-gray-900" data-column="interval">
                                    {{ $monitor->interval_minutes }} min
                                </td>

                                <!-- Status -->
                                <td class="column-data px-6 py-4" data-column="status">
                                    @if($monitor->latestResult)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $monitor->latestResult->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $monitor->latestResult->success ? 'OK' : 'Fehler' }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Nicht getestet
                                        </span>
                                    @endif
                                </td>

                                <!-- Email Alerts -->
                                <td class="column-data px-6 py-4" data-column="alerts">
                                    <div class="flex items-center">
                                        @if($monitor->email_alerts_enabled)
                                            <button onclick="quickToggleEmailAlerts({{ $monitor->id }}, false)"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200 transition duration-200 cursor-pointer">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                </svg>
                                                Aktiv
                                            </button>
                                        @else
                                            <button onclick="quickToggleEmailAlerts({{ $monitor->id }}, true)"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 hover:bg-red-200 transition duration-200 cursor-pointer">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                    <path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14z"></path>
                                                </svg>
                                                Deaktiviert
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- Response Time -->
                                <td class="column-data px-6 py-4 text-sm text-gray-900" data-column="response-time">
                                    @if($monitor->latestResult)
                                        <div>
                                            <span class="font-medium">{{ $monitor->latestResult->response_time_ms }}ms</span>
                                            @if($monitor->latestResult->status_code)
                                                <div class="text-xs text-gray-500">({{ $monitor->latestResult->status_code }})</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Groups -->
                                <td class="column-data px-6 py-4" data-column="groups" style="display: none;">
                                    <div class="flex flex-wrap gap-1">
                                        <!-- Groups werden später implementiert -->
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Admin
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('api-monitor.show', $monitor) }}"
                                           class="text-blue-600 hover:text-blue-900" title="Details anzeigen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        @can('edit-monitors')
                                            <a href="{{ route('api-monitor.edit', $monitor) }}"
                                               class="text-indigo-600 hover:text-indigo-900" title="Bearbeiten">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endcan

                                        <button onclick="testMonitor({{ $monitor->id }})"
                                                class="text-green-600 hover:text-green-900" title="Jetzt testen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-9a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Monitore gefunden</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ request()->hasAny(['search', 'status', 'group']) ? 'Keine Monitore entsprechen den Filterkriterien.' : 'Erstellen Sie Ihren ersten API-Monitor.' }}
                                    </p>
                                    @can('create-monitors')
                                        @if(!request()->hasAny(['search', 'status', 'group']))
                                            <div class="mt-6">
                                                <a href="{{ route('api-monitor.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Neuer Monitor
                                                </a>
                                            </div>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($monitors->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $monitors->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Column Settings Modal -->
    <div id="columnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Spalten-Einstellungen</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-name" checked class="mr-2">
                        <span>Name</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-url" checked class="mr-2">
                        <span>URL & Methode</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-interval" checked class="mr-2">
                        <span>Intervall</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-status" checked class="mr-2">
                        <span>Status</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-alerts" checked class="mr-2">
                        <span>E-Mail Alerts</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-response-time" checked class="mr-2">
                        <span>Antwortzeit</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="toggle-groups" class="mr-2">
                        <span>Gruppen</span>
                    </label>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeColumnSettings()" class="bg-gray-500 text-white px-4 py-2 rounded">
                        Abbrechen
                    </button>
                    <button onclick="saveColumnSettings()" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Speichern
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Column Management
        function openColumnSettings() {
            document.getElementById('columnModal').classList.remove('hidden');

            // Load current settings
            const settings = JSON.parse(localStorage.getItem('monitorColumnSettings') || '{}');

            ['name', 'url', 'interval', 'status', 'alerts', 'response-time', 'groups'].forEach(column => {
                const checkbox = document.getElementById(`toggle-${column}`);
                checkbox.checked = settings[column] !== false;
            });
        }

        function closeColumnSettings() {
            document.getElementById('columnModal').classList.add('hidden');
        }

        function saveColumnSettings() {
            const settings = {};

            ['name', 'url', 'interval', 'status', 'alerts', 'response-time', 'groups'].forEach(column => {
                const checkbox = document.getElementById(`toggle-${column}`);
                settings[column] = checkbox.checked;

                // Toggle column visibility
                const elements = document.querySelectorAll(`[data-column="${column}"]`);
                elements.forEach(el => {
                    el.style.display = checkbox.checked ? '' : 'none';
                });
            });

            localStorage.setItem('monitorColumnSettings', JSON.stringify(settings));
            closeColumnSettings();
        }

        // Load column settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            const settings = JSON.parse(localStorage.getItem('monitorColumnSettings') || '{}');

            Object.keys(settings).forEach(column => {
                if (settings[column] === false) {
                    const elements = document.querySelectorAll(`[data-column="${column}"]`);
                    elements.forEach(el => {
                        el.style.display = 'none';
                    });
                }
            });
        });

        // Test Monitor Function
        async function testMonitor(monitorId) {
            try {
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
                button.disabled = true;

                const response = await fetch(`/api-monitor/${monitorId}/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(`✅ Test erfolgreich!\n\nAntwortzeit: ${result.response_time_ms}ms\nStatus Code: ${result.status_code}`);
                } else {
                    alert(`❌ Test fehlgeschlagen!\n\nFehler: ${result.error_message}\nAntwortzeit: ${result.response_time_ms}ms`);
                }

                setTimeout(() => location.reload(), 1000);

            } catch (error) {
                console.error('Test error:', error);
                alert(`❌ Fehler beim Testen: ${error.message}`);
            } finally {
                const button = event.target.closest('button');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        }

        // Quick Toggle Email Alerts
        async function quickToggleEmailAlerts(monitorId, enable) {
            const action = enable ? 'enable' : 'disable';
            const actionText = enable ? 'aktivieren' : 'deaktivieren';

            let reason = '';
            if (!enable) {
                reason = prompt(`E-Mail-Alerts ${actionText}?\n\nGrund (optional):`);
                if (reason === null) return;
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
                    const icon = enable ? '✅' : '🚫';
                    alert(`${icon} E-Mail-Alerts erfolgreich ${actionText}!`);
                    location.reload();
                } else {
                    alert('❌ Fehler beim Aktualisieren der E-Mail-Einstellungen');
                }

            } catch (error) {
                alert('❌ Fehler: ' + error.message);
            }
        }

        // Modal close on outside click
        document.getElementById('columnModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeColumnSettings();
            }
        });
    </script>
@endsection
