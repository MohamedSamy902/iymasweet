<?php

namespace App\Services;

use App\Models\UiText;
use Illuminate\Support\Facades\Cache;

class UiTextService
{
    public const CACHE_KEY = 'app.ui_texts.all';

    /** @return array<string, array{ar: string, en: ?string}> */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $texts = [];

            UiText::query()->get()->each(function (UiText $text) use (&$texts) {
                $texts[$text->fullKey()] = [
                    'ar' => $text->value_ar,
                    'en' => $text->value_en,
                ];
            });

            return $texts;
        });
    }

    public function get(string $key, ?string $default = null, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $all = $this->all();

        if (! isset($all[$key])) {
            return $default ?? $key;
        }

        if ($locale === 'en') {
            return $all[$key]['en'] ?: ($all[$key]['ar'] ?? ($default ?? $key));
        }

        return $all[$key]['ar'] ?? ($default ?? $key);
    }

    /** @return array<string, string> */
    public function forLocale(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $result = [];

        foreach ($this->all() as $key => $values) {
            $result[$key] = $locale === 'en'
                ? ($values['en'] ?: $values['ar'])
                : $values['ar'];
        }

        return $result;
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
