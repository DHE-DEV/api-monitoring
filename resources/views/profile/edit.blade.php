{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profil Informationen') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Aktualisieren Sie Ihre Profil-Informationen und E-Mail-Adresse.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <!-- Avatar -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profilbild</label>
                                <div class="flex items-center space-x-4">
                                    <img class="h-20 w-20 rounded-full object-cover"
                                         src="{{ $user->avatar_url }}"
                                         alt="{{ $user->name }}">
                                    <div>
                                        <input type="file"
                                               name="avatar"
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF bis zu 2MB</p>
                                    </div>
                                </div>
                                @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input id="name" name="name" type="text"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-Mail</label>
                                <input id="email" name="email" type="email"
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-800">
                                            {{ __('Ihre E-Mail-Adresse ist nicht verifiziert.') }}
                                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Klicken Sie hier, um die Verifizierungs-E-Mail erneut zu senden.') }}
                                            </button>
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Vorname & Nachname -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">Vorname</label>
                                    <input id="first_name" name="first_name" type="text"
                                           value="{{ old('first_name', $user->first_name) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nachname</label>
                                    <input id="last_name" name="last_name" type="text"
                                           value="{{ old('last_name', $user->last_name) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Abteilung & Telefon -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700">Abteilung</label>
                                    <input id="department" name="department" type="text"
                                           value="{{ old('department', $user->department) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                                    <input id="phone" name="phone" type="text"
                                           value="{{ old('phone', $user->phone) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- E-Mail Benachrichtigungen -->
                            <div>
                                <div class="flex items-center">
                                    <input type="hidden" name="email_notifications" value="0">
                                    <input id="email_notifications" name="email_notifications" type="checkbox" value="1"
                                           {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                                        E-Mail-Benachrichtigungen aktivieren
                                    </label>
                                </div>
                            </div>

                            <!-- Benachrichtigungs-Typen -->
                            <div id="notification-types" class="{{ old('email_notifications', $user->email_notifications) ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Benachrichtigungstypen</label>
                                <div class="space-y-2">
                                    @php
                                        $notificationTypes = [
                                            'api_down' => 'API nicht erreichbar',
                                            'slow_response' => 'Langsame Antwortzeit',
                                            'http_error' => 'HTTP-Fehler'
                                        ];
                                    @endphp

                                    @foreach($notificationTypes as $type => $label)
                                        <div class="flex items-center">
                                            <input id="notification_{{ $type }}"
                                                   name="notification_types[]"
                                                   type="checkbox"
                                                   value="{{ $type }}"
                                                   {{ in_array($type, old('notification_types', $user->notification_types ?? [])) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="notification_{{ $type }}" class="ml-2 block text-sm text-gray-900">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    {{ __('Speichern') }}
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <p class="text-sm text-gray-600">{{ __('Gespeichert.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('email_notifications').addEventListener('change', function() {
            const notificationTypes = document.getElementById('notification-types');
            if (this.checked) {
                notificationTypes.classList.remove('hidden');
            } else {
                notificationTypes.classList.add('hidden');
            }
        });
    </script>
@endsection
