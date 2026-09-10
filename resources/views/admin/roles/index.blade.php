@extends('layouts.console')
@section('heading', 'Roles & access')
@section('subheading', $roles->count().' roles across '.$permissionCount.' permissions')

@section('actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">New role</a>
@endsection

@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($roles as $role)
        <div class="card-flat p-5 flex flex-col">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h2 class="font-display text-lg font-semibold">{{ $role->name }}</h2>
                    <p class="font-mono text-[11px] text-pine/45 mt-0.5">{{ $role->slug }}</p>
                </div>
                @if($role->is_locked)<span class="badge badge-iris">Locked</span>@endif
            </div>

            <p class="text-[0.95rem] text-pine/75 leading-relaxed flex-1">{{ $role->description }}</p>

            <dl class="grid grid-cols-3 gap-2 mt-5 pt-4 border-t border-pine/8 text-center">
                @foreach(['People' => $role->users_count, 'Permissions' => $role->permissions_count, 'Level' => $role->level] as $label => $value)
                    <div>
                        <dt class="eyebrow text-pine/40 mb-1">{{ $label }}</dt>
                        <dd class="font-display font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="flex gap-2 mt-4">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-ghost btn-sm flex-1">
                    {{ $role->slug === 'owner' ? 'View' : 'Edit' }}
                </a>
                @if($role->isDeletable())
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" data-confirm="Delete this role?">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm text-rose-600">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
