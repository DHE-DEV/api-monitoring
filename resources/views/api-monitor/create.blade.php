{{-- resources/views/api-monitor/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <!-- Header Section -->
    <div class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <!-- Breadcrumb -->
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('api-monitor.index') }}" class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10l4.707-4.707a1 1 0 011.414 1.414L6.414 10l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('api-monitor.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">API Monitore</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-900">Neuer Monitor</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                            Neuen API Monitor erstellen
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Konfigurieren Sie einen neuen Monitor für Ihre API-Überwachung
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('api-monitor.store') }}" method="POST" id="monitorForm">
            @csrf

            <div class="space-y-6">
                <!-- Basic Configuration -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900">Grundkonfiguration</h3>
                                <p class="mt-1 text-sm text-gray-600">Name, URL und grundlegende Einstellungen des Monitors</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700">
                                    Monitor Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           required
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 @error('name') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                           placeholder="z.B. User API Health Check">
                                </div>
                                @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- URL -->
                            <div class="space-y-2">
                                <label for="url" class="block text-sm font-semibold text-gray-700">
                                    API URL <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                    <input type="url"
                                           name="url"
                                           id="url"
                                           value="{{ old('url') }}"
                                           required
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 @error('url') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                           placeholder="https://api.example.com/health">
                                </div>
                                @error('url')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- HTTP Method -->
                            <div class="space-y-2">
                                <label for="method" class="block text-sm font-semibold text-gray-700">
                                    HTTP Methode <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <select name="method"
                                            id="method"
                                            required
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 @error('method') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror appearance-none bg-white">
                                        <option value="GET" {{ old('method') == 'GET' ? 'selected' : '' }}>GET</option>
                                        <option value="POST" {{ old('method') == 'POST' ? 'selected' : '' }}>POST</option>
                                        <option value="PUT" {{ old('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                                        <option value="DELETE" {{ old('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('method')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- Interval -->
                            <div class="space-y-2">
                                <label for="interval_minutes" class="block text-sm font-semibold text-gray-700">
                                    Überwachungsintervall <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="number"
                                           name="interval_minutes"
                                           id="interval_minutes"
                                           value="{{ old('interval_minutes', 15) }}"
                                           min="1"
                                           max="1440"
                                           required
                                           class="block w-full pl-10 pr-16 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 @error('interval_minutes') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-medium">min</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">Zwischen 1 und 1440 Minuten (24 Stunden)</p>
                                @error('interval_minutes')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Configuration -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900">Erweiterte Konfiguration</h3>
                                <p class="mt-1 text-sm text-gray-600">HTTP Headers und Request Body für den API-Aufruf</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Headers -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="headers" class="block text-sm font-semibold text-gray-700">
                                    HTTP Headers (JSON Format)
                                </label>
                                <button type="button"
                                        onclick="formatJSON('headers')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    JSON formatieren
                                </button>
                            </div>
                            <div class="relative">
                                <textarea name="headers"
                                          id="headers"
                                          rows="5"
                                          class="block w-full p-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 font-mono text-sm resize-none @error('headers') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                          placeholder='{"Content-Type": "application/json", "Accept": "application/json"}'>{{ old('headers') }}</textarea>
                                <div class="absolute top-2 right-2">
                                    <div class="flex items-center space-x-1">
                                        <div id="headers-status" class="hidden">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <svg class="h-4 w-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Leer lassen für keine zusätzlichen Headers. Bearer Token wird automatisch aus der Konfiguration hinzugefügt.
                                </p>
                            </div>
                            @error('headers')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Payload -->
                        <div id="payload-section" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="payload" class="block text-sm font-semibold text-gray-700">
                                    Request Body (JSON Format)
                                </label>
                                <button type="button"
                                        onclick="formatJSON('payload')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    JSON formatieren
                                </button>
                            </div>
                            <div class="relative">
                                <textarea name="payload"
                                          id="payload"
                                          rows="5"
                                          class="block w-full p-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400 font-mono text-sm resize-none @error('payload') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                          placeholder='{"key": "value", "data": {"nested": "object"}}'>{{ old('payload') }}</textarea>
                                <div class="absolute top-2 right-2">
                                    <div class="flex items-center space-x-1">
                                        <div id="payload-status" class="hidden">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Request Body für POST/PUT Anfragen im JSON Format</p>
                            @error('payload')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="h-4 w-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Configuration -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900">Monitor Status</h3>
                                <p class="mt-1 text-sm text-gray-600">Aktivierungsstatus des neuen Monitors</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Monitor Active -->
                        <div class="flex items-start p-4 rounded-xl bg-green-50 border border-green-200">
                            <div class="flex items-center h-6">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div class="ml-4">
                                <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer">Monitor ist aktiv</label>
                                <p class="text-xs text-gray-500 mt-1">Monitor wird sofort nach der Erstellung aktiviert und überwacht</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <span class="text-red-500">*</span> Pflichtfelder
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('api-monitor.index') }}"
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Abbrechen
                                </a>
                                <button type="button"
                                        onclick="testFormSettings()"
                                        class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    Testen (ohne Speichern)
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Monitor erstellen
                                </button>
                            </div>
                        </div>
                    </div>
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

            // JSON Validation with visual feedback
            document.getElementById('headers').addEventListener('input', function() {
                validateJSONField(this, 'headers-status');
            });

            document.getElementById('payload').addEventListener('input', function() {
                validateJSONField(this, 'payload-status');
            });

            // Initial validation
            validateJSONField(document.getElementById('headers'), 'headers-status');
            validateJSONField(document.getElementById('payload'), 'payload-status');
        });

        function validateJSONField(element, statusId) {
            const statusElement = document.getElementById(statusId);

            if (!element.value.trim()) {
                element.classList.remove('border-red-300', 'border-green-300');
                element.classList.add('border-gray-300');
                statusElement.classList.add('hidden');
                return;
            }

            try {
                JSON.parse(element.value);
                element.classList.remove('border-red-300');
                element.classList.add('border-green-300');
                statusElement.classList.remove('hidden');
                statusElement.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            } catch (e) {
                element.classList.remove('border-green-300');
                element.classList.add('border-red-300');
                statusElement.classList.remove('hidden');
                statusElement.innerHTML = '<svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            }
        }

        function formatJSON(fieldId) {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();

            if (!value) return;

            try {
                const parsed = JSON.parse(value);
                field.value = JSON.stringify(parsed, null, 2);
                field.classList.remove('border-red-300');
                field.classList.add('border-green-300');

                // Update status indicator
                const statusId = fieldId + '-status';
                validateJSONField(field, statusId);
            } catch (e) {
                alert('Ungültiges JSON Format. Bitte korrigieren Sie die Syntax.');
                field.focus();
            }
        }

        async function testFormSettings() {
            const button = event.target;
            const originalHTML = button.innerHTML;

            // Validation
            const name = document.getElementById('name').value;
            const url = document.getElementById('url').value;

            if (!name || !url) {
                alert('❌ Bitte füllen Sie Name und URL aus.');
                return;
            }

            try {
                button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Teste...';
                button.disabled = true;

                const formData = {
                    name: name,
                    url: url,
                    method: document.getElementById('method').value,
                    headers: document.getElementById('headers').value,
                    payload: document.getElementById('payload').value,
                };

                // Parse headers
                let parsedHeaders = {};
                if (formData.headers.trim()) {
                    try {
                        parsedHeaders = JSON.parse(formData.headers);
                    } catch (e) {
                        throw new Error('Ungültiges JSON Format in Headers');
                    }
                }

                // Parse payload for POST/PUT
                let requestBody = undefined;
                if (formData.method !== 'GET' && formData.method !== 'DELETE' && formData.payload.trim()) {
                    try {
                        requestBody = formData.payload;
                    } catch (e) {
                        throw new Error('Ungültiges JSON Format in Payload');
                    }
                }

                // Test API call
                const startTime = performance.now();
                const response = await fetch(formData.url, {
                    method: formData.method,
                    headers: {
                        'Content-Type': 'application/json',
                        ...parsedHeaders
                    },
                    body: requestBody
                });

                const endTime = performance.now();
                const responseTime = Math.round(endTime - startTime);

                alert(`✅ Test erfolgreich!\n\nAntwortzeit: ${responseTime}ms\nStatus Code: ${response.status}\nURL: ${formData.url}\nMethode: ${formData.method}`);

            } catch (error) {
                console.error('Test error:', error);
                alert(`❌ Test fehlgeschlagen!\n\nFehler: ${error.message}`);
            } finally {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        }

        console.log('Monitor Create loaded successfully');
    </script>
@endsection
