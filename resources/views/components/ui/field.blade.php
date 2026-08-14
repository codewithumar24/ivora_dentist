@props(['label' => null, 'name' => null, 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="field-label">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
                  {{ $attributes->except('class')->merge(['class' => 'field']) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
               {{ $attributes->except('class')->merge(['class' => 'field']) }}>
    @endif

    @if($hint)<p class="text-xs text-pine/50 mt-1.5">{{ $hint }}</p>@endif
    @error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>
