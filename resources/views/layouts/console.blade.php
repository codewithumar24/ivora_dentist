<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body class="console-body text-ink">

@php
    $user = auth()->user();
    $groups = [
        'Clinic' => [
            ['admin.dashboard', 'Dashboard', null, 'M3 10.5 10 4l7 6.5V17a1 1 0 0 1-1 1h-4v-5H8v5H4a1 1 0 0 1-1-1z'],
            ['admin.appointments.index', 'Appointments', 'appointments.view', 'M4 5h12v12H4zM7 3v4M13 3v4M4 9h12'],
            ['admin.appointments.calendar', 'Diary', 'appointments.view', 'M4 4h12v12H4zM4 8h12M8 12h4'],
            ['admin.patients.index', 'Patients', 'patients.view', 'M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM4 17a6 6 0 0 1 12 0'],
            ['admin.records.index', 'Clinical notes', 'records.view', 'M5 3h10v14H5zM8 7h4M8 10h4M8 13h2'],
        ],
        'Practice' => [
            ['admin.doctors.index', 'Practitioners', 'doctors.view', 'M10 3v4M7 5h6M6 9h8v8H6z'],
            ['admin.billing.index', 'Invoices', 'billing.view', 'M5 3h10v14l-2.5-1.5L10 17l-2.5-1.5L5 17zM8 7h4M8 10h4'],
            ['admin.billing.payments', 'Payments', 'billing.view', 'M3 6h14v9H3zM3 9h14M6 12h3'],
            ['admin.reports', 'Reports', 'billing.view', 'M4 16V8M8 16V4M12 16v-6M16 16v-9'],
        ],
        'Website' => [
            ['admin.content.services', 'Treatments', 'services.manage', 'M10 3 4 6v5c0 3.5 2.6 5.6 6 6.5 3.4-.9 6-3 6-6.5V6z'],
            ['admin.content.posts', 'Journal', 'blog.manage', 'M4 4h12v12H4zM7 8h6M7 11h6'],
            ['admin.content.gallery', 'Gallery', 'gallery.manage', 'M3 5h14v10H3zM3 12l4-4 3 3 2-2 5 5'],
            ['admin.content.testimonials', 'Testimonials', 'gallery.manage', 'M4 4h12v9H8l-4 3z'],
            ['admin.content.reviews', 'Reviews', 'gallery.manage', 'm10 3 2.2 4.5 5 .7-3.6 3.5.9 4.9L10 14.3 5.5 16.6l.9-4.9L2.8 8.2l5-.7z'],
            ['admin.content.faqs', 'FAQs', 'faqs.manage', 'M10 17a7 7 0 1 0 0-14 7 7 0 0 0 0 14zM8.5 8a1.5 1.5 0 1 1 2 1.4v1.1M10 13.5h.01'],
        ],
        'Front desk' => [
            ['admin.inbox.index', 'Enquiries', 'messages.view', 'M3 5h14v10H3zM3 6l7 5 7-5'],
            ['admin.inbox.subscribers', 'Mailing list', 'subscribers.manage', 'M4 6h12v8H4zM7 10h6'],
        ],
        'Administration' => [
            ['admin.users.index', 'Team', 'users.view', 'M7 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM3 16a4 4 0 0 1 8 0M14 9.5a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM12.5 16a3.5 3.5 0 0 1 5 0'],
            ['admin.roles.index', 'Roles & access', 'roles.manage', 'M10 3l6 3v4c0 3.5-2.5 6.2-6 7-3.5-.8-6-3.5-6-7V6z'],
            ['admin.activity.index', 'Audit trail', 'activity.view', 'M3 10h3l2-5 4 10 2-5h3'],
            ['admin.activity.sms', 'SMS outbox', 'activity.view', 'M4 4h12v9H9l-4 3v-3H4z'],
            ['admin.settings', 'Settings', 'settings.manage', 'M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2M15.3 4.7l-1.4 1.4M6.1 13.9l-1.4 1.4M15.3 15.3l-1.4-1.4M6.1 6.1 4.7 4.7'],
        ],
    ];
@endphp

