<?php

use App\Services\SettingsService;
use App\Services\UiTextService;

if (! function_exists('t')) {
    /**
     * Translate a UI text key from the ui_texts table.
     */
    function t(string $key, ?string $default = null, ?string $locale = null): string
    {
        return app(UiTextService::class)->get($key, $default, $locale);
    }
}

if (! function_exists('setting')) {
    /**
     * Read a setting value from the settings table.
     */
    function setting(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        return app(SettingsService::class)->get($key, $default, $locale);
    }
}
