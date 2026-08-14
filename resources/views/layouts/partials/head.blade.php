<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', $settings['meta_title'] ?? 'Ivora Dental Studio')</title>
<meta name="description" content="@yield('description', $settings['meta_description'] ?? 'A calm dental studio in Karachi.')">

<meta property="og:title" content="@yield('title', $settings['meta_title'] ?? 'Ivora Dental Studio')">
<meta property="og:description" content="@yield('description', $settings['meta_description'] ?? '')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

<link rel="icon" href="{{ asset('assets/img/logo-mark.svg') }}" type="image/svg+xml">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=Inter+Tight:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com/3.4.3"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          ink: '#0A1614',
          pine: '#10403B',
          jade: '#1C7A6E',
          mint: '#A8E6D8',
          porcelain: '#F4F7F5',
          iris: '#4F5BD5',
        },
        fontFamily: {
          display: ['"Bricolage Grotesque"', 'sans-serif'],
          sans: ['"Inter Tight"', 'system-ui', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace'],
        },
        maxWidth: { shell: '1240px' },
      },
    },
  };
</script>

<link rel="stylesheet" href="{{ asset('assets/css/ivora.css') }}">
@stack('head')
