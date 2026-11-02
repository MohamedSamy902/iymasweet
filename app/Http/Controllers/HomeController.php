<?php

namespace App\Http\Controllers;

use App\Enums\AvailabilityType;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Review;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $card = fn (Product $p) => [
            'id' => $p->id,
            'slug' => $p->slug,
            'name_ar' => $p->name_ar,
            'base_price' => (float) $p->base_price,
            'compare_at_price' => $p->compare_at_price ? (float) $p->compare_at_price : null,
            'availability_type' => $p->availability_type->value,
            'availability_label' => $p->availability_type->labelAr(),
            'avg_rating' => (float) $p->avg_rating,
            'image' => $p->primaryImage()?->path ?? '/images/logo-placeholder.svg',
        ];

        return Inertia::render('Home', [
            'sections' => HomeSection::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->get(['id', 'slug', 'name_ar', 'color', 'image']),
            'statusProducts' => [
                'available_now' => Product::query()->with('images')->where('is_active', true)->where('availability_type', AvailabilityType::AvailableNow)->limit(8)->get()->map($card),
                'available_tomorrow' => Product::query()->with('images')->where('is_active', true)->where('availability_type', AvailabilityType::AvailableTomorrow)->limit(8)->get()->map($card),
                'pre_order' => Product::query()->with('images')->where('is_active', true)->where('availability_type', AvailabilityType::PreOrder)->limit(8)->get()->map($card),
                'sold_out' => Product::query()->with('images')->where('is_active', true)->where('availability_type', AvailabilityType::SoldOut)->limit(8)->get()->map($card),
            ],
            'featured' => Product::query()->with('images')->where('is_active', true)->where('is_featured', true)->limit(8)->get()->map($card),
            'bundles' => Bundle::query()->where('is_active', true)->orderBy('sort_order')->limit(4)->get(['slug', 'name_ar', 'price', 'compare_at_price', 'image', 'type']),
            'reviews' => Review::query()->with(['user:id,name', 'product:id,name_ar,slug'])->where('status', 'approved')->latest()->limit(6)->get(),
            'faqs' => Faq::query()->where('is_active', true)->orderBy('sort_order')->limit(5)->get(),
            'howSteps' => [
                ['icon' => 'donut', 'title_key' => 'home.step_pick', 'fallback' => 'اختاري المنتج'],
                ['icon' => 'calendar', 'title_key' => 'home.step_slot', 'fallback' => 'احجزي ميعاد'],
                ['icon' => 'wallet', 'title_key' => 'home.step_pay', 'fallback' => 'ادفعي براحتك'],
                ['icon' => 'bike', 'title_key' => 'home.step_receive', 'fallback' => 'استلمي الطلب'],
            ],
        ]);
    }
}
