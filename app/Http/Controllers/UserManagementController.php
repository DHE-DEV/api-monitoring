<?php
// app/Http/Controllers/UserManagementController.php - Korrigierte Version

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    // Constructor entfernt - Permissions werden über Routes gehandhabt

    /**
     * Benutzer-Übersicht anzeigen
     */
    public function index()
    {
        // Permission-Check hier, falls nötig
        if (!auth()->user()->can('view-users')) {
            abort(403, 'Keine Berechtigung zum Anzeigen von Benutzern.');
        }

        $users = User::with('roles')
            ->latest()
            ->paginate(15);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subWeek())
                ->count(),
        ];

        $roles = Role::all();

        return view('users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Benutzer-Details anzeigen
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');

        // Benutzer-Aktivität
        $activityStats = [
            'monitors_count' => $user->apiMonitors()->count() ?? 0,
            'active_monitors' => $user->apiMonitors()->where('is_active', true)->count() ?? 0,
            'created_monitors' => $user->apiMonitors()->whereDate('created_at', today())->count() ?? 0,
        ];

        return view('users.show', compact('user', 'activityStats'));
    }

    /**
     * Neuen Benutzer erstellen - Formular anzeigen
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Neuen Benutzer speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ], [
            'first_name.required' => 'Vorname ist erforderlich.',
            'last_name.required' => 'Nachname ist erforderlich.',
            'email.required' => 'E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Ungültige E-Mail-Adresse.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'password.required' => 'Passwort ist erforderlich.',
            'password.min' => 'Passwort muss mindestens 8 Zeichen haben.',
            'password.confirmed' => 'Passwort-Bestätigung stimmt nicht überein.',
            'role.required' => 'Rolle ist erforderlich.',
            'role.exists' => 'Ungültige Rolle ausgewählt.',
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Benutzer erfolgreich erstellt.',
                'user' => $user->load('roles')
            ], 201);
        }

        return redirect()->route('users.index')
            ->with('success', 'Benutzer "' . $user->full_name . '" wurde erfolgreich erstellt.');
    }

    /**
     * Benutzer bearbeiten - Formular anzeigen
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Benutzer aktualisieren
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ], [
            'first_name.required' => 'Vorname ist erforderlich.',
            'last_name.required' => 'Nachname ist erforderlich.',
            'email.required' => 'E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Ungültige E-Mail-Adresse.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'password.min' => 'Passwort muss mindestens 8 Zeichen haben.',
            'password.confirmed' => 'Passwort-Bestätigung stimmt nicht überein.',
            'role.required' => 'Rolle ist erforderlich.',
            'role.exists' => 'Ungültige Rolle ausgewählt.',
        ]);

        $updateData = [
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ];

        // Passwort nur aktualisieren wenn angegeben
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Benutzer erfolgreich aktualisiert.',
                'user' => $user->load('roles')
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Benutzer "' . $user->full_name . '" wurde erfolgreich aktualisiert.');
    }

    /**
     * Benutzer löschen
     */
    public function destroy(User $user)
    {
        // Schutz: Benutzer kann sich nicht selbst löschen
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sie können sich nicht selbst löschen.'
                ], 403);
            }

            return redirect()->route('users.index')
                ->with('error', 'Sie können sich nicht selbst löschen.');
        }

        $userName = $user->full_name;
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Benutzer erfolgreich gelöscht.'
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Benutzer "' . $userName . '" wurde erfolgreich gelöscht.');
    }

    /**
     * Benutzer-Status schnell umschalten (AJAX)
     */
    public function toggleStatus(User $user)
    {
        // Schutz: Benutzer kann sich nicht selbst deaktivieren
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Sie können sich nicht selbst deaktivieren.'
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'Benutzer aktiviert.' : 'Benutzer deaktiviert.',
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Benutzer-Passwort zurücksetzen
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'password123'; // In Produktion: Zufälliges Passwort generieren

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // In Produktion: E-Mail mit neuem Passwort senden

        return response()->json([
            'success' => true,
            'message' => 'Passwort wurde zurückgesetzt.',
            'new_password' => $newPassword // Nur für Demo - in Produktion nicht zurückgeben!
        ]);
    }

    /**
     * Benutzer-Daten für AJAX abrufen
     */
    public function getUserData()
    {
        $users = User::with('roles')
            ->select('id', 'name', 'first_name', 'last_name', 'email', 'is_active', 'last_login_at', 'created_at')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'role' => $user->roles->first()?->name,
                    'last_login' => $user->last_login_at?->format('d.m.Y H:i'),
                    'created_at' => $user->created_at->format('d.m.Y'),
                    'can_edit' => auth()->user()->can('edit-users'),
                    'can_delete' => auth()->user()->can('delete-users') && $user->id !== auth()->id(),
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Bulk-Aktionen für mehrere Benutzer
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = collect($validated['user_ids'])
            ->reject(fn($id) => $id == auth()->id()) // Aktuellen Benutzer ausschließen
            ->values();

        if ($userIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine gültigen Benutzer ausgewählt.'
            ], 400);
        }

        $affectedCount = 0;

        switch ($validated['action']) {
            case 'activate':
                $affectedCount = User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = "$affectedCount Benutzer aktiviert.";
                break;

            case 'deactivate':
                $affectedCount = User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = "$affectedCount Benutzer deaktiviert.";
                break;

            case 'delete':
                $affectedCount = User::whereIn('id', $userIds)->delete();
                $message = "$affectedCount Benutzer gelöscht.";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => $affectedCount
        ]);
    }
}
