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

        // Realistic EG homemade sweets menu (prices in EGP)
        $products = [
            ['donuts-lotus-6', 'بوكس دونتس لوتس ×٦', 'Lotus Donuts ×6', 'donuts', 185, AvailabilityType::AvailableNow, 'lotus', true, true, 'عجين طري مع صوص لوتس وكرامبل بسكويت', 'Soft dough with Lotus sauce and biscuit crumble'],
            ['donuts-nutella-6', 'بوكس دونتس نوتيلا ×٦', 'Nutella Donuts ×6', 'donuts', 195, AvailabilityType::AvailableNow, 'nutella', true, false, 'محشو نوتيلا مع تغطية شوكولاتة خفيفة', 'Filled with Nutella and lightly chocolate-coated'],
            ['donuts-choco-12', 'بوكس دونتس شوكولاتة ×١٢', 'Chocolate Donut Box ×12', 'donuts', 340, AvailabilityType::AvailableTomorrow, 'chocolate', false, true, 'تشكيلة شوكولاتة للحفلات والتجمعات', 'Chocolate assortment for gatherings'],
            ['donuts-strawberry', 'دونتس فراولة طازجة ×٦', 'Fresh Strawberry Donuts ×6', 'donuts', 190, AvailabilityType::SoldOut, 'strawberry', false, false, 'جليز فراولة وقطع فاكهة موسمية', 'Strawberry glaze with seasonal fruit'],
            ['cake-vanilla-1kg', 'تورتة فانيليا ١ كجم', 'Vanilla Cake 1kg', 'cakes', 420, AvailabilityType::PreOrder, 'vanilla', true, false, 'طبقات فانيليا كريمية — مناسب للاحتفالات', 'Creamy vanilla layers for celebrations'],
            ['cake-choco-2kg', 'تورتة شوكولاتة ٢ كجم', 'Chocolate Cake 2kg', 'cakes', 680, AvailabilityType::PreOrder, 'chocolate', false, true, 'شوكولاتة غامقة مع ganache — بالحجز المسبق', 'Dark chocolate with ganache — pre-order'],
            ['cake-lotus', 'تورتة لوتس ١.٥ كجم', 'Lotus Cake 1.5kg', 'cakes', 520, AvailabilityType::AvailableNow, 'lotus', true, false, 'طبقة لوتس سميكة وبسكويت مقرمش', 'Thick Lotus layer with crunchy biscuit'],
            ['cookies-mix', 'علبة كوكيز مشكل ×١٢', 'Mixed Cookies ×12', 'cookies', 145, AvailabilityType::AvailableNow, 'chocolate', false, true, 'تشكيلة شوكولاتة وفانيليا ولوتس', 'Chocolate, vanilla, and Lotus mix'],
            ['cookies-lotus', 'كوكيز لوتس ×٨', 'Lotus Cookies ×8', 'cookies', 130, AvailabilityType::AvailableTomorrow, 'lotus', true, false, 'كوكيز طري محشو كريمة لوتس', 'Soft cookies filled with Lotus cream'],
            ['box-sweet-6', 'بوكس هدية ٦ قطع', 'Gift Box 6pcs', 'boxes', 250, AvailabilityType::AvailableNow, 'chocolate', true, true, 'دونتس وكوكيز بتغليف هدية أنيق', 'Donuts and cookies in gift packaging'],
            ['box-party', 'بوكس الحفلة الكبير', 'Party Box Large', 'boxes', 480, AvailabilityType::AvailableTomorrow, 'nutella', false, false, 'تشكيلة كبيرة للضيوف — يكفي ٨–١٠ أفراد', 'Large assortment for 8–10 guests'],
            ['donuts-vanilla', 'دونتس فانيليا كلاسيك ×٦', 'Classic Vanilla Donuts ×6', 'donuts', 160, AvailabilityType::AvailableNow, 'vanilla', false, false, 'جليز فانيليا ناعم بدون حشو ثقيل', 'Soft vanilla glaze, lightly finished'],
            ['cake-birthday', 'تورتة عيد ميلاد حسب الطلب', 'Custom Birthday Cake', 'cakes', 580, AvailabilityType::PreOrder, 'strawberry', true, true, 'تصميم حسب الاسم واللون — احجزي قبل الموعد بـ ٤٨ ساعة', 'Custom name & colors — book 48h ahead'],
            ['cookies-diet', 'كوكيز خفيف السكر ×٨', 'Lower-Sugar Cookies ×8', 'cookies', 150, AvailabilityType::AvailableNow, 'vanilla', false, false, 'تحلية أخف مع نفس القوام الطري', 'Lighter sweetness with a soft bite'],
            ['box-romantic', 'بوكس مناسبة رومانسية', 'Romantic Occasion Box', 'boxes', 320, AvailabilityType::SoldOut, 'strawberry', false, false, 'قطع مختارة بتغليف وردي للمناسبات الخاصة', 'Selected sweets in soft pink gift wrap'],
            ['donuts-assorted', 'دونتس مشكل نكهات ×٦', 'Assorted Flavor Donuts ×6', 'donuts', 200, AvailabilityType::AvailableNow, 'chocolate', true, true, 'ثلاث نكهات في بوكس واحد', 'Three flavors in one box'],
            ['cake-mini', 'ميني تورتة فردية', 'Individual Mini Cake', 'cakes', 95, AvailabilityType::AvailableNow, 'vanilla', false, false, 'حصة فردية جاهزة للتقديم فوراً', 'Single serve, ready to enjoy'],
            ['cookies-nutella', 'كوكيز نوتيلا ×٨', 'Nutella Cookies ×8', 'cookies', 140, AvailabilityType::AvailableTomorrow, 'nutella', false, true, 'حواف مقرمشة وقلب نوتيلا دافئ', 'Crispy edges with a warm Nutella center'],
            ['box-office', 'بوكس المكتب', 'Office Sharing Box', 'boxes', 280, AvailabilityType::AvailableNow, 'lotus', false, false, 'تشكيلة للمشاركة في الاجتماعات والمكتب', 'Shareable assortment for the office'],
            ['donuts-glazed', 'دونتس جليز سادة ×٦', 'Plain Glazed Donuts ×6', 'donuts', 140, AvailabilityType::SoldOut, 'vanilla', false, false, 'كلاسيك خفيف — مثالي مع القهوة', 'Light classic — perfect with coffee'],
        ];

        $availabilityService = app(ProductAvailabilityService::class);

        foreach ($products as $i => [$slug, $ar, $en, $cat, $price, $availability, $flavorKey, $featured, $best, $shortAr, $shortEn]) {
            $product = Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$cat]->id,
                    'name_ar' => $ar,
                    'name_en' => $en,
                    'short_description_ar' => $shortAr,
                    'short_description_en' => $shortEn,
                    'description_ar' => "{$ar}: {$shortAr}. تجهيز منزلي يومي من مطبخ Iyma Sweet.",
                    'description_en' => "{$en}: {$shortEn}. Fresh homemade prep from Iyma Sweet.",
                    'base_price' => $price,
                    'compare_at_price' => $featured ? $price + 25 : null,
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
                        ['key' => 'notes', 'type' => 'textarea', 'label_ar' => 'ملاحظات على الطلب', 'required' => false],
                    ] : null,
                    'ingredients_ar' => 'دقيق، سكر، زبدة، بيض، نكهات طبيعية حسب الصنف',
                    'allergens_ar' => 'يحتوي على جلوتين وألبان وبيض. قد يحتوي على مكسرات حسب النكهة.',
                    'storage_instructions_ar' => 'يُحفظ مبرداً ويُفضل الاستهلاك خلال ٤٨ ساعة من الاستلام.',
                    'min_qty' => 1,
                    'sort_order' => $i,
                    'avg_rating' => round(mt_rand(40, 50) / 10, 1),
                    'ratings_count' => mt_rand(8, 60),
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
                'name_ar' => 'بوكس نهاية الأسبوع',
                'name_en' => 'Weekend Box',
                'description_ar' => 'دونتس لوتس مع علبة كوكيز مشكل بسعر أوفر من الشراء المنفصل',
                'type' => 'fixed',
                'price' => 290,
                'compare_at_price' => 330,
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
                'name_ar' => 'اصنعي بوكسك',
                'name_en' => 'Build your box',
                'description_ar' => 'اختاري ٦ قطع من نكهات الدونتس المتاحة',
                'type' => 'customizable',
                'price' => 220,
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
                'name_ar' => 'باقة الحفلة',
                'name_en' => 'Party Bundle',
                'description_ar' => 'تشكيلة كبيرة للمناسبات — دونتس وبوكس هدية',
                'type' => 'fixed',
                'price' => 650,
                'compare_at_price' => 760,
                'availability_type' => AvailabilityType::AvailableTomorrow,
                'is_active' => true,
                'image' => '/images/logo-placeholder.svg',
            ]
        );
    }
}
