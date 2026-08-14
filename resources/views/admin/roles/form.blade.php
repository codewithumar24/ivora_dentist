@extends('layouts.console')
@section('heading', $role->exists ? 'Edit '.$role->name : 'New role')
@section('subheading', 'Tick what this role can reach')

@section('content')
<form action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
      method="POST" class="max-w-4xl space-y-5">
    @csrf
    @if($role->exists) @method('PUT') @endif

    <x-ui.panel title="Role">
        <div class="grid sm:grid-cols-3 gap-5">
            <x-ui.field label="Name" name="name" :value="$role->name" required class="sm:col-span-2" />
            <div>
                <label class="field-label" for="level">Level</label>
                <select name="level" id="level" class="field">
                    @foreach([10 => 'Patient (10)', 20 => 'Staff (20)', 40 => 'Clinical (40)', 80 => 'Manager (80)'] as $value => $label)
                        <option value="{{ $value }}" @selected($role->level == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-pine/50 mt-1.5">Level 20+ can open the staff console.</p>
            </div>
        </div>
        <x-ui.field label="Description" name="description" :value="$role->description" class="mt-5" />
    </x-ui.panel>

    @foreach($groups as $group => $permissions)
        <x-ui.panel :title="$group">
            <div class="grid sm:grid-cols-2 gap-2.5">
                @foreach($permissions as $permission)
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border border-pine/10 cursor-pointer hover:border-jade transition has-[:checked]:bg-mint/25 has-[:checked]:border-pine">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               @checked(in_array($permission->id, $assigned)) class="w-4 h-4 mt-0.5 rounded border-pine/25 text-pine focus:ring-jade">
                        <span class="min-w-0">
                            <span class="block text-sm">{{ $permission->description }}</span>
                            <span class="block font-mono text-[11px] text-pine/45 mt-0.5">{{ $permission->slug }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </x-ui.panel>
    @endforeach

    <div class="flex gap-3">
        <button class="btn btn-primary">{{ $role->exists ? 'Save role' : 'Create role' }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
