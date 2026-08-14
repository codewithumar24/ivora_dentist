@extends('layouts.console')
@section('heading', 'Access for '.$user->name)
@section('subheading', 'Role: '.($user->role?->name ?? 'none').' — overrides below beat the role')

@section('content')
<form action="{{ route('admin.users.permissions.save', $user) }}" method="POST" class="max-w-4xl space-y-5">
    @csrf @method('PUT')

    <div class="card-flat p-5 bg-mint/20 border-mint">
        <p class="text-sm text-pine/80 leading-relaxed">
            Each permission starts at whatever the <strong>{{ $user->role?->name }}</strong> role grants.
            Switch one to <em>Grant</em> or <em>Revoke</em> to override that for this person only —
            which is how you give one receptionist access to billing without giving it to all of them.
        </p>
    </div>

    @foreach($groups as $group => $permissions)
        <x-ui.panel :title="$group" :padded="false">
            <div class="divide-y divide-pine/6">
                @foreach($permissions as $permission)
                    @php
                        $fromRole = in_array($permission->slug, $rolePermissions, true);
                        $override = $overrides[$permission->slug] ?? null;
                        $state = is_null($override) ? 'inherit' : ($override ? 'grant' : 'revoke');
                    @endphp

                    <div class="flex flex-wrap items-center gap-4 px-5 py-3.5">
                        <div class="flex-1 min-w-[240px]">
                            <p class="text-sm font-medium">{{ $permission->description }}</p>
                            <p class="font-mono text-[11px] text-pine/45 mt-0.5">{{ $permission->slug }}</p>
                        </div>

                        <span class="badge badge-{{ $fromRole ? 'jade' : 'slate' }}">
                            Role: {{ $fromRole ? 'allowed' : 'denied' }}
                        </span>

                        <div class="flex gap-1">
                            @foreach(['inherit' => 'Inherit', 'grant' => 'Grant', 'revoke' => 'Revoke'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="permissions[{{ $permission->id }}]" value="{{ $value }}"
                                           class="sr-only peer" @checked($state === $value)>
                                    <span class="px-3 py-1.5 rounded-lg border border-pine/12 text-xs block transition
                                                 peer-checked:bg-pine peer-checked:text-mint peer-checked:border-pine">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.panel>
    @endforeach

    <div class="flex gap-3">
        <button class="btn btn-primary">Save permissions</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Back to team</a>
    </div>
</form>
@endsection
