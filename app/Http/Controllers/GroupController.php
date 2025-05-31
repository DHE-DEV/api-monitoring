<?php
// app/Http/Controllers/GroupController.php
namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\ApiMonitor;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('view_groups')) {
            abort(403, 'Keine Berechtigung zum Anzeigen von Gruppen');
        }

        $query = Group::with(['members', 'monitors', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $groups = $query->paginate(20)->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('create_groups')) {
            abort(403, 'Keine Berechtigung zum Erstellen von Gruppen');
        }

        $permissions = Permission::byCategory('monitors')->get();
        $monitors = ApiMonitor::all();
        $users = User::all();

        return view('admin.groups.create', compact('permissions', 'monitors', 'users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create_groups')) {
            abort(403, 'Keine Berechtigung zum Erstellen von Gruppen');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:groups,slug',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
            'members' => 'array',
            'members.*' => 'exists:users,id',
            'monitors' => 'array',
            'monitors.*' => 'exists:api_monitors,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();

        $group = Group::create($validated);

        // Add members
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $userId) {
                $group->addMember(User::find($userId), $validated['permissions'] ?? []);
            }
        }

        // Add monitors
        if (!empty($validated['monitors'])) {
            foreach ($validated['monitors'] as $monitorId) {
                $group->addMonitor(ApiMonitor::find($monitorId), $validated['permissions'] ?? []);
            }
        }

        return redirect()->route('admin.groups.index')
            ->with('success', 'Gruppe erfolgreich erstellt');
    }

    public function show(Group $group)
    {
        if (!auth()->user()->hasPermission('view_groups')) {
            abort(403, 'Keine Berechtigung zum Anzeigen von Gruppen');
        }

        $group->load(['members.primaryRole', 'monitors', 'creator']);

        return view('admin.groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        if (!auth()->user()->hasPermission('edit_groups')) {
            abort(403, 'Keine Berechtigung zum Bearbeiten von Gruppen');
        }

        $permissions = Permission::byCategory('monitors')->get();
        $monitors = ApiMonitor::all();
        $users = User::all();

        return view('admin.groups.edit', compact('group', 'permissions', 'monitors', 'users'));
    }

    public function update(Request $request, Group $group)
    {
        if (!auth()->user()->hasPermission('edit_groups')) {
            abort(403, 'Keine Berechtigung zum Bearbeiten von Gruppen');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('groups')->ignore($group)],
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
            'members' => 'array',
            'members.*' => 'exists:users,id',
            'monitors' => 'array',
            'monitors.*' => 'exists:api_monitors,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $group->update($validated);

        // Sync members
        $group->members()->detach();
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $userId) {
                $group->addMember(User::find($userId), $validated['permissions'] ?? []);
            }
        }

        // Sync monitors
        $group->monitors()->detach();
        if (!empty($validated['monitors'])) {
            foreach ($validated['monitors'] as $monitorId) {
                $group->addMonitor(ApiMonitor::find($monitorId), $validated['permissions'] ?? []);
            }
        }

        return redirect()->route('admin.groups.index')
            ->with('success', 'Gruppe erfolgreich aktualisiert');
    }

    public function destroy(Group $group)
    {
        if (!auth()->user()->hasPermission('delete_groups')) {
            abort(403, 'Keine Berechtigung zum Löschen von Gruppen');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Gruppe erfolgreich gelöscht');
    }

    public function addMember(Request $request, Group $group)
    {
        if (!auth()->user()->hasPermission('manage_group_members')) {
            abort(403, 'Keine Berechtigung zum Verwalten von Gruppen-Mitgliedern');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'string'
        ]);

        $user = User::find($validated['user_id']);
        $group->addMember($user, $validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Benutzer zur Gruppe hinzugefügt'
        ]);
    }

    public function removeMember(Request $request, Group $group, User $user)
    {
        if (!auth()->user()->hasPermission('manage_group_members')) {
            abort(403, 'Keine Berechtigung zum Verwalten von Gruppen-Mitgliedern');
        }

        $group->removeMember($user);

        return response()->json([
            'success' => true,
            'message' => 'Benutzer aus Gruppe entfernt'
        ]);
    }

    public function addMonitor(Request $request, Group $group)
    {
        if (!auth()->user()->hasPermission('edit_groups')) {
            abort(403, 'Keine Berechtigung zum Bearbeiten von Gruppen');
        }

        $validated = $request->validate([
            'monitor_id' => 'required|exists:api_monitors,id',
            'permissions' => 'array',
            'permissions.*' => 'string'
        ]);

        $monitor = ApiMonitor::find($validated['monitor_id']);
        $group->addMonitor($monitor, $validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Monitor zur Gruppe hinzugefügt'
        ]);
    }

    public function removeMonitor(Request $request, Group $group, ApiMonitor $monitor)
    {
        if (!auth()->user()->hasPermission('edit_groups')) {
            abort(403, 'Keine Berechtigung zum Bearbeiten von Gruppen');
        }

        $group->removeMonitor($monitor);

        return response()->json([
            'success' => true,
            'message' => 'Monitor aus Gruppe entfernt'
        ]);
    }
}
