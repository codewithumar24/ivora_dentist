<svg viewBox="0 0 540 580" class="w-full max-w-xl h-full" role="presentation" aria-hidden="true"
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

    {{-- Connecting lines: Circle edge se Badge ke edge tak correctly connected --}}
    <g stroke="#A8E6D8" stroke-opacity=".4" stroke-width="1.8" fill="none">
        <path d="M180 200 L180 94" class="node-line"/>
        <path d="M228 220 L270 174" class="node-line"/>
        <path d="M250 270 L300 254" class="node-line"/>
        <path d="M250 310 L300 334" class="node-line"/>
        <path d="M228 360 L270 414" class="node-line"/>
        <path d="M180 380 L180 494" class="node-line"/>
    </g>

   {{-- Center Logo Core (White Background) --}}
<circle cx="180" cy="290" r="82" fill="#A8E6D8" fill-opacity=".22" class="node-pulse"/>

{{-- Dark gradient circle ki jagah solid white circle --}}
<circle cx="180" cy="290" r="68" fill="#FFFFFF" stroke="#A8E6D8" stroke-width="2"/>

{{-- Zynera Logo --}}
<image href="{{ \App\Models\Setting::media('logo', 'images/zynera-logo.png') }}" 
       x="130" 
       y="240" 
       width="100" 
       height="100" 
       preserveAspectRatio="xMidYMid meet" />

    {{-- Right & Center Aligned Badges --}}
    @php
        $nodes = [
            ['x' => 90,  'y' => 40,  'label' => 'Appointments',  'icon' => 'calendar'],
            ['x' => 270, 'y' => 120, 'label' => 'Treatment plan', 'icon' => 'clipboard'],
            ['x' => 300, 'y' => 200, 'label' => 'X-rays & notes',  'icon' => 'document'],
            ['x' => 300, 'y' => 320, 'label' => 'Invoices',        'icon' => 'receipt'],
            ['x' => 270, 'y' => 400, 'label' => 'Prescriptions',   'icon' => 'pill'],
            ['x' => 90,  'y' => 480, 'label' => 'Recall reminders', 'icon' => 'bell'],
        ];
    @endphp

    @foreach ($nodes as $i => $node)
        <g class="node-drift" style="animation-delay: {{ 0.25 * $i }}s">
            <rect x="{{ $node['x'] }}" y="{{ $node['y'] }}" width="210" height="50" rx="25" fill="url(#pillGrad)"/>
            <circle cx="{{ $node['x'] + 25 }}" cy="{{ $node['y'] + 25 }}" r="15" fill="#fff" fill-opacity=".2"/>

            <g transform="translate({{ $node['x'] + 15 }}, {{ $node['y'] + 15 }})" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none">
                @if($node['icon'] === 'calendar')
                    <rect x="3" y="4" width="14" height="13" rx="2"/><path d="M6 2v3M14 2v3M3 9h14"/>
                @elseif($node['icon'] === 'clipboard')
                    <path d="M13 2H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/><path d="M8 7h4M8 11h4"/>
                @elseif($node['icon'] === 'document')
                    <path d="M12 2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V6z"/><path d="M11 2v4h4"/>
                @elseif($node['icon'] === 'receipt')
                    <path d="M4 2v15l3-2 3 2 3-2 3 2V2l-3 2-3-2-3 2z"/><path d="M7 6h6M7 10h6"/>
                @elseif($node['icon'] === 'pill')
                    <path d="M10.5 4.5a4.5 4.5 0 0 1 6.36 6.36l-5.5 5.5a4.5 4.5 0 0 1-6.36-6.36l5.5-5.5z"/><path d="M8.5 8.5l3 3"/>
                @elseif($node['icon'] === 'bell')
                    <path d="M16 7A5 5 0 0 0 6 7c0 6-2.5 8-2.5 8h15S16 13 16 7"/><path d="M12 18a2 2 0 0 1-2-2"/>
                @endif
            </g>

            <text x="{{ $node['x'] + 52 }}" y="{{ $node['y'] + 30 }}" fill="#fff"
                  font-family="Inter Tight, sans-serif" font-size="14" font-weight="600">{{ $node['label'] }}</text>
        </g>
    @endforeach
</svg>