<?php
// resources/views/api-monitor/show.blade.php
?>
@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Monitor Info Card -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ $apiMonitor->name }}</h2>
                <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs rounded-full {{ $apiMonitor->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $apiMonitor->is_active ? 'Aktiv' : 'Inaktiv' }}
                </span>
                    <button onclick="testMonitor({{ $apiMonitor->id }})"
                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        Jetzt testen
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">URL</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $apiMonitor->url }}</p>
                        <p class="text-xs text-gray-500">{{ $apiMonitor->method }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Intervall</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $apiMonitor->interval_minutes }} Minuten</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Letzter Status</h3>
                        @if($apiMonitor->latestResult)
                            <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $apiMonitor->latestResult->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $apiMonitor->latestResult->success ? 'Erfolgreich' : 'Fehler' }}
                            </span>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $apiMonitor->latestResult->response_time_ms }}ms
                                    @if($apiMonitor->latestResult->status_code)
                                        ({{ $apiMonitor->latestResult->status_code }})
                                    @endif
                                </p>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Noch nicht getestet</p>
                        @endif
                    </div>
                </div>

                @if($apiMonitor->headers)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700">Headers</h3>
                        <pre class="mt-1 text-xs bg-gray-50 p-3 rounded">{{ json_encode($apiMonitor->headers, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                @if($apiMonitor->payload)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700">Payload</h3>
                        <pre class="mt-1 text-xs bg-gray-50 p-3 rounded">{{ json_encode($apiMonitor->payload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Cards -->
        @if($results->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Statistics Card 24h -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistiken (letzte 24h)</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            @php
                                $last24h = $results->where('executed_at', '>=', now()->subDay());
                                $successRate24h = $last24h->count() > 0 ? ($last24h->where('success', true)->count() / $last24h->count()) * 100 : 0;
                                $avgResponseTime24h = $last24h->where('success', true)->avg('response_time_ms');
                                $maxResponseTime24h = $last24h->where('success', true)->max('response_time_ms');
                            @endphp

                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Erfolgsrate</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($successRate24h, 1) }}%</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Ø Antwortzeit</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ $avgResponseTime24h ? number_format($avgResponseTime24h, 0) . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Min/Max Antwortzeit</h4>
                                <p class="mt-1 text-xl font-semibold text-gray-900">
                                    {{ $last24h->where('success', true)->min('response_time_ms') ?: '-' }}ms /
                                    {{ $maxResponseTime24h ? $maxResponseTime24h . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Anfragen (24h)</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $last24h->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card 30 Tage -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistiken (letzter Monat)</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            @php
                                $lastMonth = $results->where('executed_at', '>=', now()->subDays(30));
                                $successRateMonth = $lastMonth->count() > 0 ? ($lastMonth->where('success', true)->count() / $lastMonth->count()) * 100 : 0;
                                $avgResponseTimeMonth = $lastMonth->where('success', true)->avg('response_time_ms');
                                $maxResponseTimeMonth = $lastMonth->where('success', true)->max('response_time_ms');
                                $minResponseTimeMonth = $lastMonth->where('success', true)->min('response_time_ms');
                                $totalDowntime = $lastMonth->where('success', false)->count() * $apiMonitor->interval_minutes;
                            @endphp

                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Erfolgsrate</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($successRateMonth, 1) }}%</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Ø Antwortzeit</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ $avgResponseTimeMonth ? number_format($avgResponseTimeMonth, 0) . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Min/Max Antwortzeit</h4>
                                <p class="mt-1 text-xl font-semibold text-gray-900">
                                    {{ $minResponseTimeMonth ? $minResponseTimeMonth . 'ms' : '-' }} /
                                    {{ $maxResponseTimeMonth ? $maxResponseTimeMonth . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Anfragen (30 Tage)</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $lastMonth->count() }}</p>
                                @if($totalDowntime > 0)
                                    <p class="text-xs text-red-600 mt-1">{{ $totalDowntime }} Min Ausfallzeit</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Results Table with Filters -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Ergebnisse ({{ $results->total() }} gesamt)</h3>
                    <a href="{{ route('api-monitor.export', array_merge(['apiMonitor' => $apiMonitor->id], request()->query())) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                        📊 Excel Export
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Zeitraum Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Zeitraum</label>
                        <select name="time_filter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>Alle</option>
                            <option value="today" {{ request('time_filter') == 'today' ? 'selected' : '' }}>Heute</option>
                            <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>Diese Woche</option>
                            <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>Dieser Monat</option>
                            <option value="year" {{ request('time_filter') == 'year' ? 'selected' : '' }}>Dieses Jahr</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status_filter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="" {{ request('status_filter') == '' ? 'selected' : '' }}>Alle</option>
                            <option value="errors" {{ request('status_filter') == 'errors' ? 'selected' : '' }}>Nur Fehler</option>
                        </select>
                    </div>

                    <!-- HTTP Code Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HTTP Code</label>
                        <select name="http_code_filter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Alle</option>
                            @foreach($availableHttpCodes as $code)
                                <option value="{{ $code }}" {{ request('http_code_filter') == $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                            Filter anwenden
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'executed_at', 'direction' => request('sort') == 'executed_at' && request('direction') == 'desc' ? 'asc' : 'desc']) }}"
                               class="flex items-center hover:text-gray-700">
                                Zeitpunkt
                                @if(request('sort') == 'executed_at')
                                    <span class="ml-1">{{ request('direction') == 'desc' ? '↓' : '↑' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'success', 'direction' => request('sort') == 'success' && request('direction') == 'desc' ? 'asc' : 'desc']) }}"
                               class="flex items-center hover:text-gray-700">
                                Status
                                @if(request('sort') == 'success')
                                    <span class="ml-1">{{ request('direction') == 'desc' ? '↓' : '↑' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'response_time_ms', 'direction' => request('sort') == 'response_time_ms' && request('direction') == 'desc' ? 'asc' : 'desc']) }}"
                               class="flex items-center hover:text-gray-700">
                                Antwortzeit
                                @if(request('sort') == 'response_time_ms')
                                    <span class="ml-1">{{ request('direction') == 'desc' ? '↓' : '↑' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status_code', 'direction' => request('sort') == 'status_code' && request('direction') == 'desc' ? 'asc' : 'desc']) }}"
                               class="flex items-center hover:text-gray-700">
                                HTTP Code
                                @if(request('sort') == 'status_code')
                                    <span class="ml-1">{{ request('direction') == 'desc' ? '↓' : '↑' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fehler</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($results as $result)
                        <tr class="{{ $result->success ? '' : 'bg-red-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $result->executed_at->format('d.m.Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $result->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $result->success ? 'OK' : 'Fehler' }}
                            </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-medium text-gray-900">{{ $result->response_time_ms }}ms</span>
                                @if($result->response_time_ms > 2000)
                                    <span class="text-red-500 text-xs ml-1">Langsam</span>
                                @elseif($result->response_time_ms > 1000)
                                    <span class="text-yellow-500 text-xs ml-1">Mittel</span>
                                @else
                                    <span class="text-green-500 text-xs ml-1">Schnell</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($result->status_code)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $result->status_code >= 200 && $result->status_code < 300 ? 'bg-green-100 text-green-800' :
                                       ($result->status_code >= 400 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $result->status_code }}
                                </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                @if($result->error_message)
                                    <span class="truncate block" title="{{ $result->error_message }}">
                                    {{ Str::limit($result->error_message, 50) }}
                                </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Keine Ergebnisse für die gewählten Filter gefunden.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($results->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $results->appends(request()->query())->links() }}
                </div>
            @endif
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
    </script>
@endsection
