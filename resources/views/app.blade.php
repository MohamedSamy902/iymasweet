<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="{{ setting('theme.primary-soft', '#FFF5F8') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
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
            --font-heading: '{{ setting('theme.heading_font', 'Baloo Bhaijaan 2') }}', 'Cairo', ui-sans-serif, system-ui, sans-serif;
            --font-display: '{{ setting('theme.heading_font', 'Baloo Bhaijaan 2') }}', 'Cairo', ui-sans-serif, system-ui, sans-serif;
            --font-body: '{{ setting('theme.body_font', 'Cairo') }}', ui-sans-serif, system-ui, sans-serif;
        }
    </style>

    <link rel="preload" href="/fonts/cairo-arabic-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/baloo-bhaijaan-2-arabic-700.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="icon" href="/favicon.ico">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/images/logo-placeholder.svg">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="min-h-dvh bg-[var(--color-bg)] text-[var(--color-text)] antialiased font-body">
    <a href="#main" class="skip-link">{{ __('تخطي إلى المحتوى') }}</a>
    @inertia
</body>
</html>
