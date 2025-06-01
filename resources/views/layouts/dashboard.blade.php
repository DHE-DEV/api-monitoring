{{-- resources/views/layouts/dashboard.blade.php --}}
    <!DOCTYPE html>
<html lang="de" x-data="{ sidebarOpen: false }" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - API Monitor System</title>

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full" x-data="globalData()">
<div class="min-h-full">
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 flex z-40 lg:hidden">

        <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-white">

            <!-- Close button -->
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="sidebarOpen = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @include('partials.sidebar')
        </div>
    </div>

    <!-- Desktop sidebar -->
    <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
        @include('partials.sidebar')
    </div>

    <!-- Main content -->
    <div class="lg:pl-64 flex flex-col flex-1">
        <!-- Top navigation -->
        <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = true"
                    class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>

            <!-- Top nav content -->
            <div class="flex-1 px-4 flex justify-between">
                <div class="flex-1 flex">
                    <div class="w-full flex md:ml-0">
                        <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm"
                                   placeholder="Suchen..."
                                   type="search">
                        </div>
                    </div>
                </div>

                <!-- User menu -->
                <div class="ml-4 flex items-center md:ml-6">
                    <!-- Notifications -->
                    <button class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- User dropdown -->
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                                    </span>
                            </div>
                            <span class="hidden md:block ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            <svg class="hidden md:block ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">

                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil bearbeiten</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Einstellungen</a>

                            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Abmelden
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page content -->
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</div>

<!-- Alpine.js Notification System -->
<div x-data="notifications()"
     x-on:show-notification.window="show($event.detail)"
     class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="notification.show"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto"
                 :class="{
                         'bg-green-500': notification.type === 'success',
                         'bg-red-500': notification.type === 'error',
                         'bg-blue-500': notification.type === 'info',
                         'bg-yellow-500': notification.type === 'warning'
                     }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                        </div>
                        <button @click="remove(notification.id)"
                                class="ml-4 text-white hover:text-gray-200 focus:outline-none">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    // Global Alpine.js Data
    function globalData() {
        return {
            user: @json(auth()->user()),

            init() {
                // Auto-refresh dashboard data every 30 seconds
                setInterval(() => {
                    this.refreshDashboardData();
                }, 30000);
            },

            async refreshDashboardData() {
                try {
                    const response = await fetch('{{ route('dashboard.data') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.dispatchEvent(new CustomEvent('dashboard-updated', { detail: data }));
                    }
                } catch (error) {
                    console.error('Dashboard refresh error:', error);
                }
            }
        }
    }

    // Notification System
    function notifications() {
        return {
            notifications: [],

            show(notification) {
                const id = Date.now();
                const newNotification = {
                    id: id,
                    show: true,
                    ...notification
                };

                this.notifications.push(newNotification);

                // Auto-remove nach 5 Sekunden
                setTimeout(() => {
                    this.remove(id);
                }, notification.duration || 5000);
            },

            remove(id) {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index > -1) {
                    this.notifications[index].show = false;
                    setTimeout(() => {
                        this.notifications.splice(index, 1);
                    }, 100);
                }
            }
        }
    }
</script>
</body>
</html>
