{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center space-x-6">
                        <!-- Avatar -->
                        <img class="h-24 w-24 rounded-full object-cover"
                             src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}">

                        <!-- User Info -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name ?? $user->name }}</h1>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            @if($user->department)
                                <p class="text-gray-500">{{ $user->department }}</p>
                            @endif
                            <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                                   ($user->role === 'manager' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $user->role_display }}
                            </span>
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktiv
                                </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Deaktiviert
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Edit Button -->
                        <div>
                            <a href="{{ route('profile.edit') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                Profil bearbeiten
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Persönliche Informationen</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vollständiger Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->full_name ?? $user->name }}</p>
                        </div>

                        @if($user->first_name || $user->last_name)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Vorname</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->first_name ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nachname</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->last_name ?? '-' }}</p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-Mail-Adresse</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                ✓ Verifiziert
                            </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                ⚠ Nicht verifiziert
                            </span>
                            @endif
                        </div>

                        @if($user->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Konto-Informationen</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rolle</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->role_display }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konto-Status</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->status_display }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Registriert seit</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                        </div>

                        @if($user->last_login_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Letzter Login</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->last_login_at->format('d.m.Y H:i') }}</p>
                                @if($user->last_login_ip)
                                    <p class="text-xs text-gray-500">von {{ $user->last_login_ip }}</p>
                                @endif
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-Mail-Benachrichtigungen</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $user->email_notifications ? 'Aktiviert' : 'Deaktiviert' }}
                            </p>
                            @if($user->email_notifications && $user->notification_types)
                                <div class="mt-2 space-y-1">
                                    @foreach($user->notification_types as $type)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                        @switch($type)
                                                @case('api_down')
                                                    API nicht erreichbar
                                                    @break
                                                @case('slow_response')
                                                    Langsame Antwortzeit
                                                    @break
                                                @case('http_error')
                                                    HTTP-Fehler
                                                    @break
                                                @default
                                                    {{ $type }}
                                            @endswitch
                                    </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monitor Access (if applicable) -->
            @if($user->monitor_access)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Monitor-Zugriffsrechte</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Dieser Benutzer hat Zugriff auf spezifische Monitore:</p>
                        <div class="space-y-2">
                            @foreach(\App\Models\ApiMonitor::whereIn('id', $user->monitor_access)->get() as $monitor)
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded">
                                    <span class="text-sm font-medium text-gray-900">{{ $monitor->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $monitor->url }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
