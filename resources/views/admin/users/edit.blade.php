@extends('layouts.console')
@section('heading', 'Edit '.$user->name)
@section('subheading', $user->role?->name)

@section('actions')
    <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-ghost btn-sm">Permissions</a>
@endsection

@section('content')
<form action="{{ route('admin.users.update', $user) }}" method="POST" class="max-w-2xl space-y-5">
    @csrf @method('PUT')

    <x-ui.panel title="Account">
        <div class="space-y-5">
            <x-ui.field label="Full name" name="name" :value="$user->name" required />
            <div class="grid sm:grid-cols-2 gap-5">
                <x-ui.field label="Email" name="email" type="email" :value="$user->email" required />
                <x-ui.field label="Mobile" name="phone" :value="$user->phone" />
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="field-label" for="role_id">Role</label>
                    <select name="role_id" id="role_id" class="field" {{ $user->isOwner() ? 'disabled' : '' }}>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected($user->role_id == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @if($user->isOwner())
                        <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                        <p class="text-xs text-pine/50 mt-1.5">The owner role cannot be changed here.</p>
                    @endif
                </div>
                <div>
                    <label class="field-label" for="status">Status</label>
                    <select name="status" id="status" class="field">
                        @foreach(['active', 'suspended', 'pending'] as $status)
                            <option value="{{ $status }}" @selected($user->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5 pt-5 border-t border-pine/8">
                <div>
                    <label class="field-label" for="password">New password</label>
                    <input type="password" name="password" id="password" class="field">
                    <p class="text-xs text-pine/50 mt-1.5">Leave blank to keep the current one.</p>
                </div>
                <div>
                    <label class="field-label" for="password_confirmation">Confirm</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="field">
                </div>
            </div>
        </div>
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Save account</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>

@unless($user->isOwner())
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="max-w-2xl mt-4"
          data-confirm="Remove this team member's account?">
        @csrf @method('DELETE')
        <button class="btn btn-sm border border-rose-200 text-rose-700 hover:bg-rose-50">Remove account</button>
    </form>
@endunless
@endsection
