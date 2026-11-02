<?php

namespace Database\Seeders;

use App\Enums\AvailabilityType;
use App\Models\AddonGroup;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Services\ProductAvailabilityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['donuts', 'دونتس', 'Donuts', '#F2A0B8'],
            ['cakes', 'تورتات', 'Cakes', '#6F93B8'],
            ['cookies', 'كوكيز', 'Cookies', '#F9DC8B'],
            ['boxes', 'بوكسات هدايا', 'Gift boxes', '#C9B6E4'],
        ];

        $categories = [];
        foreach ($cats as $i => [$slug, $ar, $en, $color]) {
            $categories[$slug] = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name_ar' => $ar,
                    'name_en' => $en,
                    'color' => $color,
                    'sort_order' => $i,
                    'is_active' => true,
                    'is_featured' => true,
                ]
            );
        }

        $flavor = Attribute::query()->updateOrCreate(
            ['key' => 'flavor'],
            ['name_ar' => 'النكهة', 'name_en' => 'Flavor', 'type' => 'select', 'is_filterable' => true, 'sort_order' => 1]
        );
        $occasion = Attribute::query()->updateOrCreate(
            ['key' => 'occasion'],
            ['name_ar' => 'المناسبة', 'name_en' => 'Occasion', 'type' => 'select', 'is_filterable' => true, 'sort_order' => 2]
        );
        $diet = Attribute::query()->updateOrCreate(
            ['key' => 'diet'],
            ['name_ar' => 'النوع', 'name_en' => 'Type', 'type' => 'select', 'is_filterable' => true, 'sort_order' => 3]
        );

        $flavorValues = [];
        foreach ([['chocolate', 'شوكولاتة'], ['vanilla', 'فانيليا'], ['strawberry', 'فراولة'], ['lotus', 'لوتس'], ['nutella', 'نوتيلا']] as $i => [$key, $ar]) {
            $flavorValues[$key] = AttributeValue::query()->updateOrCreate(
                ['attribute_id' => $flavor->id, 'value_ar' => $ar],
                ['value_en' => ucfirst($key), 'sort_order' => $i]
            );
        }

        foreach ([['birthday', 'عيد ميلاد'], ['engagement', 'خطوبة'], ['gift', 'هدية'], ['home', 'جلسة بيت']] as $i => [$key, $ar]) {
            AttributeValue::query()->updateOrCreate(
                ['attribute_id' => $occasion->id, 'value_ar' => $ar],
                ['value_en' => $key, 'sort_order' => $i]
            );
        }

        foreach ([['regular', 'عادي'], ['diet', 'دايت'], ['sugar_free', 'خالي من السكر'], ['vegan', 'نباتي']] as $i => [$key, $ar]) {
            AttributeValue::query()->updateOrCreate(
                ['attribute_id' => $diet->id, 'value_ar' => $ar],
                ['value_en' => $key, 'sort_order' => $i]
            );
        }

        $tagNew = Tag::query()->updateOrCreate(['slug' => 'new'], ['name_ar' => 'جديد', 'name_en' => 'New']);
        $tagBest = Tag::query()->updateOrCreate(['slug' => 'bestseller'], ['name_ar' => 'الأكثر مبيعاً', 'name_en' => 'Bestseller']);

        $products = [
            ['donuts-lotus-6', 'دونتس لوتس', 'Lotus Donuts', 'donuts', 120, AvailabilityType::AvailableNow, 'lotus', true, true],
            ['donuts-nutella-6', 'دونتس نوتيلا', 'Nutella Donuts', 'donuts', 130, AvailabilityType::AvailableNow, 'nutella', true, false],
            ['donuts-choco-12', 'بوكس دونتس شوكولاتة', 'Chocolate Donut Box', 'donuts', 220, AvailabilityType::AvailableTomorrow, 'chocolate', false, true],
            ['donuts-strawberry', 'دونتس فراولة', 'Strawberry Donuts', 'donuts', 125, AvailabilityType::SoldOut, 'strawberry', false, false],
            ['cake-vanilla-1kg', 'تورتة فانيليا ١ كيلو', 'Vanilla Cake 1kg', 'cakes', 350, AvailabilityType::PreOrder, 'vanilla', true, false],
            ['cake-choco-2kg', 'تورتة شوكولاتة ٢ كيلو', 'Chocolate Cake 2kg', 'cakes', 550, AvailabilityType::PreOrder, 'chocolate', false, true],
            ['cake-lotus', 'تورتة لوتس', 'Lotus Cake', 'cakes', 420, AvailabilityType::AvailableNow, 'lotus', true, false],
            ['cookies-mix', 'كوكيز مشكل', 'Mixed Cookies', 'cookies', 90, AvailabilityType::AvailableNow, 'chocolate', false, true],
            ['cookies-lotus', 'كوكيز لوتس', 'Lotus Cookies', 'cookies', 95, AvailabilityType::AvailableTomorrow, 'lotus', true, false],
            ['box-sweet-6', 'بوكس ٦ قطع', 'Sweet Box 6', 'boxes', 180, AvailabilityType::AvailableNow, 'chocolate', true, true],
            ['box-party', 'بوكس الحفلة', 'Party Box', 'boxes', 320, AvailabilityType::AvailableTomorrow, 'nutella', false, false],
            ['donuts-vanilla', 'دونتس فانيليا', 'Vanilla Donuts', 'donuts', 110, AvailabilityType::AvailableNow, 'vanilla', false, false],
            ['cake-birthday', 'تورتة عيد ميلاد', 'Birthday Cake', 'cakes', 480, AvailabilityType::PreOrder, 'strawberry', true, true],
            ['cookies-diet', 'كوكيز دايت', 'Diet Cookies', 'cookies', 100, AvailabilityType::AvailableNow, 'vanilla', false, false],
            ['box-romantic', 'بوكس رومانسي', 'Romantic Box', 'boxes', 260, AvailabilityType::SoldOut, 'strawberry', false, false],
            ['donuts-assorted', 'دونتس مشكل', 'Assorted Donuts', 'donuts', 140, AvailabilityType::AvailableNow, 'chocolate', true, true],
            ['cake-mini', 'ميني كيك', 'Mini Cake', 'cakes', 80, AvailabilityType::AvailableNow, 'vanilla', false, false],
            ['cookies-nutella', 'كوكيز نوتيلا', 'Nutella Cookies', 'cookies', 105, AvailabilityType::AvailableTomorrow, 'nutella', false, true],
            ['box-office', 'بوكس المكتب', 'Office Box', 'boxes', 200, AvailabilityType::AvailableNow, 'lotus', false, false],
            ['donuts-glazed', 'دونتس جليز', 'Glazed Donuts', 'donuts', 100, AvailabilityType::SoldOut, 'vanilla', false, false],
        ];

        $availabilityService = app(ProductAvailabilityService::class);

        foreach ($products as $i => [$slug, $ar, $en, $cat, $price, $availability, $flavorKey, $featured, $best]) {
            $product = Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$cat]->id,
                    'name_ar' => $ar,
                    'name_en' => $en,
                    'short_description_ar' => 'Handmade with love 🍩',
                    'short_description_en' => 'Handmade with love',
                    'description_ar' => "{$ar} معمولة بحب في مطبخ Iyma Sweet.",
                    'description_en' => "{$en} made with love at Iyma Sweet.",
                    'base_price' => $price,
                    'compare_at_price' => $featured ? $price + 30 : null,
                    'availability_type' => $availability,
                    'stock_quantity' => $availability === AvailabilityType::SoldOut ? 0 : ($availability === AvailabilityType::AvailableNow ? 50 : null),
                    'lead_time_hours' => $availability === AvailabilityType::PreOrder ? 48 : null,
                    'is_featured' => $featured,
                    'is_new' => $i < 4,
                    'is_best_seller' => $best,
                    'is_active' => true,
                    'allow_customization' => str_contains($slug, 'cake'),
                    'customization_config' => str_contains($slug, 'cake') ? [
                        ['key' => 'cake_text', 'type' => 'text', 'label_ar' => 'نص على التورتة', 'max' => 40, 'required' => false],
                        ['key' => 'notes', 'type' => 'textarea', 'label_ar' => 'ملاحظات', 'required' => false],
                    ] : null,
                    'ingredients_ar' => 'دقيق، سكر، زبدة، بيض',
                    'allergens_ar' => 'جلوتين، ألبان، بيض',
                    'storage_instructions_ar' => 'يُحفظ في مكان بارد ويُستهلك خلال ٤٨ ساعة',
                    'min_qty' => 1,
                    'sort_order' => $i,
                    'avg_rating' => round(mt_rand(35, 50) / 10, 1),
                    'ratings_count' => mt_rand(3, 40),
                ]
            );

            ProductImage::query()->updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['path' => '/images/logo-placeholder.svg', 'alt_ar' => $ar, 'sort_order' => 0]
            );

            if (str_starts_with($slug, 'donuts') || str_starts_with($slug, 'box')) {
                ProductVariant::query()->updateOrCreate(
                    ['product_id' => $product->id, 'name_ar' => '٦ قطع'],
                    ['name_en' => '6 pcs', 'price' => $price, 'is_default' => true, 'sort_order' => 0]
                );
                ProductVariant::query()->updateOrCreate(
                    ['product_id' => $product->id, 'name_ar' => '١٢ قطعة'],
                    ['name_en' => '12 pcs', 'price' => $price * 1.8, 'is_default' => false, 'sort_order' => 1]
                );
            }

            if (str_contains($slug, 'cake')) {
                $group = AddonGroup::query()->updateOrCreate(
                    ['product_id' => $product->id, 'name_ar' => 'إضافات'],
                    ['type' => 'multiple', 'is_required' => false, 'max_select' => 3]
                );
                foreach ([['شمعة', 15], ['علبة هدية', 40], ['صوص شوكولاتة', 25]] as $j => [$name, $addonPrice]) {
                    $group->options()->updateOrCreate(
                        ['name_ar' => $name],
                        ['price' => $addonPrice, 'is_active' => true, 'sort_order' => $j]
                    );
                }
            }

            if (isset($flavorValues[$flavorKey])) {
                ProductAttributeValue::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'attribute_id' => $flavor->id,
                        'attribute_value_id' => $flavorValues[$flavorKey]->id,
                    ],
                    []
                );
            }

            $sync = [];
            if ($product->is_new) {
                $sync[] = $tagNew->id;
            }
            if ($product->is_best_seller) {
                $sync[] = $tagBest->id;
            }
            $product->tags()->sync($sync);

            $availabilityService->rebuildSearchText($product->fresh(['category', 'tags']));
        }

        $fixed = Bundle::query()->updateOrCreate(
            ['slug' => 'weekend-box'],
            [
                'name_ar' => 'بوكس الويكند',
                'name_en' => 'Weekend Box',
                'description_ar' => 'تشكيلة دونتس وكوكيز بسعر مجمّع',
                'type' => 'fixed',
                'price' => 250,
                'compare_at_price' => 300,
                'availability_type' => AvailabilityType::AvailableNow,
                'is_active' => true,
                'image' => '/images/logo-placeholder.svg',
            ]
        );

        $donut = Product::query()->where('slug', 'donuts-lotus-6')->first();
        $cookie = Product::query()->where('slug', 'cookies-mix')->first();
        if ($donut && $cookie) {
            $fixed->items()->delete();
            $fixed->items()->create(['product_id' => $donut->id, 'quantity' => 1]);
            $fixed->items()->create(['product_id' => $cookie->id, 'quantity' => 1]);
        }

        $custom = Bundle::query()->updateOrCreate(
            ['slug' => 'build-your-box'],
            [
                'name_ar' => 'اختاري بوكسك',
                'name_en' => 'Build your box',
                'description_ar' => 'اختاري ٦ قطع من ١٠ نكهات',
                'type' => 'customizable',
                'price' => 200,
                'items_count' => 6,
                'min_items' => 6,
                'max_items' => 6,
                'availability_type' => AvailabilityType::AvailableNow,
                'is_active' => true,
                'image' => '/images/logo-placeholder.svg',
            ]
        );

        $custom->allowedProducts()->sync(
            Product::query()->where('category_id', $categories['donuts']->id)->limit(8)->pluck('id')
        );

        Bundle::query()->updateOrCreate(
            ['slug' => 'party-bundle'],
            [
                'name_ar' => 'باكدج الحفلة',
                'name_en' => 'Party Bundle',
                'type' => 'fixed',
                'price' => 600,
                'compare_at_price' => 720,
                'availability_type' => AvailabilityType::AvailableTomorrow,
                'is_active' => true,
                'image' => '/images/logo-placeholder.svg',
            ]
        );
    }
}
