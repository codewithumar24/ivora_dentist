<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeToIvora;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can.do:users.view')->only(['index', 'show']);
        $this->middleware('can.do:users.manage')->only(['create', 'store', 'edit', 'update', 'destroy', 'permissions', 'savePermissions']);
    }

    public function index(Request $request)
    {
        return view('admin.users.index', [
            'users' => User::staff()->with('role')->search($request->q)
                ->when($request->role, fn ($q, $r) => $q->whereRelation('role', 'slug', $r))
                ->latest()->paginate(15)->withQueryString(),
            'roles' => Role::where('level', '>=', Role::LEVEL_STAFF)->get(),
            'filters' => $request->only('q', 'role'),
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'roles' => Role::where('level', '>=', Role::LEVEL_STAFF)->orderBy('level')->get(),
        ]);
    }

  public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'unique:users,email'],
        'phone' => ['nullable', 'string', 'max:32', 'unique:users,phone'],
        'role_id' => ['required', 'exists:roles,id'],
        'password' => ['nullable', 'confirmed', Password::min(8)],
        'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $password = $data['password'] ?? Str::password(12);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? null,
        'role_id' => $data['role_id'],
        'password' => $password,
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    if ($request->hasFile('avatar')) {
        $user->update(['avatar' => $request->file('avatar')->store('avatars', 'public')]);
    }

    Mail::to($user->email)->queue(new WelcomeToIvora($user, $password));
    ActivityLog::record('created', "Created staff account for {$user->name}", $user);

    return redirect()->route('admin.users.index')->with('status', 'Team member added and emailed their login.');
}

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user->load('role', 'permissions'),
            'roles' => Role::where('level', '>=', Role::LEVEL_STAFF)->orderBy('level')->get(),
        ]);
    }

   public function update(Request $request, User $user)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'unique:users,email,'.$user->id],
        'phone' => ['nullable', 'string', 'max:32', 'unique:users,phone,'.$user->id],
        'role_id' => ['required', 'exists:roles,id'],
        'status' => ['required', 'in:active,suspended,pending'],
        'password' => ['nullable', 'confirmed', Password::min(8)],
        'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    // The owner seat cannot be demoted by anyone but itself.
    if ($user->isOwner() && auth()->id() !== $user->id) {
        return back()->withErrors(['role_id' => 'Only the clinic owner can change the owner account.']);
    }

    $user->update(collect($data)->except(['avatar'])->filter(fn ($v, $k) => $k !== 'password' || filled($v))->all());
    $user->forgetPermissionCache();

    if ($request->hasFile('avatar')) {
        // Replaced photos are dead weight — remove the old one rather than
        // letting storage/app/public/avatars grow forever.
        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => $request->file('avatar')->store('avatars', 'public')]);
    }

    ActivityLog::record('updated', "Updated staff account {$user->name}", $user);

    return back()->with('status', 'Account saved.');
}

    /** The per-person override screen: grant or revoke single permissions. */
    public function permissions(User $user)
    {
        return view('admin.users.permissions', [
            'user' => $user->load('role.permissions', 'permissions'),
            'groups' => Permission::orderBy('group')->get()->groupBy('group'),
            'rolePermissions' => $user->role?->permissions->pluck('slug')->all() ?? [],
            'overrides' => $user->permissions->pluck('pivot.granted', 'slug')->all(),
        ]);
    }

    public function savePermissions(Request $request, User $user)
    {
        $overrides = collect($request->input('permissions', []))
            ->filter(fn ($state) => $state !== 'inherit')
            ->mapWithKeys(function ($state, $permissionId) {
                return [$permissionId => ['granted' => $state === 'grant']];
            });

        $user->permissions()->sync($overrides);
        $user->forgetPermissionCache();

        ActivityLog::record('updated', "Changed permission overrides for {$user->name}", $user, [
            'overrides' => $overrides->count(),
        ]);

        return back()->with('status', 'Permissions saved. They apply the next time this person loads a page.');
    }

   public function destroy(User $user)
{
    if ($user->isOwner()) {
        return back()->withErrors(['user' => 'The owner account cannot be deleted.']);
    }

    if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
    }

    $user->delete();
    ActivityLog::record('deleted', "Removed staff account {$user->name}", $user);

    return redirect()->route('admin.users.index')->with('status', 'Team member removed.');
}
}
