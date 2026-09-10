{{--
    Sign-in panel illustration: the six things a patient record ties together,
    orbiting the chart itself. Purely decorative — hidden from assistive tech,
    and every animation is switched off under prefers-reduced-motion.
--}}
<svg viewBox="0 0 520 560" class="w-full max-w-lg h-full" role="presentation" aria-hidden="true"
     xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="pillGrad" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stop-color="#1C7A6E"/>
            <stop offset="100%" stop-color="#2FA093"/>
        </linearGradient>
        <linearGradient id="coreGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#0A1614"/>
            <stop offset="100%" stop-color="#10403B"/>
        </linearGradient>
    </defs>

    <g stroke="#A8E6D8" stroke-opacity=".45" stroke-width="1.6" fill="none">
        @foreach ([
            'M188 104 L188 216', 'M300 176 L246 238', 'M330 252 L268 270',
            'M330 352 L268 302', 'M300 432 L246 338', 'M188 472 L188 358',
        ] as $i => $d)
            <path d="{{ $d }}" class="node-line" style="animation-delay: {{ 0.15 * $i }}s"/>
        @endforeach
    </g>

    {{-- The chart at the centre, drawn as a tooth --}}
    <circle cx="188" cy="287" r="88" fill="#A8E6D8" fill-opacity=".28" class="node-pulse"/>
    <circle cx="188" cy="287" r="72" fill="url(#coreGrad)"/>

    <path d="M188 248c-9-7-19-9-27-4-11 7-13 24-8 43 4 15 7 27 9 38 2 9 5 14 10 14s8-5 10-14c2-11 5-23 9-38 5-19 3-36-8-43-8-5-18-3-27 4z"
          transform="translate(0 -4)" fill="#fff" fill-opacity=".95"/>

    <text x="188" y="345" text-anchor="middle" fill="#A8E6D8"
          font-family="JetBrains Mono, monospace" font-size="11" letter-spacing="2.4">ONE CHART</text>

    @foreach ([
        [96,  74,  'Appointments'],
        [212, 146, 'Treatment plan'],
        [258, 228, 'X-rays & notes'],
        [258, 328, 'Invoices'],
        [212, 408, 'Prescriptions'],
        [96,  446, 'Recall reminders'],
    ] as $i => [$x, $y, $label])
        <g class="node-drift" style="animation-delay: {{ 0.4 * $i }}s">
            <rect x="{{ $x }}" y="{{ $y }}" width="204" height="52" rx="26" fill="url(#pillGrad)"/>
            <circle cx="{{ $x + 27 }}" cy="{{ $y + 26 }}" r="15" fill="#fff" fill-opacity=".22"/>
            <text x="{{ $x + 54 }}" y="{{ $y + 32 }}" fill="#fff"
                  font-family="Inter Tight, sans-serif" font-size="15" font-weight="600">{{ $label }}</text>
        </g>
    @endforeach
</svg>
