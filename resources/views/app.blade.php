<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="{{ setting('theme.primary', '#F2A0B8') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ setting('general.brand_name', 'Iyma Sweet') }}</title>

    @php
        $themeVars = app(\App\Services\SettingsService::class)->themeCssVariables();
    @endphp
    <style>
        :root {
            @foreach($themeVars as $var => $value)
                {{ $var }}: {{ $value }};
            @endforeach
            --font-heading: 'Baloo Bhaijaan 2', 'Fredoka', 'Cairo', sans-serif;
            --font-body: 'Cairo', 'Tajawal', sans-serif;
            --radius-card: 1.5rem;
            --shadow-soft: 0 10px 30px rgba(242, 160, 184, 0.18);
        }
    </style>

    <link rel="icon" href="/favicon.ico">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="min-h-dvh bg-[var(--color-cream)] text-[var(--color-text)] antialiased font-body">
    @inertia
</body>
</html>
