<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body class="bg-white text-ink">

<div class="min-h-screen grid lg:grid-cols-2">

    {{-- Form column. Crest first, so a patient knows whose door this is
         before reading a word. --}}
    <section class="flex flex-col px-5 py-10 sm:px-10 lg:px-16">
        <div class="w-full max-w-md mx-auto my-auto">
            <a href="{{ route('home') }}" class="enter flex flex-col items-center gap-3 text-center mb-9">
                <img src="{{ \App\Models\Setting::media('logo', 'images/zynera-logo.png') }}"
                     alt="{{ $settings['clinic_name'] ?? 'Dental studio' }}"
                     class="h-20 w-auto object-contain float-slow">
                <span class="font-mono text-[10px] tracking-[.24em] uppercase text-pine/50">
                    {{ $settings['clinic_name'] ?? 'Dental Studio' }}
                </span>
            </a>

            @if(session('status'))
                <p class="auth-note auth-note-ok enter delay-1 mb-6">{{ session('status') }}</p>
            @endif

            @yield('form')
        </div>

        <p class="text-xs text-pine/45 text-center mt-8">
            &copy; {{ date('Y') }} {{ $settings['clinic_name'] ?? 'Dental Studio' }} ·
            <a href="{{ route('contact') }}" class="underline-grow">Need help?</a>
        </p>
    </section>

    {{-- Illustration column. Hidden below lg: on a phone it would push the form
         off the first screen, which is the only thing that matters here. --}}
    <section class="hidden lg:flex relative items-center justify-center overflow-hidden on-dark p-12">
        <div class="grain absolute inset-0"></div>
        <div class="absolute inset-0 opacity-60"
             style="background-image:radial-gradient(circle at 22% 20%, rgba(168,230,216,.18) 0, transparent 46%), radial-gradient(circle at 78% 80%, rgba(28,122,110,.35) 0, transparent 48%)"></div>

        <div class="relative z-10 w-full">
            <div class="enter delay-1 flex justify-center">
                @include('layouts.partials.auth-graphic')
            </div>

            <div class="enter delay-2 max-w-md mx-auto text-center mt-10">
                <h2 class="font-display text-2xl font-bold text-porcelain">Everything on one chart</h2>
                <p class="mt-3 leading-relaxed text-porcelain/80">
                    Appointments, x-rays, treatment plans and invoices stay together, so nobody
                    has to ask you the same question twice.
                </p>
            </div>

            <div class="enter delay-3 flex justify-center gap-10 mt-10 pt-8 border-t border-white/10">
                @foreach([['12,400+', 'patients'], ['18', 'years'], ['98%', 'would return']] as [$value, $label])
                    <div class="text-center">
                        <p class="font-display text-2xl font-bold text-mint">{{ $value }}</p>
                        <p class="text-xs text-porcelain/50 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
</body>
</html>
