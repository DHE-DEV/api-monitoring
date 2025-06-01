{{-- resources/views/layouts/auth.blade.php --}}
    <!DOCTYPE html>
<html lang="de" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Login') - API Monitor System</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full">
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo/Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">API Monitor</h1>
            <p class="mt-2 text-sm text-gray-600">Überwachung & Verwaltung</p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center">
        <p class="text-xs text-gray-500">
            &copy; {{ date('Y') }} API Monitor System. Alle Rechte vorbehalten.
        </p>
    </div>
</div>

<!-- Alpine.js Notification System -->
<div x-data="notifications()"
     x-on:show-notification.window="show($event.detail)"
     class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50">
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
        <template x-for="notification in notifications" :key="notification.id">
            <div
                x-show="notification.show"
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
                        'bg-blue-500': notification.type === 'info'
                    }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="remove(notification.id)"
                                    class="inline-flex text-white hover:text-gray-200 focus:outline-none">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    // Alpine.js Notification System
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
                }, 5000);
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
