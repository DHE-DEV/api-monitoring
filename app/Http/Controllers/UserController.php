// app/Http/Controllers/UserController.php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('manage-users');

        return User::with('roles')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
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

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user)
    {
        $this->authorize('manage-users');

        return $user->load('roles', 'permissions');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage-users');

        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'exists:roles,name',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['first_name']) && isset($validated['last_name'])) {
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        }

        $user->update(array_filter($validated));

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json($user->load('roles'));
    }

    public function destroy(User $user)
    {
        $this->authorize('manage-users');

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Sie können sich nicht selbst löschen.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Benutzer erfolgreich gelöscht.']);
    }
}
