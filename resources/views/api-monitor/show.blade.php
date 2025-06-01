{{-- resources/views/api-monitor/show.blade.php --}}
@extends('layouts.dashboard')

@section('title', $apiMonitor->name . ' - Monitor Details')

@section('content')
    @php
        // Statistics calculations
        $last24h = $results->where('executed_at', '>=', now()->subDay());
        $lastMonth = $results->where('executed_at', '>=', now()->subDays(30));

        $successRate24h = $last24h->count() > 0 ? ($last24h->where('success', true)->count() / $last24h->count()) * 100 : 0;
        $successRateMonth = $lastMonth->count() > 0 ? ($lastMonth->where('success', true)->count() / $lastMonth->count()) * 100 : 0;

        $avgResponseTime24h = $last24h->where('success', true)->avg('response_time_ms');
        $avgResponseTimeMonth = $lastMonth->where('success', true)->avg('response_time_ms');

        $maxResponseTime24h = $last24h->where('success', true)->max('response_time_ms');
        $minResponseTime24h = $last24h->where('success', true)->min('response_time_ms');

        $totalDowntime = $lastMonth->where('success', false)->count() * $apiMonitor->interval_minutes;
    @endphp

        <!-- Header Section -->
    <div class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('api-monitor.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10l4.707-4.707a1 1 0 011.414 1.414L6.414 10l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('api-monitor.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">API Monitore</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-900">{{ $apiMonitor->name }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full flex items-center justify-center {{ $apiMonitor->is_active ? 'bg-green-100' : 'bg-gray-100' }}">
                                <svg class="h-6 w-6 {{ $apiMonitor->is_active ? 'text-green-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                {{ $apiMonitor->name }}
                            </h1>
                            <div class="mt-1 flex items-center text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $apiMonitor->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $apiMonitor->is_active ? 'Aktiv' : 'Inaktiv' }}
                            </span>
                                <span class="mx-2">•</span>
                                <span>ID: {{ $apiMonitor->id }}</span>
                                <span class="mx-2">•</span>
                                <span>Erstellt: {{ $apiMonitor->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                    <button onclick="testMonitor({{ $apiMonitor->id }})"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Jetzt testen
                    </button>

                    @can('manage-monitors')
                        <a href="{{ route('api-monitor.edit', $apiMonitor) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Bearbeiten
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Monitor Configuration -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Monitor Konfiguration</h3>
                <p class="mt-1 text-sm text-gray-500">Einstellungen und Parameter dieses API-Monitors</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Endpoint Info -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Endpunkt Details</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">URL</label>
                                    <p class="mt-1 text-sm text-gray-900 break-all">{{ $apiMonitor->url }}</p>
                                </div>
                                <div class="flex space-x-6">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Methode</label>
                                        <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $apiMonitor->method }}
                                        </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Intervall</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $apiMonitor->interval_minutes }} Minuten</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($apiMonitor->headers)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Headers</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 whitespace-pre-wrap font-mono">{{ json_encode($apiMonitor->headers, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif

                        @if($apiMonitor->payload)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Payload</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 whitespace-pre-wrap font-mono">{{ json_encode($apiMonitor->payload, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Status & Alerts -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Status & Benachrichtigungen</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Monitor Status</label>
                                        <div class="mt-1 flex items-center">
                                            <div class="h-2 w-2 rounded-full {{ $apiMonitor->is_active ? 'bg-green-400' : 'bg-gray-400' }} mr-2"></div>
                                            <span class="text-sm text-gray-900">{{ $apiMonitor->is_active ? 'Aktiv überwacht' : 'Pausiert' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">E-Mail Alerts</label>
                                            <div class="mt-1 flex items-center">
                                                @if($apiMonitor->email_alerts_enabled)
                                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                    </svg>
                                                    <span class="text-sm text-green-700 font-medium">Aktiv</span>
                                                @else
                                                    <svg class="h-4 w-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                        <path fill-rule="evenodd" d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-14-14z"></path>
                                                    </svg>
                                                    <span class="text-sm text-red-700 font-medium">Deaktiviert</span>
                                                @endif
                                            </div>
                                            @if(!$apiMonitor->email_alerts_enabled && $apiMonitor->email_alerts_disabled_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Seit {{ $apiMonitor->email_alerts_disabled_at->format('d.m.Y H:i') }}
                                                    @if($apiMonitor->email_alerts_disabled_reason)
                                                        <br>Grund: {{ $apiMonitor->email_alerts_disabled_reason }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Letzter Test</label>
                                    @if($apiMonitor->latestResult)
                                        <div class="mt-1">
                                            <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $apiMonitor->latestResult->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $apiMonitor->latestResult->success ? 'Erfolgreich' : 'Fehler' }}
                                            </span>
                                                <span class="ml-2 text-sm text-gray-600">
                                                {{ $apiMonitor->latestResult->response_time_ms }}ms
                                            </span>
                                                @if($apiMonitor->latestResult->status_code)
                                                    <span class="ml-2 text-xs text-gray-500">({{ $apiMonitor->latestResult->status_code }})</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $apiMonitor->latestResult->executed_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-gray-500">Noch nicht getestet</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($results->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 24h Statistics -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistiken (letzte 24h)</h3>
                        <p class="mt-1 text-sm text-gray-500">Performance der letzten 24 Stunden</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Erfolgsrate</dt>
                                <dd class="mt-1 text-3xl font-semibold {{ $successRate24h >= 95 ? 'text-green-600' : ($successRate24h >= 90 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($successRate24h, 1) }}%
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ø Antwortzeit</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $avgResponseTime24h ? number_format($avgResponseTime24h, 0) . 'ms' : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Anfragen</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $last24h->count() }}</dd>
                                <dd class="text-sm text-gray-500">{{ $last24h->where('success', true)->count() }} erfolgreich</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Min/Max Zeit</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $minResponseTime24h ? number_format($minResponseTime24h, 0) : '-' }}ms /
                                    {{ $maxResponseTime24h ? number_format($maxResponseTime24h, 0) : '-' }}ms
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 30 Days Statistics -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistiken (30 Tage)</h3>
                        <p class="mt-1 text-sm text-gray-500">Langzeit-Performance des letzten Monats</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Erfolgsrate</dt>
                                <dd class="mt-1 text-3xl font-semibold {{ $successRateMonth >= 95 ? 'text-green-600' : ($successRateMonth >= 90 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($successRateMonth, 1) }}%
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ø Antwortzeit</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $avgResponseTimeMonth ? number_format($avgResponseTimeMonth, 0) . 'ms' : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Anfragen</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $lastMonth->count() }}</dd>
                                <dd class="text-sm text-gray-500">{{ $lastMonth->where('success', true)->count() }} erfolgreich</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ausfallzeit</dt>
                                <dd class="mt-1 text-lg font-semibold {{ $totalDowntime > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $totalDowntime > 0 ? $totalDowntime . ' Min' : '0 Min' }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Results Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Test Ergebnisse</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $results->total() }} Testergebnisse gesamt</p>
                    </div>
                    <a href="{{ route('api-monitor.export', array_merge(['apiMonitor' => $apiMonitor->id], request()->query())) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel Export
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Zeitraum Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Zeitraum</label>
                        <select name="time_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('time_filter') == 'all' ? 'selected' : '' }}>Alle</option>
                            <option value="today" {{ request('time_filter') == 'today' ? 'selected' : '' }}>Heute</option>
                            <option value="week" {{ request('time_filter') == 'week' ? 'selected' : '' }}>Diese Woche</option>
                            <option value="month" {{ request('time_filter') == 'month' ? 'selected' : '' }}>Dieser Monat</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="" {{ request('status_filter') == '' ? 'selected' : '' }}>Alle</option>
                            <option value="errors" {{ request('status_filter') == 'errors' ? 'selected' : '' }}>Nur Fehler</option>
                        </select>
                    </div>

                    <!-- HTTP Code Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">HTTP Code</label>
                        <select name="http_code_filter" class="block w-full bg-white border border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Alle</option>
                            @foreach($availableHttpCodes as $code)
                                <option value="{{ $code }}" {{ request('http_code_filter') == $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-200 text-sm">
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
                               class="flex items-center hover:text-gray-700 group">
                                Zeitpunkt
                                <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antwortzeit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HTTP Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fehler</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($results as $result)
                        <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $result->success ? '' : 'bg-red-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $result->executed_at->setTimezone('Europe/Berlin')->format('d.m.Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if($result->success)
                                    <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    OK
                                @else
                                    <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Fehler
                                @endif
                            </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-medium text-gray-900">{{ $result->response_time_ms }}ms</span>
                                @if($result->response_time_ms > 2000)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Langsam</span>
                                @elseif($result->response_time_ms > 1000)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Mittel</span>
                                @else
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Schnell</span>
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
                                    {{ Str::limit($result->error_message, 40) }}
                                </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="showResultDetails({{ $result->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                        title="Details anzeigen">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <!-- Hidden data for modal -->
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Ergebnisse</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Für die gewählten Filter wurden keine Testergebnisse gefunden.
                                </p>
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
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Teste...';
                button.disabled = true;

                const response = await fetch(`/api-monitor/${monitorId}/test`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

                location.reload();

            } catch (error) {
                console.error('Test error:', error);
                alert(`❌ Fehler beim Testen: ${error.message}`);
            } finally {
                try {
                    button.innerHTML = originalText;
                    button.disabled = false;
                } catch (e) {}
            }
        }

        function showResultDetails(resultId) {
            const resultData = JSON.parse(document.getElementById(`result-data-${resultId}`).textContent);

            document.getElementById('modalTitle').textContent = `API Request Details - ID: ${resultData.id}`;
            document.getElementById('modalMonitorName').textContent = resultData.monitor_name;
            document.getElementById('modalMonitorUrl').textContent = resultData.monitor_url;
            document.getElementById('modalMonitorMethod').textContent = resultData.monitor_method;
            document.getElementById('modalExecutedAt').textContent = resultData.executed_at;

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

            const errorSection = document.getElementById('errorSection');
            if (resultData.error_message) {
                document.getElementById('modalErrorMessage').textContent = resultData.error_message;
                errorSection.classList.remove('hidden');
            } else {
                errorSection.classList.add('hidden');
            }

            const responseBody = resultData.response_body;
            const responseBodyElement = document.getElementById('modalResponseBody');
            if (responseBody && responseBody !== null) {
                responseBodyElement.textContent = typeof responseBody === 'string' ? responseBody : JSON.stringify(responseBody, null, 2);
            } else {
                responseBodyElement.textContent = 'Keine Response Body verfügbar';
            }

            document.getElementById('resultModal').classList.remove('hidden');
        }

        function closeResultModal() {
            document.getElementById('resultModal').classList.add('hidden');
        }

        function copyResponseBody() {
            const responseBody = document.getElementById('modalResponseBody').textContent;
            navigator.clipboard.writeText(responseBody).then(function() {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Kopiert!';
                setTimeout(() => { button.textContent = originalText; }, 2000);
            }).catch(function(err) {
                console.error('Fehler beim Kopieren: ', err);
                alert('Fehler beim Kopieren in die Zwischenablage');
            });
        }

        function copyErrorMessage() {
            const errorMessage = document.getElementById('modalErrorMessage').textContent;
            navigator.clipboard.writeText(errorMessage).then(function() {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Kopiert!';
                setTimeout(() => { button.textContent = originalText; }, 2000);
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

        console.log('Monitor Details loaded successfully');
    </script>
@endsection
