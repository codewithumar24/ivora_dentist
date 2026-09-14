@props([
    'label' => null, 'name', 'value' => null, 'type' => 'text',
    'hint' => null, 'required' => false, 'placeholder' => null,
])

@php $isPassword = $type === 'password'; @endphp

<div>
    @if($label)
        <label for="{{ $name }}" class="field-label">
            {{ $label }}@if($required)<span class="text-rose-600" aria-hidden="true"> *</span>@endif
        </label>
    @endif

    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @error($name) aria-invalid="true" @enderror
            {{ $attributes->merge(['class' => 'auth-field ' . ($isPassword ? 'auth-field-pw' : '')]) }}
        >

        @if($isPassword)
            {{-- Wired up by ivora.js. Rendered by the server rather than injected
                 so it is present before JS runs; without JS it simply does nothing. --}}
            <button type="button" class="auth-eye" data-toggle-password="{{ $name }}"
                    aria-label="Show password" aria-pressed="false">
                <svg data-eye-open class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg data-eye-shut class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 6.2A9.9 9.9 0 0 1 12 6c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.3 4.2"/>
                    <path d="M6.6 6.8A17.4 17.4 0 0 0 2 13s3.6 7 10 7a9.7 9.7 0 0 0 4.2-.9"/>
                    <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/>
                </svg>
            </button>
        @endif
    </div>

    @error($name)<p class="field-error">{{ $message }}</p>@enderror
    @if($hint && ! $errors->has($name))<p class="mt-1.5 text-xs text-pine/55">{{ $hint }}</p>@endif
</div>