<div class="min-h-screen flex">
    <aside data-sidebar class="fixed lg:sticky top-0 left-0 z-50 w-[252px] h-screen shrink-0 bg-pine text-porcelain flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="px-5 h-[64px] flex items-center gap-2.5 border-b border-white/8 shrink-0">
            <span class="w-8 h-8 text-mint"><img src="{{ asset('assets/img/logo.svg') }}" alt="" class="w-full h-full"></span>
            <div class="leading-none">
                <span class="block font-display text-base font-bold">Ivora</span>
                <span class="block font-mono text-[9px] tracking-[.2em] uppercase text-mint/60">Staff console</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 pb-4">
            @php
                $groupIcons = [
                    'Clinic'         => 'M3 10.5 10 4l7 6.5V17a1 1 0 0 1-1 1h-4v-5H8v5H4a1 1 0 0 1-1-1z',
                    'Practice'       => 'M10 3v4M7 5h6M6 9h8v8H6z',
                    'Website'        => 'M10 17a7 7 0 1 0 0-14 7 7 0 0 0 0 14zM3 10h14M10 3a11 11 0 0 1 0 14 11 11 0 0 1 0-14z',
                    'Front desk'     => 'M3 5h14v10H3zM3 6l7 5 7-5',
                    'Administration' => 'M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2',
                ];
            @endphp

            @foreach($groups as $group => $links)
                @php
                    $visible = collect($links)->filter(fn($l) => !$l[2] || $user->hasPermission($l[2]));

                    // Open the group that owns the page on screen, so nobody lands
                    // on a screen whose menu entry is hidden inside a closed list.
                    $isOpen = $visible->contains(fn($l) => request()->routeIs($l[0]));
                @endphp

                @continue($visible->isEmpty())

                <div class="sidebar-section {{ $isOpen ? 'is-open' : '' }}" data-nav-group>
                    <button type="button" class="sidebar-group-btn" data-nav-toggle
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                        <svg class="w-[17px] h-[17px] shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor"
                             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="{{ $groupIcons[$group] ?? 'M4 10h12' }}"/>
                        </svg>
                        <span class="truncate">{{ $group }}</span>
                        <svg class="sidebar-caret" viewBox="0 0 20 20" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 8 5 5 5-5"/>
                        </svg>
                    </button>

                    <div class="sidebar-sub" data-nav-panel>
                        @foreach($visible as [$route, $label, $permission, $path])
                            <a href="{{ route($route) }}" class="sidebar-link {{ request()->routeIs($route) ? 'is-active' : '' }}">
                                <svg class="w-[16px] h-[16px] shrink-0 opacity-80" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                                <span class="truncate">{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/8 shrink-0">
            <div class="px-2 py-2 mb-2">
                <p class="text-xs text-porcelain/85 truncate">{{ $user->name }}</p>
                <p class="font-mono text-[10px] text-mint/70 uppercase tracking-wider">{{ $user->role?->name }}</p>
            </div>
            <a href="{{ route('home') }}" class="sidebar-link text-xs">← View the website</a>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col">
        <header class="sticky top-0 z-40 h-[64px] bg-white/88 backdrop-blur border-b border-pine/8 flex items-center gap-4 px-5">
            <button data-sidebar-toggle class="lg:hidden w-9 h-9 grid place-items-center rounded-lg border border-pine/12">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>

            <div class="flex-1 min-w-0">
                <h1 class="font-display text-lg font-semibold truncate">@yield('heading', 'Dashboard')</h1>
                <p class="text-xs text-pine/55 truncate">@yield('subheading', now()->format('l j F Y'))</p>
            </div>

            <div class="flex items-center gap-2">
                @yield('actions')

                @permission('appointments.create')
                    <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary btn-sm">New appointment</a>
                @endpermission

                <div data-dropdown class="relative">
                    <button data-dropdown-trigger class="w-9 h-9 rounded-full overflow-hidden border border-pine/12">
                        <img src="{{ $user->avatar_url }}" alt="" class="w-full h-full object-cover">
                    </button>
                    <div data-dropdown-menu class="hidden absolute right-0 mt-2 w-52 card-flat shadow-xl p-1.5 z-50">
                        <div class="px-3 py-2.5 border-b border-pine/8">
                            <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                            <p class="text-xs text-pine/55">{{ $user->role?->name }}</p>
                        </div>
                        @permission('settings.manage')
                            <a href="{{ route('admin.settings') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-mint/30">Clinic settings</a>
                        @endpermission
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-rose-50 text-rose-700">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if(session('status'))
            <div data-flash class="mx-5 mt-5 card-flat px-4 py-3 border-l-4 border-l-jade text-sm">{{ session('status') }}</div>
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
@stack('scripts')
</body>
</html>
