<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Zeige Login-Form
     */
    public function showLoginForm()
    {
        // Redirect wenn bereits eingeloggt
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Login verarbeiten (Web-Version)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Ungültige E-Mail-Adresse.',
            'password.required' => 'Passwort ist erforderlich.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Login-Zeit aktualisieren
            Auth::user()->update(['last_login_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Erfolgreich angemeldet.',
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Die angegebenen Anmeldedaten sind ungültig.',
            'errors' => [
                'email' => ['E-Mail oder Passwort ist falsch.']
            ]
        ], 422);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Erfolgreich abgemeldet.'
            ]);
        }

        return redirect()->route('login');
    }
}
