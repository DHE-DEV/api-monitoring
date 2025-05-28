{{-- resources/views/api-monitor/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">API Monitor bearbeiten: {{ $apiMonitor->name }}</h2>
        </div>

        <form action="{{ route('api-monitor.update', $apiMonitor) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                           id="name"
                           name="name"
                           type="text"
                           placeholder="Monitor Name eingeben"
                           value="{{ old('name', $apiMonitor->name) }}"
                           required>
                    @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="url">
                        URL
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('url') border-red-500 @enderror"
                           id="url"
                           name="url"
                           type="url"
                           placeholder="https://api.example.com/endpoint"
                           value="{{ old('url', $apiMonitor->url) }}"
                           required>
                    @error('url')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="method">
                        HTTP Methode
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('method') border-red-500 @enderror"
                            id="method"
                            name="method"
                            required>
                        <option value="GET" {{ old('method', $apiMonitor->method) == 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ old('method', $apiMonitor->method) == 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="PUT" {{ old('method', $apiMonitor->method) == 'PUT' ? 'selected' : '' }}>PUT</option>
                        <option value="DELETE" {{ old('method', $apiMonitor->method) == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                    </select>
                    @error('method')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="interval_minutes">
                        Intervall (Minuten)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('interval_minutes') border-red-500 @enderror"
                           id="interval_minutes"
                           name="interval_minutes"
                           type="number"
                           min="1"
                           max="1440"
                           placeholder="15"
                           value="{{ old('interval_minutes', $apiMonitor->interval_minutes) }}"
                           required>
                    @error('interval_minutes')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">Überwachungsintervall zwischen 1 und 1440 Minuten</p>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="headers">
                    Headers (JSON Format)
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono text-sm @error('headers') border-red-500 @enderror"
                          id="headers"
                          name="headers"
                          rows="4"
                          placeholder='{"Content-Type": "application/json", "Accept": "application/json"}'>{{ old('headers', $apiMonitor->headers ? json_encode($apiMonitor->headers, JSON_PRETTY_PRINT) : '') }}</textarea>
                @error('headers')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-xs mt-1">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Leer lassen für keine zusätzlichen Headers. Bearer Token wird automatisch hinzugefügt.
                </span>
                </p>
            </div>

            <div id="payload-section">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="payload">
                    Payload (JSON Format - nur bei POST/PUT)
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono text-sm @error('payload') border-red-500 @enderror"
                          id="payload"
                          name="payload"
                          rows="4"
                          placeholder='{"key": "value", "data": {"nested": "object"}}'>{{ old('payload', $apiMonitor->payload ? json_encode($apiMonitor->payload, JSON_PRETTY_PRINT) : '') }}</textarea>
                @error('payload')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-xs mt-1">Request Body für POST/PUT Anfragen im JSON Format</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                           type="checkbox"
                           name="is_active"
                           id="is_active"
                           value="1"
                        {{ old('is_active', $apiMonitor->is_active) ? 'checked' : '' }}>
                    <label class="ml-3 text-sm font-medium text-gray-700" for="is_active">
                        Monitor ist aktiv
                    </label>
                </div>
                <p class="text-gray-600 text-xs mt-1 ml-7">Deaktivierte Monitore werden nicht automatisch ausgeführt</p>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    @if($apiMonitor->created_at)
                        Erstellt: {{ $apiMonitor->created_at->format('d.m.Y H:i') }}
                        @if($apiMonitor->updated_at && $apiMonitor->updated_at->ne($apiMonitor->created_at))
                            | Zuletzt bearbeitet: {{ $apiMonitor->updated_at->format('d.m.Y H:i') }}
                        @endif
                    @endif
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('api-monitor.index') }}"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                        Abbrechen
                    </a>
                    <button type="button"
                            onclick="testCurrentSettings()"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                        🧪 Testen
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                        💾 Monitor aktualisieren
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodSelect = document.getElementById('method');
            const payloadSection = document.getElementById('payload-section');

            function togglePayloadSection() {
                const method = methodSelect.value;
                if (method === 'GET' || method === 'DELETE') {
                    payloadSection.style.display = 'none';
                } else {
                    payloadSection.style.display = 'block';
                }
            }

            // Initial setup
            togglePayloadSection();

            // Event listener
            methodSelect.addEventListener('change', togglePayloadSection);
        });

        async function testCurrentSettings() {
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('url', document.getElementById('url').value);
            formData.append('method', document.getElementById('method').value);
            formData.append('headers', document.getElementById('headers').value);
            formData.append('payload', document.getElementById('payload').value);
            formData.append('_token', window.Laravel.csrfToken);

            try {
                // Zeige Loading-Anzeige
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Teste...';
                button.disabled = true;

                const response = await fetch(`{{ route('api-monitor.test', $apiMonitor) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(`✅ Test erfolgreich!\n\nAntwortzeit: ${result.response_time_ms}ms\nStatus Code: ${result.status_code}`);
                } else {
                    alert(`❌ Test fehlgeschlagen!\n\nFehler: ${result.error_message}\nAntwortzeit: ${result.response_time_ms}ms`);
                }

                // Button zurücksetzen
                button.textContent = originalText;
                button.disabled = false;

            } catch (error) {
                alert('❌ Fehler beim Testen: ' + error.message);

                // Button zurücksetzen
                event.target.textContent = 'Testen';
                event.target.disabled = false;
            }
        }

        // JSON Validation für Headers und Payload
        document.getElementById('headers').addEventListener('blur', function() {
            validateJSON(this, 'Headers');
        });

        document.getElementById('payload').addEventListener('blur', function() {
            validateJSON(this, 'Payload');
        });

        function validateJSON(element, fieldName) {
            if (!element.value.trim()) {
                element.classList.remove('border-red-300');
                return;
            }

            try {
                JSON.parse(element.value);
                element.classList.remove('border-red-300');
                element.classList.add('border-green-300');
            } catch (e) {
                element.classList.remove('border-green-300');
                element.classList.add('border-red-300');
                // Optional: Zeige Fehler-Tooltip oder Message
            }
        }
    </script>
@endsection
