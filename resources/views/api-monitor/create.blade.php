{{-- resources/views/api-monitor/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Neuen API Monitor erstellen</h2>
        </div>

        <form action="{{ route('api-monitor.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                    <input type="url" name="url" id="url" value="{{ old('url') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('url') border-red-300 @enderror">
                    @error('url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700">HTTP Methode</label>
                    <select name="method" id="method" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('method') border-red-300 @enderror">
                        <option value="GET" {{ old('method') == 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ old('method') == 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="PUT" {{ old('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                        <option value="DELETE" {{ old('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                    </select>
                    @error('method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="interval_minutes" class="block text-sm font-medium text-gray-700">Intervall (Minuten)</label>
                    <input type="number" name="interval_minutes" id="interval_minutes" value="{{ old('interval_minutes', 15) }}" min="1" max="1440" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('interval_minutes') border-red-300 @enderror">
                    @error('interval_minutes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="headers" class="block text-sm font-medium text-gray-700">Headers (JSON Format)</label>
                <textarea name="headers" id="headers" rows="3" placeholder='{"Content-Type": "application/json"}'
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm @error('headers') border-red-300 @enderror">{{ old('headers') }}</textarea>
                @error('headers')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Leer lassen für keine zusätzlichen Headers. Bearer Token wird automatisch hinzugefügt.</p>
            </div>

            <div id="payload-section">
                <label for="payload" class="block text-sm font-medium text-gray-700">Payload (JSON Format - nur bei POST/PUT)</label>
                <textarea name="payload" id="payload" rows="3" placeholder='{"key": "value"}'
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm @error('payload') border-red-300 @enderror">{{ old('payload') }}</textarea>
                @error('payload')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Monitor ist aktiv</label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('api-monitor.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Abbrechen
                </a>
                <button type="button" onclick="testFormSettings()" class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Testen (ohne Speichern)
                </button>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Monitor erstellen
                </button>
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

        async function testFormSettings() {
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Teste...';
            button.disabled = true;

            try {
                const formData = {
                    name: document.getElementById('name').value,
                    url: document.getElementById('url').value,
                    method: document.getElementById('method').value,
                    headers: document.getElementById('headers').value,
                    payload: document.getElementById('payload').value,
                };

                // Validation
                if (!formData.name || !formData.url) {
                    alert('❌ Bitte füllen Sie Name und URL aus.');
                    return;
                }

                // Test API call (simplified - you could create a test endpoint)
                const response = await fetch(formData.url, {
                    method: formData.method,
                    headers: {
                        'Content-Type': 'application/json',
                        ...JSON.parse(formData.headers || '{}')
                    },
                    body: formData.method !== 'GET' && formData.method !== 'DELETE' ? formData.payload : undefined
                });

                alert(`✅ Test erfolgreich!\n\nStatus: ${response.status}\nURL: ${formData.url}\nMethode: ${formData.method}`);

            } catch (error) {
                alert(`❌ Test fehlgeschlagen!\n\nFehler: ${error.message}`);
            } finally {
                button.textContent = originalText;
                button.disabled = false;
            }
        }

        // JSON Validation
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
            }
        }
    </script>
@endsection
