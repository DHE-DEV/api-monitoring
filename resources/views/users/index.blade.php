{{-- resources/views/users/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Benutzerverwaltung')

@section('content')
    <div x-data="userManagement()" class="space-y-6">

        <!-- Header -->
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                            Benutzerverwaltung
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Verwalten Sie Benutzer, Rollen und Berechtigungen des Systems
                        </p>
                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $stats['total_users'] }} Benutzer insgesamt
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $stats['active_users'] }} aktiv
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                        @can('create-users')
                            <button @click="showCreateModal = true"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                Neuer Benutzer
                            </button>
                        @endcan

                        <button @click="refreshUsers"
                                :disabled="loading"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                :class="loading ? 'opacity-50 cursor-not-allowed' : ''">
                            <svg class="-ml-1 mr-2 h-5 w-5" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span x-text="loading ? 'Aktualisiert...' : 'Aktualisieren'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Gesamte Benutzer</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Alle Benutzer</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Aktive Benutzer</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['active_users'] }}</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                            <span>{{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}%</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Von {{ $stats['total_users'] }} Benutzern</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Inaktive Benutzer</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['inactive_users'] }}</div>
                                        @if($stats['inactive_users'] > 0)
                                            <div class="ml-2 flex items-baseline text-sm font-semibold text-red-600">
                                                <span>Deaktiviert</span>
                                            </div>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Nicht angemeldet</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Letzte 30 Tage</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['recent_logins'] }}</div>
                                        <div class="ml-2 flex items-baseline text-sm font-semibold text-blue-600">
                                            <span>Anmeldungen</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700">Kürzliche Aktivität</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Suche -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Benutzer Übersicht</h3>
                    <p class="mt-1 text-sm text-gray-500">Alle registrierten Benutzer und deren aktueller Status</p>
                </div>
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <!-- Suche -->
                        <div class="flex-1 max-w-lg">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input x-model="searchQuery"
                                       @input="filterUsers"
                                       type="text"
                                       placeholder="Nach Name oder E-Mail suchen..."
                                       class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition-all duration-200">
                                <!-- Clear Search Button -->
                                <div x-show="searchQuery"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button @click="searchQuery=''; filterUsers()"
                                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <!-- Status Filter -->
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-700 mb-1 sm:sr-only">Status</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <select x-model="statusFilter"
                                            @change="filterUsers"
                                            class="block w-full pl-10 pr-10 py-2 text-sm border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="">Alle Status</option>
                                        <option value="active">Aktiv</option>
                                        <option value="inactive">Inaktiv</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Filter -->
                            <div class="relative">
                                <label class="block text-xs font-medium text-gray-700 mb-1 sm:sr-only">Rolle</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <select x-model="roleFilter"
                                            @change="filterUsers"
                                            class="block w-full pl-10 pr-10 py-2 text-sm border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="">Alle Rollen</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Benutzer-Tabelle -->
                @if($users->count() > 0)
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox"
                                           @change="toggleSelectAll($event.target.checked)"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benutzer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rolle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letzter Login</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-200" :class="selectedUsers.includes(user.id) ? 'bg-blue-50' : ''">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                               :value="user.id"
                                               @change="toggleSelectUser(user.id, $event.target.checked)"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                                     :class="user.is_active ? 'bg-indigo-500' : 'bg-gray-400'"
                                                     x-text="user.name.split(' ').map(n => n[0]).join('').toUpperCase()"></div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                                <div class="text-sm text-gray-500" x-text="user.email"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="(role, index) in (user.roles || [])" :key="`role-${user.id}-${index}`">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      :class="{
                                                          'bg-red-100 text-red-800': (typeof role === 'string' ? role : role.name) === 'Super Admin',
                                                          'bg-blue-100 text-blue-800': (typeof role === 'string' ? role : role.name) === 'Manager',
                                                          'bg-green-100 text-green-800': (typeof role === 'string' ? role : role.name) === 'Viewer',
                                                          'bg-purple-100 text-purple-800': (typeof role === 'string' ? role : role.name) === 'Monitor',
                                                          'bg-gray-100 text-gray-800': !['Super Admin', 'Manager', 'Viewer', 'Monitor'].includes(typeof role === 'string' ? role : role.name)
                                                      }"
                                                      x-text="typeof role === 'string' ? role : role.name"></span>
                                            </template>
                                            <!-- Fallback: Falls user.role existiert aber user.roles nicht -->
                                            <template x-if="(!user.roles || user.roles.length === 0) && user.role">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      :class="{
                                                          'bg-red-100 text-red-800': user.role === 'Super Admin',
                                                          'bg-blue-100 text-blue-800': user.role === 'Manager',
                                                          'bg-green-100 text-green-800': user.role === 'Viewer',
                                                          'bg-purple-100 text-purple-800': user.role === 'Monitor',
                                                          'bg-gray-100 text-gray-800': !['Super Admin', 'Manager', 'Viewer', 'Monitor'].includes(user.role)
                                                      }"
                                                      x-text="user.role"></span>
                                            </template>
                                            <!-- Keine Rolle zugewiesen -->
                                            <template x-if="(!user.roles || user.roles.length === 0) && !user.role">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 italic">
                                                    Keine Rolle
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button @click="toggleUserStatus(user)"
                                                :disabled="user.id === currentUserId"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200"
                                                :class="{
                                                    'bg-green-100 text-green-800 hover:bg-green-200': user.is_active,
                                                    'bg-red-100 text-red-800 hover:bg-red-200': !user.is_active,
                                                    'opacity-50 cursor-not-allowed': user.id === currentUserId
                                                }">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path x-show="user.is_active" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                <path x-show="!user.is_active" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span x-text="user.is_active ? 'Aktiv' : 'Inaktiv'"></span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div x-show="user.last_login" class="flex items-center text-sm text-gray-900">
                                            <span x-text="formatGermanDate(user.last_login)"></span>
                                        </div>
                                        <span x-show="!user.last_login" class="text-gray-400 italic">Nie angemeldet</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span x-text="formatGermanDate(user.created_at)"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a :href="`/users/${user.id}`"
                                               class="text-indigo-600 hover:text-indigo-900 transition duration-200"
                                               title="Details anzeigen">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            @can('edit-users')
                                                <button @click="editUser(user)"
                                                        class="text-blue-600 hover:text-blue-900 transition duration-200"
                                                        title="Bearbeiten">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            @endcan

                                            <button @click="resetPassword(user)"
                                                    class="text-yellow-600 hover:text-yellow-900 transition duration-200"
                                                    title="Passwort zurücksetzen">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                </svg>
                                            </button>

                                            @can('delete-users')
                                                <button @click="deleteUser(user)"
                                                        :disabled="user.id === currentUserId"
                                                        class="text-red-600 hover:text-red-900 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        title="Löschen">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Benutzer</h3>
                        <p class="mt-1 text-sm text-gray-500">Erstellen Sie den ersten Benutzer um mit der Verwaltung zu beginnen.</p>
                        @can('create-users')
                            <div class="mt-6">
                                <button @click="showCreateModal = true"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Ersten Benutzer erstellen
                                </button>
                            </div>
                        @endcan
                    </div>
                @endif

                <!-- Keine Ergebnisse (nur bei Filter) -->
                <div x-show="filteredUsers && filteredUsers.length === 0 && (searchQuery || statusFilter || roleFilter)"
                     class="text-center py-12 border-t">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Benutzer gefunden</h3>
                    <p class="mt-1 text-sm text-gray-500">Versuchen Sie andere Suchkriterien oder löschen Sie die Filter.</p>
                    <div class="mt-4">
                        <button @click="searchQuery=''; statusFilter=''; roleFilter=''; filterUsers()"
                                class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            Filter zurücksetzen
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div x-show="selectedUsers.length > 0"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <span class="text-sm text-gray-700">
                            <span x-text="selectedUsers.length"></span> Benutzer ausgewählt
                        </span>
                        </div>
                        <div class="flex space-x-2">
                            @can('edit-users')
                                <button @click="bulkActivate"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                                    Aktivieren
                                </button>

                                <button @click="bulkDeactivate"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 transition-colors duration-200">
                                    Deaktivieren
                                </button>
                            @endcan

                            @can('delete-users')
                                <button @click="bulkDelete"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                                    Löschen
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit User Modal -->
        <div x-show="showCreateModal || showEditModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">

            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900"
                            x-text="showCreateModal ? 'Neuen Benutzer erstellen' : 'Benutzer bearbeiten'"></h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form @submit.prevent="saveUser" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Vorname</label>
                                <input x-model="userForm.first_name"
                                       type="text"
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="errors.first_name" class="mt-1 text-sm text-red-600" x-text="errors.first_name"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nachname</label>
                                <input x-model="userForm.last_name"
                                       type="text"
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="errors.last_name" class="mt-1 text-sm text-red-600" x-text="errors.last_name"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-Mail-Adresse</label>
                            <input x-model="userForm.email"
                                   type="email"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <div x-show="errors.email" class="mt-1 text-sm text-red-600" x-text="errors.email"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    <span x-text="showCreateModal ? 'Passwort' : 'Neues Passwort (optional)'"></span>
                                </label>
                                <input x-model="userForm.password"
                                       type="password"
                                       :required="showCreateModal"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div x-show="errors.password" class="mt-1 text-sm text-red-600" x-text="errors.password"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Passwort bestätigen</label>
                                <input x-model="userForm.password_confirmation"
                                       type="password"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rolle</label>
                            <select x-model="userForm.role"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Rolle auswählen</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div x-show="errors.role" class="mt-1 text-sm text-red-600" x-text="errors.role"></div>
                        </div>

                        <div class="flex items-center">
                            <input x-model="userForm.is_active"
                                   type="checkbox"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">
                                Benutzer ist aktiv
                            </label>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                Abbrechen
                            </button>
                            <button type="submit"
                                    :disabled="loading"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                <span x-text="loading ? 'Speichert...' : (showCreateModal ? 'Erstellen' : 'Speichern')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userManagement() {
            return {
                users: @json($users->items()),
                filteredUsers: [],
                loading: false,
                searchQuery: '',
                statusFilter: '',
                roleFilter: '',
                selectedUsers: [],
                showCreateModal: false,
                showEditModal: false,
                currentUserId: {{ auth()->id() }},

                userForm: {
                    id: null,
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: '',
                    is_active: true
                },

                errors: {},

                init() {
                    this.filteredUsers = [...this.users];
                    console.log('User Management initialized with', this.users.length, 'users');
                },

                async refreshUsers() {
                    this.loading = true;
                    try {
                        // Seite neu laden für aktuellste Daten
                        window.location.reload();
                    } catch (error) {
                        this.$dispatch('show-notification', {
                            type: 'error',
                            message: 'Fehler beim Laden der Benutzer'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                filterUsers() {
                    this.filteredUsers = this.users.filter(user => {
                        const matchesSearch = !this.searchQuery ||
                            user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            user.email.toLowerCase().includes(this.searchQuery.toLowerCase());

                        const matchesStatus = !this.statusFilter ||
                            (this.statusFilter === 'active' && user.is_active) ||
                            (this.statusFilter === 'inactive' && !user.is_active);

                        // Verbesserte Rollen-Filterung: sowohl user.roles Array als auch user.role Feld unterstützen
                        const matchesRole = !this.roleFilter ||
                            (user.roles && Array.isArray(user.roles) && user.roles.some(role => {
                                const roleName = typeof role === 'string' ? role : role.name;
                                return roleName === this.roleFilter;
                            })) ||
                            (user.role === this.roleFilter);

                        return matchesSearch && matchesStatus && matchesRole;
                    });
                },

                toggleSelectAll(checked) {
                    if (checked) {
                        this.selectedUsers = this.filteredUsers.map(user => user.id);
                    } else {
                        this.selectedUsers = [];
                    }
                },

                toggleSelectUser(userId, checked) {
                    if (checked) {
                        this.selectedUsers.push(userId);
                    } else {
                        this.selectedUsers = this.selectedUsers.filter(id => id !== userId);
                    }
                },

                editUser(user) {
                    // Rollen-Daten korrekt extrahieren
                    let userRole = '';
                    if (user.roles && Array.isArray(user.roles) && user.roles.length > 0) {
                        // Role kann ein String oder ein Objekt mit .name Eigenschaft sein
                        const firstRole = user.roles[0];
                        userRole = typeof firstRole === 'string' ? firstRole : firstRole.name;
                    } else if (user.role) {
                        userRole = user.role;
                    }

                    this.userForm = {
                        id: user.id,
                        first_name: user.first_name || user.name.split(' ')[0],
                        last_name: user.last_name || user.name.split(' ')[1] || '',
                        email: user.email,
                        password: '',
                        password_confirmation: '',
                        role: userRole,
                        is_active: user.is_active
                    };
                    this.showEditModal = true;
                },

                async saveUser() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const url = this.showCreateModal ? '/admin/users' : `/admin/users/${this.userForm.id}`;
                        const method = this.showCreateModal ? 'POST' : 'PUT';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.userForm)
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message);
                            this.closeModal();
                            await this.refreshUsers();
                        } else {
                            this.errors = data.errors || {};
                            alert('❌ Fehler beim Speichern');
                        }
                    } catch (error) {
                        alert('❌ Fehler beim Speichern des Benutzers');
                    } finally {
                        this.loading = false;
                    }
                },

                async toggleUserStatus(user) {
                    if (user.id === this.currentUserId) return;

                    try {
                        const response = await fetch(`/admin/users/${user.id}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            user.is_active = data.is_active;
                            alert('✅ ' + data.message);
                        }
                    } catch (error) {
                        alert('❌ Fehler beim Ändern des Status');
                    }
                },

                async deleteUser(user) {
                    if (user.id === this.currentUserId) return;

                    if (!confirm(`Möchten Sie den Benutzer "${user.name}" wirklich löschen?`)) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/users/${user.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message);
                            await this.refreshUsers();
                        }
                    } catch (error) {
                        alert('❌ Fehler beim Löschen des Benutzers');
                    }
                },

                async resetPassword(user) {
                    if (!confirm(`Möchten Sie das Passwort für "${user.name}" zurücksetzen?`)) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/users/${user.id}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert(`✅ ${data.message}\nNeues Passwort: ${data.new_password}`);
                        }
                    } catch (error) {
                        alert('❌ Fehler beim Zurücksetzen des Passworts');
                    }
                },

                async bulkActivate() {
                    await this.bulkAction('activate');
                },

                async bulkDeactivate() {
                    await this.bulkAction('deactivate');
                },

                async bulkDelete() {
                    if (!confirm(`Möchten Sie ${this.selectedUsers.length} Benutzer wirklich löschen?`)) {
                        return;
                    }
                    await this.bulkAction('delete');
                },

                async bulkAction(action) {
                    try {
                        const response = await fetch('/admin/users/bulk-action', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                action: action,
                                user_ids: this.selectedUsers
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message);
                            await this.refreshUsers();
                            this.selectedUsers = [];
                        }
                    } catch (error) {
                        alert('❌ Fehler bei der Bulk-Aktion');
                    }
                },

                closeModal() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.userForm = {
                        id: null,
                        first_name: '',
                        last_name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '',
                        is_active: true
                    };
                    this.errors = {};
                },

                // Deutsche Datumsformatierung
                formatGermanDate(dateString) {
                    if (!dateString) return '-';

                    try {
                        const date = new Date(dateString);

                        // Prüfen ob das Datum gültig ist
                        if (isNaN(date.getTime())) return '-';

                        // Deutsche Formatierung: DD.MM.YYYY HH:MM
                        const day = date.getDate().toString().padStart(2, '0');
                        const month = (date.getMonth() + 1).toString().padStart(2, '0');
                        const year = date.getFullYear();
                        const hours = date.getHours().toString().padStart(2, '0');
                        const minutes = date.getMinutes().toString().padStart(2, '0');

                        return `${day}.${month}.${year} ${hours}:${minutes}`;
                    } catch (error) {
                        console.error('Fehler beim Formatieren des Datums:', error);
                        return '-';
                    }
                }
            }
        }

        console.log('User Management loaded successfully');
    </script>
@endsection
