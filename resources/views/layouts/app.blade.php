<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow-sm">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold text-gray-900">API Monitor</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('api-monitor.index') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                <a href="{{ route('api-monitor.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Neuer Monitor</a>
            </div>
        </div>
    </div>
</nav>

<main class="w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<script>
    window.Laravel = {
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
</script>
</body>
</html>
