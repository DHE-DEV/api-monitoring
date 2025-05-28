{{-- resources/views/api-monitor/show.blade.php --}}
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
                        <h3 class="text-sm font-medium text-gray-700">E-Mail Alerts</h3>
                        @if($apiMonitor->email_alerts_enabled)
                            <div class="mt-1 flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                <span class="text-sm text-green-700 font-medium">Aktiv</span>
                            </div>
                        @else
                            <div class="mt-1 flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path fill-rule="evenodd" d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14z"></path>
                                </svg>
                                <span class="text-sm text-red-700 font-medium">Deaktiviert</span>
                            </div>
                            @if($apiMonitor->email_alerts_disabled_at)
                                <p class="text-xs text-gray-500 mt-1">
                                    Seit {{ $apiMonitor->email_alerts_disabled_at->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        @endif
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
                                    {{ $avgResponseTime24h ? number_format($avgResponseTime24h, 0, ',', '.') . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Min/Max Antwortzeit</h4>
                                <p class="mt-1 text-xl font-semibold text-gray-900">
                                    {{ $last24h->where('success', true)->min('response_time_ms') ? number_format($last24h->where('success', true)->min('response_time_ms'), 0, ',', '.') . 'ms' : '-' }} /
                                    {{ $maxResponseTime24h ? number_format($maxResponseTime24h, 0, ',', '.') . 'ms' : '-' }}
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
                                    {{ $avgResponseTimeMonth ? number_format($avgResponseTimeMonth, 0, ',', '.') . 'ms' : '-' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Min/Max Antwortzeit</h4>
                                <p class="mt-1 text-xl font-semibold text-gray-900">
                                    {{ $minResponseTimeMonth ? number_format($minResponseTimeMonth, 0, ',', '.') . 'ms' : '-' }} /
                                    {{ $maxResponseTimeMonth ? number_format($maxResponseTimeMonth, 0, ',', '.') . 'ms' : '-' }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Zeitraum</label>
                        <div class="relative">
                            <select name="time_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>Alle</option>
                                <option value="today" {{ request('time_filter') == 'today' ? 'selected' : '' }}>Heute</option>
                                <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>Diese Woche</option>
                                <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>Dieser Monat</option>
                                <option value="year" {{ request('time_filter') == 'year' ? 'selected' : '' }}>Dieses Jahr</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="relative">
                            <select name="status_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                <option value="" {{ request('status_filter') == '' ? 'selected' : '' }}>Alle</option>
                                <option value="errors" {{ request('status_filter') == 'errors' ? 'selected' : '' }}>Nur Fehler</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- HTTP Code Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">HTTP Code</label>
                        <div class="relative">
                            <select name="http_code_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                <option value="">Alle</option>
                                @foreach($availableHttpCodes as $code)
                                    <option value="{{ $code }}" {{ request('http_code_filter') == $code ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg shadow-sm transition duration-200 text-sm">
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
                        <tr class="cursor-pointer hover:bg-gray-50 {{ $result->success ? '' : 'bg-red-50' }}"
                            onclick="showResultDetails({{ $result->id }})">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $result->executed_at->setTimezone('Europe/Berlin')->format('d.m.Y H:i:s') }}
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
                                {{ Str::limit($result->error_message, 30) }}
                            </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Versteckte Daten für Modal -->
                        <script type="application/json" id="result-data-{{ $result->id }}">
                            {
                                "id": {{ $result->id }},
                        "executed_at": "{{ $result->executed_at->setTimezone('Europe/Berlin')->format('d.m.Y H:i:s') }}",
                        "success": {{ $result->success ? 'true' : 'false' }},
                        "response_time_ms": {{ $result->response_time_ms }},
                        "status_code": {{ $result->status_code ?? 'null' }},
                        "error_message": {!! json_encode($result->error_message) !!},
                        "response_body": {!! json_encode($result->response_body, JSON_PRETTY_PRINT) !!},
                        "monitor_name": {!! json_encode($apiMonitor->name) !!},
                        "monitor_url": {!! json_encode($apiMonitor->url) !!},
                        "monitor_method": {!! json_encode($apiMonitor->method) !!}
                            }
                        </script>
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

    <!-- Result Details Modal -->
    <div id="resultModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">API Request Details</h3>
                    <button onclick="closeResultModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Request Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Request Information</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Monitor:</span>
                                <span id="modalMonitorName" class="text-gray-900"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">URL:</span>
                                <span id="modalMonitorUrl" class="text-gray-900 break-all"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Method:</span>
                                <span id="modalMonitorMethod" class="text-gray-900"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Executed:</span>
                                <span id="modalExecutedAt" class="text-gray-900"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Response Information</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Status:</span>
                                <span id="modalStatus" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Response Time:</span>
                                <span id="modalResponseTime" class="text-gray-900"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">HTTP Code:</span>
                                <span id="modalStatusCode" class="text-gray-900"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div id="errorSection" class="mt-6 hidden">
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-sm font-medium text-red-700">Error Message</h4>
                            <button onclick="copyErrorMessage()" class="text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                📋 Copy
                            </button>
                        </div>
                        <pre id="modalErrorMessage" class="text-sm text-red-800 whitespace-pre-wrap font-mono max-h-96 overflow-auto bg-white p-3 rounded border"></pre>
                    </div>
                </div>

                <!-- Response Body -->
                <div id="responseSection" class="mt-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-sm font-medium text-blue-700">Response Body</h4>
                            <button onclick="copyResponseBody()" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                📋 Copy
                            </button>
                        </div>
                        <pre id="modalResponseBody" class="text-sm text-blue-800 whitespace-pre-wrap font-mono max-h-96 overflow-auto bg-white p-3 rounded border"></pre>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button onclick="closeResultModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testMonitor(monitorId) {
            try {
                // Button-Referenz für Loading-State
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Teste...';
                button.disabled = true;

                const response = await fetch(`/api-monitor/${monitorId}/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'  // Wichtig: JSON-Response anfordern
                    }
                });

                // Prüfen ob Response OK ist
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // Content-Type prüfen
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Unexpected response:', text);
                    throw new Error(`Server returned HTML instead of JSON. Check server logs for errors.`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(`✅ Test erfolgreich!\n\nAntwortzeit: ${result.response_time_ms}ms\nStatus Code: ${result.status_code}`);
                } else {
                    alert(`❌ Test fehlgeschlagen!\n\nFehler: ${result.error_message}\nAntwortzeit: ${result.response_time_ms}ms`);
                }

                // Seite neu laden um aktuellen Status zu zeigen
                location.reload();

            } catch (error) {
                console.error('Test error:', error);
                alert(`❌ Fehler beim Testen: ${error.message}\n\nBitte prüfen Sie die Browser-Konsole und Server-Logs für weitere Details.`);
            } finally {
                // Button zurücksetzen falls noch verfügbar
                try {
                    const button = event.target;
                    button.textContent = originalText;
                    button.disabled = false;
                } catch (e) {
                    // Ignorieren falls Button nicht mehr verfügbar
                }
            }
        }

        function showResultDetails(resultId) {
            const resultData = JSON.parse(document.getElementById(`result-data-${resultId}`).textContent);

            // Modal Title
            document.getElementById('modalTitle').textContent = `API Request Details - ID: ${resultData.id}`;

            // Request Info
            document.getElementById('modalMonitorName').textContent = resultData.monitor_name;
            document.getElementById('modalMonitorUrl').textContent = resultData.monitor_url;
            document.getElementById('modalMonitorMethod').textContent = resultData.monitor_method;
            document.getElementById('modalExecutedAt').textContent = resultData.executed_at;

            // Response Info
            const statusElement = document.getElementById('modalStatus');
            if (resultData.success) {
                statusElement.textContent = 'Erfolgreich';
                statusElement.className = 'font-medium text-green-600';
            } else {
                statusElement.textContent = 'Fehler';
                statusElement.className = 'font-medium text-red-600';
            }

            document.getElementById('modalResponseTime').textContent = `${resultData.response_time_ms}ms`;
            document.getElementById('modalStatusCode').textContent = resultData.status_code || '-';

            // Error Message
            const errorSection = document.getElementById('errorSection');
            if (resultData.error_message) {
                document.getElementById('modalErrorMessage').textContent = resultData.error_message;
                errorSection.classList.remove('hidden');
            } else {
                errorSection.classList.add('hidden');
            }

            // Response Body
            const responseBody = resultData.response_body;
            const responseBodyElement = document.getElementById('modalResponseBody');
            if (responseBody && responseBody !== null) {
                responseBodyElement.textContent = typeof responseBody === 'string'
                    ? responseBody
                    : JSON.stringify(responseBody, null, 2);
            } else {
                responseBodyElement.textContent = 'Keine Response Body verfügbar';
            }

            // Show Modal
            document.getElementById('resultModal').classList.remove('hidden');
        }

        function closeResultModal() {
            document.getElementById('resultModal').classList.add('hidden');
        }

        function copyResponseBody() {
            const responseBody = document.getElementById('modalResponseBody').textContent;
            navigator.clipboard.writeText(responseBody).then(function() {
                // Kurzzeitig Button-Text ändern
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Kopiert!';
                setTimeout(() => {
                    button.textContent = originalText;
                }, 2000);
            }).catch(function(err) {
                console.error('Fehler beim Kopieren: ', err);
                alert('Fehler beim Kopieren in die Zwischenablage');
            });
        }

        function copyErrorMessage() {
            const errorMessage = document.getElementById('modalErrorMessage').textContent;
            navigator.clipboard.writeText(errorMessage).then(function() {
                // Kurzzeitig Button-Text ändern
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Kopiert!';
                setTimeout(() => {
                    button.textContent = originalText;
                }, 2000);
            }).catch(function(err) {
                console.error('Fehler beim Kopieren: ', err);
                alert('Fehler beim Kopieren in die Zwischenablage');
            });
        }

        // Modal schließen bei Escape-Taste
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeResultModal();
            }
        });

        // Modal schließen bei Klick außerhalb
        document.getElementById('resultModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeResultModal();
            }
        });
    </script>
@endsection)
