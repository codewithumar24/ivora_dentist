@extends('layouts.console')
@section('heading', 'Clinic settings')
@section('subheading', 'Everything the website reads from the database')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-5">
    @csrf

    @foreach($groups as $group => $settingItems)
        <x-ui.panel :title="Str::headline($group)">
            <div class="grid sm:grid-cols-2 gap-5">
                @foreach($settingItems as $setting)
                    <div class="{{ in_array($setting->type, ['textarea']) ? 'sm:col-span-2' : '' }}">
                        <label class="field-label" for="s-{{ $setting->key }}">{{ $setting->label ?? Str::headline($setting->key) }}</label>

                        @if($setting->type === 'textarea')
                            <textarea name="settings[{{ $setting->key }}]" id="s-{{ $setting->key }}" rows="3" class="field">{{ $setting->value }}</textarea>
                        @elseif($setting->type === 'boolean')
                            <select name="settings[{{ $setting->key }}]" id="s-{{ $setting->key }}" class="field">
                                <option value="1" @selected($setting->value == '1')>Yes</option>
                                <option value="0" @selected($setting->value != '1')>No</option>
                            </select>
                        @elseif($setting->type === 'image')
                            @if($setting->value)<img src="{{ Str::startsWith($setting->value, 'http') ? $setting->value : asset('storage/'.$setting->value) }}" alt="" class="h-14 mb-2 rounded-lg">@endif
                            <input type="file" name="files[{{ $setting->key }}]" id="s-{{ $setting->key }}" accept="image/*" class="field !py-2 text-sm">
                        @else
                            <input name="settings[{{ $setting->key }}]" id="s-{{ $setting->key }}" value="{{ $setting->value }}" class="field">
                        @endif

                        <p class="font-mono text-[11px] text-pine/40 mt-1.5">{{ $setting->key }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.panel>
    @endforeach

    <button class="btn btn-primary">Save settings</button>
</form>
@endsection
