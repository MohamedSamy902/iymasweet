<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
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
            --font-heading: '{{ setting('theme.heading_font', 'Cairo') }}', 'Tajawal', ui-sans-serif, system-ui, sans-serif;
            --font-body: '{{ setting('theme.body_font', 'Tajawal') }}', 'Cairo', ui-sans-serif, system-ui, sans-serif;
            --radius-card: 1rem;
            --shadow-soft: 0 12px 28px rgba(228, 120, 157, 0.18);
        }
    </style>

    <link rel="icon" href="/favicon.ico">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/images/logo-placeholder.svg">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="min-h-dvh bg-[var(--color-cream)] text-[var(--color-text)] antialiased font-body">
    @inertia
</body>
</html>
