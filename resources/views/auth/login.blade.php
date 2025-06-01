{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Anmelden')

@section('content')
    <div x-data="loginForm()">
        <div class="mb-6">
            <h2 class="text-center text-2xl font-bold text-gray-900">
                Bei Ihrem Konto anmelden
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Geben Sie Ihre Anmeldedaten ein, um fortzufahren
            </p>
        </div>

        <form @submit.prevent="submitLogin" class="space-y-6">
            <!-- E-Mail -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    E-Mail-Adresse
                </label>
                <div class="mt-1">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        x-model="form.email"
                        :disabled="loading"
                        class="appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :class="errors.email ? 'border-red-300' : 'border-gray-300'"
                        placeholder="ihre.email@example.com">
                </div>
                <div x-show="errors.email" class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.email"></p>
                </div>
            </div>

            <!-- Passwort -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Passwort
                </label>
                <div class="mt-1">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        x-model="form.password"
                        :disabled="loading"
                        class="appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :class="errors.password ? 'border-red-300' : 'border-gray-300'"
                        placeholder="Ihr Passwort">
                </div>
                <div x-show="errors.password" class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.password"></p>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        x-model="form.remember"
                        :disabled="loading"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Angemeldet bleiben
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                    :class="loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'">

                    <!-- Loading Spinner -->
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span x-text="loading ? 'Wird angemeldet...' : 'Anmelden'"></span>
                </button>
            </div>
        </form>

        <!-- Demo-Zugänge -->
        <div class="mt-6 p-4 bg-gray-50 rounded-md">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Demo-Zugänge:</h3>
            <div class="space-y-1 text-xs text-gray-600">
                <p><strong>Admin:</strong> admin@apimonitor.local / admin123</p>
                <p><strong>Manager:</strong> manager@apimonitor.local / manager123</p>
                <p><strong>Viewer:</strong> viewer@apimonitor.local / viewer123</p>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                loading: false,
                form: {
                    email: '',
                    password: '',
                    remember: false
                },
                errors: {},

                async submitLogin() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const response = await fetch('{{ route('login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Erfolgs-Notification
                            window.dispatchEvent(new CustomEvent('show-notification', {
                                detail: {
                                    type: 'success',
                                    message: data.message
                                }
                            }));

                            // Weiterleitung zum Dashboard
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route('dashboard') }}';
                            }, 1000);
                        } else {
                            // Fehler anzeigen
                            this.errors = data.errors || {};

                            window.dispatchEvent(new CustomEvent('show-notification', {
                                detail: {
                                    type: 'error',
                                    message: data.message || 'Anmeldung fehlgeschlagen'
                                }
                            }));
                        }
                    } catch (error) {
                        console.error('Login error:', error);
                        window.dispatchEvent(new CustomEvent('show-notification', {
                            detail: {
                                type: 'error',
                                message: 'Ein unerwarteter Fehler ist aufgetreten.'
                            }
                        }));
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection
