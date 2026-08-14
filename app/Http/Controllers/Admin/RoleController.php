<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:roles.manage');
    }

    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users', 'permissions')->orderByDesc('level')->get(),
            'permissionCount' => Permission::count(),
        ]);
    }

    public function create()
    {
        return view('admin.roles.form', [
            'role' => new Role(['level' => Role::LEVEL_STAFF]),
            'groups' => Permission::orderBy('group')->get()->groupBy('group'),
            'assigned' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:10', 'max:80'],
            'permissions' => ['array'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'level' => $data['level'],
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);
        ActivityLog::record('created', "Created role {$role->name}", $role);

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.form', [
            'role' => $role,
            'groups' => Permission::orderBy('group')->get()->groupBy('group'),
            'assigned' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:10', 'max:90'],
            'permissions' => ['array'],
        ]);

        if ($role->slug === Role::OWNER) {
            return back()->withErrors(['name' => 'The owner role always holds every permission and cannot be edited.']);
        }

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'level' => $data['level'],
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        // Anyone holding this role gets a fresh permission map.
        $role->users->each->forgetPermissionCache();

        ActivityLog::record('updated', "Updated role {$role->name}", $role);

        return back()->with('status', 'Role saved.');
    }

    public function destroy(Role $role)
    {
        if (! $role->isDeletable()) {
            return back()->withErrors(['role' => 'This role is either locked or still assigned to someone.']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }
}
