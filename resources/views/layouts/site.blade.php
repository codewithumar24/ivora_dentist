<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    @include('layouts.partials.head')
    <style>
        [data-header] { transition: transform .4s cubic-bezier(.22,1,.36,1), background-color .4s, box-shadow .4s; }
        [data-header].is-stuck { background: rgba(244,247,245,.86); backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(16,64,59,.08); }
        [data-header].is-hidden { transform: translateY(-100%); }
    </style>
</head>
<body class="bg-porcelain text-ink antialiased">

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] btn btn-primary">Skip to content</a>

{{-- ── Announcement strip ─────────────────────────────────────────────── --}}
<div class="bg-pine text-porcelain text-xs">
    <div class="max-w-shell mx-auto px-5 py-2 flex items-center justify-between gap-4">
        <p class="flex items-center gap-2 font-mono tracking-wide">
            <span class="pulse-dot inline-block w-1.5 h-1.5 rounded-full bg-mint"></span>
            Same-week appointments available
        </p>
        <div class="hidden sm:flex items-center gap-5">
            <a href="tel:{{ $settings['phone'] ?? '' }}" class="underline-grow">{{ $settings['phone'] ?? '' }}</a>
            <span class="opacity-50">·</span>
            <span class="opacity-80">{{ $settings['hours_weekday'] ?? '' }} Mon–Fri</span>
        </div>
    </div>
</div>

