<?php

namespace App\Http\Middleware;

use App\Models\Banner;
use App\Services\CartService;
use App\Services\SettingsService;
use App\Services\UiTextService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $cartCount = 0;

        try {
            $cartCount = app(CartService::class)->summary(app(CartService::class)->resolve())['count'] ?? 0;
        } catch (\Throwable) {
            $cartCount = 0;
        }

        return [
            ...parent::share($request),
            'locale' => $locale,
            'dir' => $locale === 'ar' ? 'rtl' : 'ltr',
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'phone' => $request->user()->phone,
                ] : null,
            ],
            'settings' => fn () => app(SettingsService::class)->publicPayload($locale),
            'ui' => fn () => app(UiTextService::class)->forLocale($locale),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'cartCount' => $cartCount,
            'welcomePopup' => fn () => $this->welcomePopupPayload($locale),
        ];
    }

    /** @return array<string, mixed>|null */
    protected function welcomePopupPayload(string $locale): ?array
    {
        try {
            $banner = Banner::query()
                ->where('position', 'welcome_popup')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->first();
        } catch (\Throwable) {
            return null;
        }

        if (! $banner) {
            return null;
        }

        $isEn = $locale === 'en';

        return [
            'enabled' => true,
            'id' => $banner->id,
            'version' => optional($banner->updated_at)->timestamp,
            'title' => $isEn ? ($banner->title_en ?: $banner->title_ar) : $banner->title_ar,
            'subtitle' => $isEn ? ($banner->subtitle_en ?: $banner->subtitle_ar) : $banner->subtitle_ar,
            'cta' => $isEn ? ($banner->cta_text_en ?: $banner->cta_text_ar) : $banner->cta_text_ar,
            'link' => $banner->link,
            'image_mobile' => $banner->image_mobile,
            'image_desktop' => $banner->image_desktop,
        ];
    }
}
