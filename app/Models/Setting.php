<?php

namespace App\Models;

use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_translatable',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'is_translatable' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => app(SettingsService::class)->forgetCache());
        static::deleted(fn () => app(SettingsService::class)->forgetCache());
    }

    public function getDecodedValue(string $locale = 'ar'): mixed
    {
        $value = $this->value;

        if ($this->is_translatable && is_array($value)) {
            return $value[$locale] ?? $value['ar'] ?? $value['en'] ?? null;
        }

        return is_array($value) && array_key_exists('v', $value)
            ? $value['v']
            : $value;
    }
}
