@extends('layouts.app')

@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">API Monitore</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervall</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letzte Antwortzeit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($monitors as $monitor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $monitor->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></div>
                                <div class="text-sm font-medium text-gray-900">{{ $monitor->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $monitor->url }}</div>
                            <div class="text-sm text-gray-500">{{ $monitor->method }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $monitor->interval_minutes }} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($monitor->latestResult)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $monitor->latestResult->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $monitor->latestResult->success ? 'OK' : 'Fehler' }}
                            </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Noch nicht getestet
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($monitor->latestResult)
                                {{ $monitor->latestResult->response_time_ms }}ms
                                @if($monitor->latestResult->status_code)
                                    ({{ $monitor->latestResult->status_code }})
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('api-monitor.show', $monitor) }}" class="text-blue-600 hover:text-blue-900">Details</a>
                                <a href="{{ route('api-monitor.edit', $monitor) }}" class="text-indigo-600 hover:text-indigo-900">Bearbeiten</a>
                                <button onclick="testMonitor({{ $monitor->id }})" class="text-green-600 hover:text-green-900">Testen</button>
                                <form action="{{ route('api-monitor.destroy', $monitor) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Sind Sie sicher?')">Löschen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Keine API Monitore vorhanden.
                            <a href="{{ route('api-monitor.create') }}" class="text-blue-600 hover:text-blue-900">Erstellen Sie den ersten Monitor</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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

                location.reload();
            } catch (error) {
                alert('Fehler beim Testen: ' + error.message);
            }
        }
    </script>
@endsection
