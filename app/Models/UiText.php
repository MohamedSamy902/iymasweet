<?php

namespace App\Models;

use App\Services\UiTextService;
use Illuminate\Database\Eloquent\Model;

class UiText extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value_ar',
        'value_en',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => app(UiTextService::class)->forgetCache());
        static::deleted(fn () => app(UiTextService::class)->forgetCache());
    }

    public function fullKey(): string
    {
        return "{$this->group}.{$this->key}";
    }

    public function translate(string $locale = 'ar'): string
    {
        return $locale === 'en'
            ? ($this->value_en ?: $this->value_ar)
            : $this->value_ar;
    }
}
