{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    @php
        $monitorsCount = \App\Models\ApiMonitor::count() ?? 0;
        $activeMonitors = \App\Models\ApiMonitor::where('is_active', true)->count() ?? 0;
        $usersCount = \App\Models\User::count() ?? 0;
        $successRate = $monitorsCount > 0 ? round(($activeMonitors / $monitorsCount) * 100) : 0;

        // Erweiterte Monitoring-Metriken
        $lastTestResult = \App\Models\ApiMonitorResult::latest()->first();
        $systemHealthy = $activeMonitors >= ($monitorsCount * 0.8); // 80% der Monitore sollten aktiv sein

        // 24h Erfolgsrate - basierend auf 'success' boolean
        $last24hSuccess = \App\Models\ApiMonitorResult::where('created_at', '>=', now()->subDay())
            ->where('success', true)->count();
        $last24hTotal = \App\Models\ApiMonitorResult::where('created_at', '>=', now()->subDay())->count();
        $last24hSuccessRate = $last24hTotal > 0 ? round(($last24hSuccess / $last24hTotal) * 100) : 0;

        // Durchschnittliche Response Time (letzte 24h) - 'response_time_ms' Spalte
        $avgResponseTime = \App\Models\ApiMonitorResult::where('created_at', '>=', now()->subDay())
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');
        $avgResponseTimeFormatted = $avgResponseTime ? round($avgResponseTime) . 'ms' : 'N/A';

        // Heutige Fehler - success = false
        $todaysErrors = \App\Models\ApiMonitorResult::whereDate('created_at', today())
            ->where('success', false)->count();
    @endphp

        <!-- Einfaches Dashboard ohne komplexe JS-Interaktionen -->
    <div>
        <!-- Header Section -->
        <div class="bg-white shadow mb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center">
                            <div>
                                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                    Willkommen zurück, {{ auth()->user()->first_name ?? explode(' ', auth()->user()->name)[0] }}!
                                </h1>
                                <dl class="mt-6 flex flex-col sm:mt-2 sm:flex-row sm:flex-wrap">
                                    <dt class="sr-only">Letzter Login</dt>
                                    <dd class="flex items-center text-sm text-gray-500 font-medium sm:mr-6">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Letzter Login:
                                        @if(auth()->user()->last_login_at)
                                            {{ auth()->user()->last_login_at->diffForHumans() }}
                                        @else
                                            Erstmalig
                                        @endif
                                    </dd>
                                    <dt class="sr-only">Rolle</dt>
                                    <dd class="mt-3 flex items-center text-sm text-gray-500 font-medium sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ auth()->user()->roles->first()->name ?? 'Benutzer' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                        @can('manage-monitors')
                            <a href="{{ route('api-monitor.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                Neuer Monitor
                            </a>
                        @endcan

                        <button onclick="location.reload()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Aktualisieren
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Erste Reihe -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                <!-- Gesamte Monitore -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Gesamte Monitore</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $monitorsCount }}</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                            <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414 6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('api-monitor.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Alle anzeigen
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Aktive Monitore -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Aktive Monitore</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $activeMonitors }}</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $successRate >= 90 ? 'text-green-600' : ($successRate >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                            <span>{{ $successRate >= 90 ? 'Ausgezeichnet' : ($successRate >= 70 ? 'Gut' : 'Verbesserung nötig') }}</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">{{ $successRate }}% aktiv</span>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Status -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-6 w-6 rounded-full {{ $systemHealthy ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Monitoring Status</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $systemHealthy ? 'Gesund' : 'Achtung' }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            @if($lastTestResult)
                                <span class="font-medium text-gray-700">
                                Letzter Test: {{ $lastTestResult->created_at->diffForHumans() }}
                            </span>
                            @else
                                <span class="font-medium text-gray-700">Noch keine Tests durchgeführt</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 24h Erfolgsrate -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">24h Erfolgsrate</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $last24hSuccessRate }}%</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $last24hSuccessRate >= 95 ? 'text-green-600' : ($last24hSuccessRate >= 90 ? 'text-yellow-600' : 'text-red-600') }}">
                                            @if($last24hSuccessRate >= 95)
                                                <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($last24hSuccessRate >= 90)
                                                <svg class="self-center flex-shrink-0 h-4 w-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="self-center flex-shrink-0 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">{{ $last24hSuccess }}/{{ $last24hTotal }} Tests erfolgreich</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Zweite Reihe -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                <!-- Response Time -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ø Response Time</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $avgResponseTimeFormatted }}</div>
                                        @if($avgResponseTime)
                                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $avgResponseTime <= 500 ? 'text-green-600' : ($avgResponseTime <= 1500 ? 'text-yellow-600' : 'text-red-600') }}">
                                                @if($avgResponseTime <= 500)
                                                    <span>Schnell</span>
                                                @elseif($avgResponseTime <= 1500)
                                                    <span>Okay</span>
                                                @else
                                                    <span>Langsam</span>
                                                @endif
                                            </div>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Letzte 24 Stunden</span>
                        </div>
                    </div>
                </div>

                <!-- Heutige Fehler -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Heutige Fehler</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $todaysErrors }}</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $todaysErrors == 0 ? 'text-green-600' : ($todaysErrors <= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                            @if($todaysErrors == 0)
                                                <span>Perfekt</span>
                                            @elseif($todaysErrors <= 5)
                                                <span>Wenige</span>
                                            @else
                                                <span>Viele</span>
                                            @endif
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">{{ now()->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>

                @can('view-users')
                    <!-- Benutzer -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Benutzer</dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">{{ $usersCount }}</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                    Verwalten
                                </a>
                            </div>
                        </div>
                    </div>
                @endcan

                <!-- Leerraum für 4. Box falls gewünscht -->
                @cannot('view-users')
                    <div></div> <!-- Leere Box für gleichmäßiges Layout -->
                @endcannot
            </div>
        </div>



    </div>

    <!-- Recent Activity & System Info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <!-- Kürzliche Monitore -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Kürzliche Monitore</h3>
                    <p class="mt-1 text-sm text-gray-500">Die zuletzt erstellten API-Monitore</p>
                </div>
                <div class="p-6">
                    @php
                        $recentMonitors = \App\Models\ApiMonitor::latest()->take(5)->get();
                    @endphp

                    @if($recentMonitors->count() === 0)
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Monitore</h3>
                            <p class="mt-1 text-sm text-gray-500">Erstellen Sie Ihren ersten API-Monitor.</p>
                            @can('manage-monitors')
                                <div class="mt-6">
                                    <a href="{{ route('api-monitor.create') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Monitor erstellen
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentMonitors as $monitor)
                                <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 min-w-0 flex-1">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="h-8 w-8 rounded-full flex items-center justify-center {{ $monitor->is_active ? 'bg-green-100' : 'bg-gray-100' }}">
                                                    <svg class="h-4 w-4 {{ $monitor->is_active ? 'text-green-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $monitor->name }}</h4>
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $monitor->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $monitor->is_active ? 'Aktiv' : 'Inaktiv' }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500 truncate mt-1">{{ $monitor->url }}</p>
                                                <div class="flex items-center mt-2 text-xs text-gray-400">
                                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Erstellt: {{ $monitor->created_at->format('d.m.Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Informationen -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">System Informationen</h3>
                    <p class="mt-1 text-sm text-gray-500">Ihre Berechtigungen und System-Details</p>
                </div>
                <div class="p-6">
                    <!-- Benutzer-Rolle -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Ihre Rolle</h4>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ auth()->user()->roles->first()->name ?? 'Benutzer' }}
                            </span>
                        </div>
                    </div>

                    <!-- Berechtigungen -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Ihre Berechtigungen</h4>
                        <div class="grid grid-cols-1 gap-2">
                            @if(auth()->user()->getAllPermissions()->count() > 0)
                                @foreach(auth()->user()->getAllPermissions()->take(6) as $permission)
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</span>
                                    </div>
                                @endforeach
                                @if(auth()->user()->getAllPermissions()->count() > 6)
                                    <div class="text-sm text-gray-500">
                                        und {{ auth()->user()->getAllPermissions()->count() - 6 }} weitere...
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-gray-500">Keine spezifischen Berechtigungen zugewiesen</div>
                            @endif
                        </div>
                    </div>

                    <!-- Letzte Aktivität -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Letzte Aktivität</h4>
                        <div class="text-sm text-gray-500">
                            <p>Angemeldet: {{ now()->format('d.m.Y H:i') }}</p>
                            <p>Benutzer: {{ auth()->user()->email }}</p>
                            <p>Session: {{ session()->getId() ? Str::limit(session()->getId(), 12) : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Einfacher Auto-Refresh ohne Alpine.js -->
    <script>
        // Auto-Refresh alle 30 Sekunden
        setInterval(function() {
            location.reload();
        }, 30000); // 30000ms = 30 Sekunden

        console.log('Dashboard Auto-Refresh aktiv: alle 30 Sekunden');
    </script>
@endsection
