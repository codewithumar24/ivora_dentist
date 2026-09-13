<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', $settings['meta_title'] ?? 'Zynera Dental Solutions')</title>
<meta name="description" content="@yield('description', $settings['meta_description'] ?? 'A calm dental studio in Karachi.')">

<meta property="og:title" content="@yield('title', $settings['meta_title'] ?? 'zynera Dental Studio')">
<meta property="og:description" content="@yield('description', $settings['meta_description'] ?? '')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:image" content="{{ \App\Models\Setting::media('og_image', 'images/home.jpg') }}">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" href="{{ \App\Models\Setting::media('favicon', 'images/zynera-logo-2.png') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('head')
