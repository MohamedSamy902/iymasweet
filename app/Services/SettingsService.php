<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public const CACHE_KEY = 'app.settings.all';

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = [];

            Setting::query()->get()->each(function (Setting $setting) use (&$settings) {
                $settings["{$setting->group}.{$setting->key}"] = [
                    'group' => $setting->group,
                    'key' => $setting->key,
                    'type' => $setting->type,
                    'is_translatable' => $setting->is_translatable,
                    'value' => $setting->value,
                ];
            });

            return $settings;
        });
    }

    public function get(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        $all = $this->all();
        $item = $all[$key] ?? null;

        if ($item === null) {
            return $default;
        }

        $value = $item['value'];
        $locale ??= app()->getLocale();

        if ($item['is_translatable'] && is_array($value)) {
            return $value[$locale] ?? $value['ar'] ?? $value['en'] ?? $default;
        }

        if (is_array($value) && array_key_exists('v', $value)) {
            return $value['v'];
        }

        return $value ?? $default;
    }

    public function set(string $group, string $key, mixed $value, string $type = 'string', bool $isTranslatable = false): Setting
    {
        $stored = $isTranslatable
            ? (is_array($value) ? $value : ['ar' => $value])
            : ['v' => $value];

        $setting = Setting::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $stored,
                'type' => $type,
                'is_translatable' => $isTranslatable,
            ]
        );

        $this->forgetCache();

        return $setting;
    }

    /** @return array<string, string> */
    public function themeCssVariables(): array
    {
        $defaults = config('iyma.theme_defaults', []);
        $vars = [];

        foreach ($defaults as $cssVar => $default) {
            $key = $this->cssVarToKey($cssVar);
            $vars[$cssVar] = (string) $this->get("theme.{$key}", $default);
        }

        return $vars;
    }

    public function cssVarToKey(string $cssVar): string
    {
        return str_replace('--color-', '', $cssVar);
    }

    /** @return array<string, mixed> */
    public function publicPayload(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return [
            'brand' => [
                'name' => $this->get('general.brand_name', 'Iyma Sweet', $locale),
                'tagline' => $this->get('general.tagline', 'Handmade with love', $locale),
                'logo' => $this->get('general.logo'),
                'favicon' => $this->get('general.favicon'),
                'phone' => $this->get('general.phone'),
                'whatsapp' => $this->get('general.whatsapp'),
                'email' => $this->get('general.email'),
                'currency' => $this->get('general.currency', 'EGP'),
                'currency_symbol' => $this->get('general.currency_symbol', 'ج.م'),
            ],
            'theme' => $this->themeCssVariables(),
            'features' => [
                'english_enabled' => (bool) $this->get('general.english_enabled', true),
                'guest_checkout' => (bool) $this->get('store.guest_checkout', true),
                'store_closed' => (bool) $this->get('store.closed', false),
                'store_closed_message' => $this->get('store.closed_message', null, $locale),
                'show_sparkles' => (bool) $this->get('theme.show_sparkles', true),
            ],
            'announcement' => [
                'enabled' => (bool) $this->get('content.announcement_enabled', true),
                'text' => $this->get('content.announcement_text', null, $locale),
            ],
        ];
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
