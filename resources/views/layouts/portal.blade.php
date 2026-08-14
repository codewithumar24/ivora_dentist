<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body class="console-body text-ink">

@php
    $nav = [
        ['patient.dashboard', 'Overview', 'M3 10.5 10 4l7 6.5V17a1 1 0 0 1-1 1h-4v-5H8v5H4a1 1 0 0 1-1-1z'],
        ['patient.appointments.index', 'Appointments', 'M4 5h12v12H4zM7 3v4M13 3v4M4 9h12'],
        ['patient.records.index', 'Medical records', 'M5 3h10v14H5zM8 7h4M8 10h4M8 13h2'],
        ['patient.prescriptions', 'Prescriptions', 'M6 4h8v12H6zM9 8h2M10 7v4'],
        ['patient.invoices.index', 'Invoices', 'M5 3h10v14l-2.5-1.5L10 17l-2.5-1.5L5 17zM8 7h4M8 10h4'],
        ['patient.notifications', 'Notifications', 'M10 3a4 4 0 0 0-4 4v3l-1.5 3h11L14 10V7a4 4 0 0 0-4-4zM8.5 16a1.5 1.5 0 0 0 3 0'],
        ['patient.profile', 'My details', 'M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM4 17a6 6 0 0 1 12 0'],
    ];
    $unread = auth()->user()->unreadNotifications()->count();
@endphp

<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside data-sidebar class="fixed lg:sticky top-0 left-0 z-50 w-[248px] h-screen shrink-0 bg-pine text-porcelain flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="px-5 h-[68px] flex items-center gap-2.5 border-b border-white/8">
            <span class="w-8 h-8 text-mint"><img src="{{ asset('assets/img/logo.svg') }}" alt="" class="w-full h-full"></span>
            <div class="leading-none">
                <span class="block font-display text-base font-bold">Ivora</span>
                <span class="block font-mono text-[9px] tracking-[.2em] uppercase text-mint/60">Patient portal</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            @foreach($nav as [$route, $label, $path])
                <a href="{{ route($route) }}" class="sidebar-link {{ request()->routeIs(Str::before($route, '.index').'*') || request()->routeIs($route) ? 'is-active' : '' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                    <span class="flex-1">{{ $label }}</span>
                    @if($label === 'Notifications' && $unread)
                        <span class="badge badge-mint !px-1.5 !py-0 text-[10px]">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/8">
            <a href="{{ route('patient.appointments.create') }}" class="btn btn-light w-full btn-sm mb-3">Book a visit</a>
            <a href="{{ route('home') }}" class="sidebar-link text-xs">← Back to the website</a>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col">
        {{-- Top bar --}}
        <header class="sticky top-0 z-40 h-[68px] bg-white/85 backdrop-blur border-b border-pine/8 flex items-center gap-4 px-5">
            <button data-sidebar-toggle class="lg:hidden w-9 h-9 grid place-items-center rounded-lg border border-pine/12">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="font-display text-lg font-semibold truncate">@yield('heading', 'Overview')</h1>
                <p class="text-xs text-pine/55 truncate">@yield('subheading', 'Your care at Ivora Dental Studio')</p>
            </div>

            <div data-dropdown class="relative">
                <button data-dropdown-trigger class="flex items-center gap-2.5 pl-1 pr-3 py-1 rounded-full border border-pine/12 hover:border-jade transition">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                    <span class="hidden sm:block text-sm">{{ Str::before(auth()->user()->name, ' ') }}</span>
                </button>
                <div data-dropdown-menu class="hidden absolute right-0 mt-2 w-56 card-flat shadow-xl p-1.5 z-50">
                    <div class="px-3 py-2.5 border-b border-pine/8">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-pine/55 truncate">{{ auth()->user()->patientProfile?->mrn }}</p>
                    </div>
                    <a href="{{ route('patient.profile') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-mint/30">My details</a>
                    <a href="{{ route('patient.invoices.index') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-mint/30">Invoices</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-rose-50 text-rose-700">Sign out</button>
                    </form>
                </div>
            </div>
        </header>

        @if(!auth()->user()->hasVerifiedEmail())
            <div class="bg-amber-50 border-b border-amber-200 px-5 py-2.5 text-sm flex flex-wrap items-center gap-3">
                <span class="text-amber-900">Your email address is not confirmed yet — reminders may not reach you.</span>
                <form action="{{ route('verification.resend') }}" method="POST">
                    @csrf
                    <button class="underline text-amber-900 font-medium">Send the link again</button>
                </form>
            </div>
        @endif

        @if(session('status'))
            <div data-flash class="mx-5 mt-5 card-flat px-4 py-3 flex items-start gap-3 border-l-4 border-l-jade">
                <p class="text-sm">{{ session('status') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-5 mt-5 card-flat px-4 py-3 border-l-4 border-l-rose-500">
                <ul class="text-sm text-rose-700 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <main class="flex-1 p-5 lg:p-7">
            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/js/ivora.js') }}" defer></script>
@stack('scripts')
</body>
</html>
