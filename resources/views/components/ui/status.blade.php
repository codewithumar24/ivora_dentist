@props(['status' => 'pending'])

@php
    $meta = config("clinic.appointment_statuses.{$status}", ['label' => ucfirst(str_replace('_',' ', $status)), 'tone' => 'slate']);
@endphp

<span {{ $attributes->merge(['class' => 'badge badge-'.$meta['tone']]) }}>{{ $meta['label'] }}</span>
