<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Dashboard - API Monitor</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
<div class="min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">API Monitor Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <span class="mr-4">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                            Abmelden
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4">
        <h2 class="text-2xl font-bold mb-6">Willkommen, {{ auth()->user()->name }}!</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium">Benutzer</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['users_count'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium">Aktive Benutzer</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_users'] }}</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
