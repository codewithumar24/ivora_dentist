@extends('layouts.console')
@section('heading', 'Add a team member')
@section('subheading', 'They are emailed a login straight away')

@section('content')
<form action="{{ route('admin.users.store') }}" method="POST" class="max-w-2xl space-y-5">
    @csrf

    <x-ui.panel title="Account">
        <div class="space-y-5">
            <x-ui.field label="Full name" name="name" required />
            <div class="grid sm:grid-cols-2 gap-5">
                <x-ui.field label="Email" name="email" type="email" required />
                <x-ui.field label="Mobile" name="phone" />
            </div>

            <div>
                <label class="field-label" for="role_id">Role <span class="text-rose-500">*</span></label>
                <select name="role_id" id="role_id" class="field" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }} — {{ $role->description }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-pine/50 mt-1.5">
                    The role sets their baseline permissions. Fine-tune them per person afterwards on the access screen.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="field-label" for="password">Password</label>
                    <input type="password" name="password" id="password" class="field">
                    <p class="text-xs text-pine/50 mt-1.5">Leave blank and we generate one.</p>
                </div>
                <div>
                    <label class="field-label" for="password_confirmation">Confirm</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="field">
                </div>
            </div>
        </div>
    </x-ui.panel>

    <div class="flex gap-3">
        <button class="btn btn-primary">Create account</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
