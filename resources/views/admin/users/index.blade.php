@extends('layouts.console')
@section('heading', 'Team')
@section('subheading', $users->total().' staff accounts')

@section('actions')
    @permission('users.manage')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Add team member</a>
    @endpermission
@endsection

@section('content')
<div class="space-y-5">
    <form method="GET" class="card-flat filter-bar p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="field-label">Search</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="field !py-2 !text-sm">
        </div>
        <div>
            <label class="field-label">Role</label>
            <select name="role" class="field !py-2 !text-sm">
                <option value="">Any</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" @selected(($filters['role'] ?? '') === $role->slug)>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>

    <x-ui.panel :padded="false">
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Person</th><th>Role</th><th>Contact</th><th>Last seen</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $user->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="font-medium truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-pine/50 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->isOwner() ? 'iris' : 'mint' }}">{{ $user->role?->name }}</span>
                            </td>
                            <td class="font-mono text-xs">{{ $user->phone ?? '—' }}</td>
                            <td class="text-xs text-pine/55">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td><span class="badge badge-{{ $user->status === 'active' ? 'jade' : 'slate' }}">{{ ucfirst($user->status) }}</span></td>
                            <td class="text-right whitespace-nowrap">
                                @permission('users.manage')
                                    <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-ghost btn-sm">Access</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>
                                @endpermission
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.panel>

    {{ $users->links() }}
</div>
@endsection
