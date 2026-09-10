@extends('layouts.site')
@section('title', 'Our dentists — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="The team"
    title="Named clinicians,<br>declared specialities."
    lede="You book with a person and you see that person. Every profile shows their next free slot."
/>

<section class="py-14">
    <div class="max-w-shell mx-auto px-5">
        <div class="reveal flex flex-wrap gap-2 mb-10">
            <a href="{{ route('doctors.index') }}" class="btn btn-sm {{ !$active ? 'btn-primary' : 'btn-ghost' }}">Everyone</a>
            @foreach($specialities as $speciality)
                <a href="{{ route('doctors.index', ['speciality' => $speciality->slug]) }}"
                   class="btn btn-sm {{ $active === $speciality->slug ? 'btn-primary' : 'btn-ghost' }}">{{ $speciality->name }}</a>
            @endforeach
        </div>

        @if($doctors->isEmpty())
            <x-ui.empty title="Nobody matched that filter" message="Try another speciality." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($doctors as $doctor)
                    <x-site.doctor-card :doctor="$doctor" data-delay="{{ ($loop->index % 3) * 90 }}" />
                @endforeach
            </div>
            <div class="mt-12">{{ $doctors->links() }}</div>
        @endif
    </div>
</section>
@endsection
