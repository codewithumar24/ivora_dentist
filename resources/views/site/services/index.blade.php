@extends('layouts.site')
@section('title', 'Treatments — Ivora Dental Studio')

@section('content')
<x-site.page-hero
    eyebrow="Treatments"
    title="Everything we do,<br>and what it costs."
    lede="Filter by speciality, or search for the thing you came here to find."
/>

<section class="py-14">
    <div class="max-w-shell mx-auto px-5">
        <form method="GET" class="flex flex-wrap items-center gap-3 mb-10">
            <div class="reveal flex flex-wrap gap-2 flex-1">
                <a href="{{ route('services.index') }}" class="btn btn-sm {{ !$active ? 'btn-primary' : 'btn-ghost' }}">All</a>
                @foreach($specialities as $speciality)
                    <a href="{{ route('services.index', ['speciality' => $speciality->slug]) }}"
                       class="btn btn-sm {{ $active === $speciality->slug ? 'btn-primary' : 'btn-ghost' }}">{{ $speciality->name }}</a>
                @endforeach
            </div>
            <div class="flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search treatments" class="field !py-2 !text-sm w-52">
                <button class="btn btn-ghost btn-sm">Search</button>
            </div>
        </form>

        @if($services->isEmpty())
            <x-ui.empty title="No treatments matched" message="Try a different speciality, or clear the search." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($services as $service)
                    <x-site.service-card :service="$service" />
                @endforeach
            </div>
            <div class="mt-12">{{ $services->links() }}</div>
        @endif
    </div>
</section>
@endsection
