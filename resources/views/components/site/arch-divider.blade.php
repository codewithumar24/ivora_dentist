@props(['from' => '#F4F7F5', 'to' => '#10403B', 'flip' => false])

{{-- The arch curve used as a section transition — Ivora's shape language. --}}
<div class="arch-divider {{ $flip ? 'rotate-180' : '' }}" style="background: {{ $from }}" aria-hidden="true">
    <svg viewBox="0 0 1440 90" preserveAspectRatio="none">
        <path d="M0 90V62c240-52 480-62 720-62s480 10 720 62v28z" fill="{{ $to }}"/>
    </svg>
</div>
