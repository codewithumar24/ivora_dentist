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
                        @elseif($setting->type === 'image' || $setting->type === 'video')
                            @if($setting->value)
                                <div class="mb-2.5 flex items-center gap-3">
                                    @if($setting->type === 'video')
                                        <video src="{{ \App\Models\Setting::media($setting->key) }}" class="h-16 rounded-lg bg-pine" muted playsinline></video>
                                    @else
                                        <img src="{{ \App\Models\Setting::media($setting->key) }}" alt="" class="h-16 rounded-lg border border-pine/10 bg-white object-contain p-1">
                                    @endif

                                    {{-- A wrong upload has to be undoable, or it is permanent. --}}
                                    <label class="inline-flex cursor-pointer items-center gap-1.5 text-xs font-medium text-rose-600">
                                        <input type="checkbox" name="clear[{{ $setting->key }}]" value="1" class="rounded border-pine/25">
                                        Remove
                                    </label>
                                </div>
                            @endif

                            <input type="file" name="files[{{ $setting->key }}]" id="s-{{ $setting->key }}"
                                   accept="{{ $setting->type === 'video' ? 'video/mp4,video/webm' : 'image/*' }}"
                                   class="field !py-2 text-sm">

                            @error('files.'.$setting->key)<p class="field-error">{{ $message }}</p>@enderror

                            <p class="mt-1 text-[11px] text-pine/45">
                                {{ $setting->type === 'video' ? 'MP4 or WebM, up to 20 MB. Keep it short and silent.' : 'JPG, PNG, WebP or SVG, up to 4 MB.' }}
                            </p>
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
