<?php
// app/Http/Controllers/UserManagementController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ApiMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class UserManagementController extends Controller
{

    public function index(Request $request)
    {
        if (! auth()->user()->canManageUsers()) {
            abort(403, 'Keine Berechtigung');
        }

        Gate::authorize('manage-users');

        $query = User::query();

        // Suche
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter nach Rolle
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Filter nach Status
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sortierung
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'email', 'role', 'created_at', 'last_login_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->with(['createdBy', 'updatedBy'])->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('manage-users');

        $monitors = ApiMonitor::all();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('admin.users.create', compact('monitors', 'departments'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_types' => 'array',
            'notification_types.*' => 'in:api_down,slow_response,http_error',
            'monitor_access' => 'array',
            'monitor_access.*' => 'exists:api_monitors,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Avatar Upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = basename($avatarPath);
        }

        // Checkbox-Werte setzen
        $validated['is_active'] = $request->has('is_active');
        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['password_changed_at'] = now();
        $validated['created_by'] = auth()->id();

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich erstellt');
    }

    public function show(User $user)
    {
        Gate::authorize('view-user', $user);

        $user->load(['createdBy', 'updatedBy', 'apiMonitors']);

        // Zugängliche Monitore
        $accessibleMonitors = [];
        if ($user->monitor_access) {
            $accessibleMonitors = ApiMonitor::whereIn('id', $user->monitor_access)->get();
        } else {
            $accessibleMonitors = ApiMonitor::all();
        }

        return view('admin.users.show', compact('user', 'accessibleMonitors'));
    }

    public function edit(User $user)
    {
        Gate::authorize('manage-users');

        $monitors = ApiMonitor::all();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('admin.users.edit', compact('user', 'monitors', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_types' => 'array',
            'notification_types.*' => 'in:api_down,slow_response,http_error',
            'monitor_access' => 'array',
            'monitor_access.*' => 'exists:api_monitors,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Password nur aktualisieren wenn angegeben
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password_changed_at'] = now();
        }

        // Avatar Upload
        if ($request->hasFile('avatar')) {
            // Alten Avatar löschen
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = basename($avatarPath);
        }

        // Checkbox-Werte setzen
        $validated['is_active'] = $request->has('is_active');
        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['updated_by'] = auth()->id();

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich aktualisiert');
    }

    public function destroy(User $user)
    {
        Gate::authorize('manage-users');

        // Verhindere das Löschen des eigenen Accounts
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Sie können Ihren eigenen Account nicht löschen');
        }

        // Avatar löschen
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich gelöscht');
    }

    public function toggleStatus(User $user)
    {
        Gate::authorize('manage-users');

        // Verhindere das Deaktivieren des eigenen Accounts
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Sie können Ihren eigenen Account nicht deaktivieren'
            ], 400);
        }

        $user->update([
            'is_active' => !$user->is_active,
            'updated_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'Benutzer aktiviert' : 'Benutzer deaktiviert',
            'is_active' => $user->is_active
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email_notifications' => 'boolean',
            'notification_types' => 'array',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = basename($avatarPath);
        }

        $validated['email_notifications'] = $request->has('email_notifications');

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profil erfolgreich aktualisiert');
    }
}
