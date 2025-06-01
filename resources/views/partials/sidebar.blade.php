{{-- resources/views/partials/sidebar.blade.php --}}
<div class="flex-1 flex flex-col min-h-0 bg-white border-r border-gray-200">
    <!-- Logo -->
    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <div class="flex items-center flex-shrink-0 px-4">
            <h1 class="text-xl font-bold text-gray-900">API Monitor</h1>
        </div>

        <!-- Navigation -->
        <nav class="mt-5 flex-1 px-2 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7zm0 0a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2H3V7z" />
                </svg>
                Dashboard
            </a>

            @can('view-monitors')
                <!-- API Monitore -->
                <a href="{{ route('api-monitor.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('api-monitor.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    API Monitore
                    @can('create-monitors')
                        <svg class="ml-auto h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    @endcan
                </a>
            @endcan

            @can('view-users')
                <!-- Benutzerverwaltung -->
                <a href="{{ route('users.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    Benutzer
                </a>
            @endcan

            @can('view-settings')
                <!-- Einstellungen -->
                <a href="{{ route('settings.index') }}"
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Einstellungen
                </a>
            @endcan
        </nav>
    </div>

    <!-- User info -->
    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                    </span>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">
                    {{ auth()->user()->roles->first()->name ?? 'Benutzer' }}
                </p>
            </div>
        </div>
    </div>
</div>