{{-- ── Header ─────────────────────────────────────────────────────────── --}}
<header data-header class="sticky top-0 z-50">
    <div class="max-w-shell mx-auto px-5">
        <nav class="flex items-center justify-between h-[74px]" aria-label="Main">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-pine">
                <span class="w-9 h-9"><img src="{{ asset('assets/img/logo-mark.svg') }}" alt="" class="w-full h-full"></span>
                <span class="leading-none">
                    <span class="block font-display text-lg font-bold tracking-tight">Ivora</span>
                    <span class="block font-mono text-[9px] tracking-[.22em] uppercase opacity-60">Dental Studio</span>
                </span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-sm">
                <div class="relative group">
                    <a href="{{ route('services.index') }}" class="flex items-center gap-1 py-6 hover:text-jade transition">
                        Treatments
                        <svg class="w-3 h-3 opacity-50" viewBox="0 0 12 12" fill="none"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </a>
                    <div class="absolute left-1/2 -translate-x-1/2 top-full w-[420px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                        <div class="card-flat p-3 shadow-xl grid grid-cols-2 gap-1">
                            @foreach($navServices as $item)
                                <a href="{{ route('services.show', $item->slug) }}" class="px-3 py-2.5 rounded-lg hover:bg-mint/40 transition text-[13px]">{{ $item->name }}</a>
                            @endforeach
                            <a href="{{ route('services.index') }}" class="col-span-2 px-3 py-2.5 rounded-lg bg-pine text-porcelain text-[13px] text-center mt-1">All treatments &amp; prices</a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('doctors.index') }}" class="hover:text-jade transition">Dentists</a>
                <a href="{{ route('pricing') }}" class="hover:text-jade transition">Pricing</a>
                <a href="{{ route('about') }}" class="hover:text-jade transition">About</a>
                <a href="{{ route('blog.index') }}" class="hover:text-jade transition">Journal</a>
                <a href="{{ route('contact') }}" class="hover:text-jade transition">Contact</a>
            </div>

            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ auth()->user()->canAccessConsole() ? route('admin.dashboard') : route('patient.dashboard') }}"
                       class="hidden sm:inline-flex btn btn-ghost btn-sm">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-5 h-5 rounded-full object-cover">
                        {{ Str::before(auth()->user()->name, ' ') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex btn btn-ghost btn-sm">Patient login</a>
                @endauth
                <a href="{{ route('book') }}" class="btn btn-primary btn-sm">Book a visit</a>

                <button data-nav-toggle aria-expanded="false" aria-label="Menu"
                        class="lg:hidden w-10 h-10 grid place-items-center rounded-full border border-pine/15">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none"><path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
            </div>
        </nav>
    </div>

    <div data-nav-panel class="hidden lg:hidden fixed inset-0 top-[74px] bg-porcelain z-40 overflow-y-auto">
        <div class="px-5 py-8 space-y-1">
            @foreach([['Treatments', route('services.index')], ['Dentists', route('doctors.index')], ['Pricing', route('pricing')], ['About', route('about')], ['Gallery', route('gallery')], ['Journal', route('blog.index')], ['FAQ', route('faq')], ['Contact', route('contact')]] as [$label, $url])
                <a href="{{ $url }}" class="block py-3.5 text-2xl font-display border-b border-pine/8">{{ $label }}</a>
            @endforeach
            <div class="pt-6 flex flex-col gap-3">
                <a href="{{ route('book') }}" class="btn btn-primary w-full">Book a visit</a>
                @auth
                    <a href="{{ auth()->user()->canAccessConsole() ? route('admin.dashboard') : route('patient.dashboard') }}" class="btn btn-ghost w-full">My dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost w-full">Patient login</a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- ── Flash ──────────────────────────────────────────────────────────── --}}
@if(session('status'))
    <div data-flash class="fixed top-24 right-5 z-[60] max-w-sm card-flat px-4 py-3 shadow-xl flex items-start gap-3">
        <span class="w-6 h-6 rounded-full bg-mint grid place-items-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5 text-pine" viewBox="0 0 14 14" fill="none"><path d="M3 7.5 5.5 10 11 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="text-sm leading-relaxed">{{ session('status') }}</p>
    </div>
@endif

<main id="main">
    @yield('content')
</main>

{{-- ── Footer ─────────────────────────────────────────────────────────── --}}
<footer class="on-dark relative overflow-hidden mt-24">
    <div class="grain absolute inset-0"></div>

    <div class="relative max-w-shell mx-auto px-5 pt-20 pb-10">
        <div class="grid lg:grid-cols-[1.4fr_1fr_1fr_1.2fr] gap-12 mb-16">
            <div>
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="w-9 h-9 text-mint"><img src="{{ asset('assets/img/logo.svg') }}" alt="" class="w-full h-full"></span>
                    <span class="font-display text-xl font-bold">Ivora Dental Studio</span>
                </div>
                <p class="text-porcelain/70 text-sm leading-relaxed max-w-sm">
                    {{ $settings['tagline'] ?? 'Quiet, careful dentistry' }}. {{ $settings['address'] ?? '' }}
                </p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-7 max-w-sm">
                    @csrf
                    <label class="block text-xs font-mono tracking-widest uppercase text-mint/70 mb-2">One email a month</label>
                    <div class="flex gap-2">
                        <input type="email" name="email" required placeholder="you@email.com"
                               class="flex-1 px-4 py-2.5 rounded-full bg-white/10 border border-white/15 text-sm text-porcelain placeholder:text-porcelain/40 focus:outline-none focus:border-mint">
                        <button class="btn btn-light btn-sm">Join</button>
                    </div>
                </form>
            </div>

            <div>
                <h3 class="eyebrow text-mint/70 mb-4">Treatments</h3>
                <ul class="space-y-2.5 text-sm text-porcelain/75">
                    @foreach($navServices->take(6) as $item)
                        <li><a href="{{ route('services.show', $item->slug) }}" class="hover:text-mint transition">{{ $item->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="eyebrow text-mint/70 mb-4">Practice</h3>
                <ul class="space-y-2.5 text-sm text-porcelain/75">
                    <li><a href="{{ route('about') }}" class="hover:text-mint transition">About Ivora</a></li>
                    <li><a href="{{ route('doctors.index') }}" class="hover:text-mint transition">Our dentists</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-mint transition">Pricing</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-mint transition">Gallery</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-mint transition">Journal</a></li>
                    <li><a href="{{ route('testimonials') }}" class="hover:text-mint transition">Patient reviews</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-mint transition">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="eyebrow text-mint/70 mb-4">Visit us</h3>
                <ul class="space-y-3.5 text-sm text-porcelain/75">
                    <li><a href="tel:{{ $settings['phone'] ?? '' }}" class="hover:text-mint transition font-mono">{{ $settings['phone'] ?? '' }}</a></li>
                    <li><a href="mailto:{{ $settings['email'] ?? '' }}" class="hover:text-mint transition">{{ $settings['email'] ?? '' }}</a></li>
                    <li class="pt-2 border-t border-white/10">
                        <p class="flex justify-between"><span>Mon – Fri</span><span class="font-mono text-porcelain/60">{{ $settings['hours_weekday'] ?? '' }}</span></p>
                        <p class="flex justify-between mt-1"><span>Saturday</span><span class="font-mono text-porcelain/60">{{ $settings['hours_saturday'] ?? '' }}</span></p>
                        <p class="flex justify-between mt-1"><span>Sunday</span><span class="font-mono text-porcelain/60">{{ $settings['hours_sunday'] ?? '' }}</span></p>
                    </li>
                    <li class="pt-2">
                        <span class="badge badge-mint">Emergency {{ $settings['emergency_phone'] ?? '' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-porcelain/50">
            <p>&copy; {{ date('Y') }} Ivora Dental Studio. All rights reserved.</p>
            <div class="flex items-center gap-5">
                @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube'] as $key => $label)
                    @if(!empty($settings[$key]))
                        <a href="{{ $settings[$key] }}" target="_blank" rel="noopener" class="hover:text-mint transition">{{ $label }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</footer>

<script src="{{ asset('assets/js/ivora.js') }}" defer></script>
@stack('scripts')
</body>
</html>
