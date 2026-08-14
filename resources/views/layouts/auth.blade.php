<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body class="bg-porcelain text-ink">

<div class="min-h-screen grid lg:grid-cols-[1fr_.85fr]">

    {{-- Form side --}}
    <div class="flex flex-col px-5 sm:px-10 lg:px-16 py-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-pine mb-auto">
            <span class="w-9 h-9"><img src="{{ asset('assets/img/logo-mark.svg') }}" alt="" class="w-full h-full"></span>
            <span class="leading-none">
                <span class="block font-display text-lg font-bold">Ivora</span>
                <span class="block font-mono text-[9px] tracking-[.22em] uppercase opacity-60">Dental Studio</span>
            </span>
        </a>

        <div class="w-full max-w-md mx-auto py-12">
            @if(session('status'))
                <div class="card-flat px-4 py-3 mb-6 border-l-4 border-l-jade text-sm">{{ session('status') }}</div>
            @endif

            @yield('form')
        </div>

        <p class="text-xs text-pine/45 mt-auto text-center lg:text-left">
            &copy; {{ date('Y') }} Ivora Dental Studio · <a href="{{ route('contact') }}" class="underline-grow">Need help?</a>
        </p>
    </div>

    {{-- Brand side --}}
    <div class="hidden lg:block relative on-dark overflow-hidden">
        <div class="grain absolute inset-0"></div>

        <div class="relative h-full flex flex-col justify-center px-14 py-16">
            <div class="arch-frame w-full max-w-sm aspect-[4/5] mb-10 bg-pine/40">
                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=800&q=80" alt="" class="w-full h-full object-cover">
            </div>

            <blockquote class="max-w-md">
                <p class="font-display text-2xl leading-snug text-porcelain">
                    “I have a genuine phobia of dentists. It was the first appointment I have
                    left without shaking.”
                </p>
                <footer class="text-sm text-mint/70 mt-5 font-mono">Rehan M. · root canal patient</footer>
            </blockquote>

            <div class="flex gap-10 mt-12 pt-10 border-t border-white/10">
                @foreach([['12,400+', 'patients'], ['18', 'years'], ['98%', 'would return']] as [$value, $label])
                    <div>
                        <p class="font-display text-2xl font-bold text-mint">{{ $value }}</p>
                        <p class="text-xs text-porcelain/50 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/ivora.js') }}" defer></script>
</body>
</html>
