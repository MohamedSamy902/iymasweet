<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\ChatbotIntent;
use App\Models\ChatbotResponse;
use App\Models\ChatbotSynonym;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Menu;
use App\Models\NotificationTemplate;
use App\Models\Page;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['about', 'من نحن', 'About', 'Iyma Sweet براند حلويات منزلية: دونتس، تورتات، وبوكسات هدايا بتوصيل للمنازل.'],
            ['contact', 'تواصل معنا', 'Contact', 'تقدري ترسلي رسالة من النموذج أو تتواصلي عبر واتساب.'],
            ['faq', 'الأسئلة الشائعة', 'FAQ', 'إجابات مختصرة عن التوصيل والدفع والطلبات.'],
            ['privacy', 'سياسة الخصوصية', 'Privacy', 'نجمع البيانات اللازمة لتنفيذ الطلب فقط، ولا نشاركها مع أطراف غير ضرورية.'],
            ['terms', 'الشروط والأحكام', 'Terms', 'باستخدام المتجر يعني الموافقة على شروط الطلب والدفع والتوصيل.'],
            ['refund', 'سياسة الإلغاء والاسترجاع', 'Refund', 'يمكن إلغاء الطلب قبل بدء التجهيز وفق سياسة المتجر المعتمدة.'],
            ['shipping', 'طرق الدفع والتوصيل', 'Payment & Delivery', 'نوفر الدفع كاش عند الاستلام، InstaPay، والمحافظ الإلكترونية، مع توصيل للمناطق المفعّلة.'],
        ];

        foreach ($pages as [$slug, $ar, $en, $content]) {
            Page::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title_ar' => $ar,
                    'title_en' => $en,
                    'content_ar' => "<p>{$content}</p>",
                    'content_en' => "<p>{$content}</p>",
                    'is_active' => true,
                ]
            );
        }

        foreach ([
            ['hero', 'Iyma Sweet', 1],
            ['status_tabs', 'حسب التوفر', 2],
            ['categories', 'التصنيفات', 3],
            ['featured', 'الأكثر طلباً', 4],
            ['bundles', 'البوكسات والعروض', 5],
            ['steps', 'خطوات الطلب', 6],
            ['reviews', 'آراء العملاء', 7],
            ['faq', 'أسئلة شائعة', 8],
        ] as [$type, $title, $sort]) {
            HomeSection::query()->updateOrCreate(
                ['type' => $type],
                ['title_ar' => $title, 'is_active' => true, 'sort_order' => $sort]
            );
        }

        Banner::query()->updateOrCreate(
            ['position' => 'home_hero', 'title_ar' => 'Iyma Sweet'],
            [
                'subtitle_ar' => 'حلويات منزلية طازجة',
                'cta_text_ar' => 'اطلب الآن',
                'link' => '/shop',
                'image_desktop' => '/images/logo-placeholder.svg',
                'image_mobile' => '/images/logo-placeholder.svg',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        foreach ([
            ['توصيل', 'كم سعر التوصيل؟', 'يختلف حسب المنطقة، ويُحسب تلقائياً عند إتمام الطلب.'],
            ['دفع', 'ما طرق الدفع المتاحة؟', 'كاش عند الاستلام، InstaPay، والمحافظ الإلكترونية مثل فودافون كاش.'],
            ['طلب', 'هل يمكن إلغاء الطلب؟', 'نعم، قبل بدء التجهيز وفق سياسة الإلغاء المعتمدة.'],
            ['حساسية', 'هل توجد خيارات خاصة بالحساسية؟', 'نعم، استخدمي فلاتر النوع في المنيو للاطلاع على المتاح.'],
        ] as $i => [$cat, $q, $a]) {
            Faq::query()->updateOrCreate(
                ['question_ar' => $q],
                ['category' => $cat, 'answer_ar' => $a, 'question_en' => $q, 'answer_en' => $a, 'sort_order' => $i, 'is_active' => true]
            );
        }

        $header = Menu::query()->updateOrCreate(['location' => 'header'], ['name' => 'Header']);
        $bottom = Menu::query()->updateOrCreate(['location' => 'bottom_nav'], ['name' => 'Bottom Nav']);
        $footer = Menu::query()->updateOrCreate(['location' => 'footer_1'], ['name' => 'Footer']);

        foreach ([
            [$header->id, 'الرئيسية', '/', 1],
            [$header->id, 'المنيو', '/shop', 2],
            [$header->id, 'الباكدجات', '/bundles', 3],
            [$bottom->id, 'الرئيسية', '/', 1],
            [$bottom->id, 'المنيو', '/shop', 2],
            [$bottom->id, 'السلة', '/cart', 3],
            [$bottom->id, 'طلباتي', '/account/orders', 4],
            [$bottom->id, 'حسابي', '/account', 5],
            [$footer->id, 'من نحن', '/about', 1],
            [$footer->id, 'تواصل', '/contact', 2],
            [$footer->id, 'الأسئلة الشائعة', '/faq', 3],
        ] as [$menuId, $label, $url, $sort]) {
            \App\Models\MenuItem::query()->updateOrCreate(
                ['menu_id' => $menuId, 'url' => $url],
                ['label_ar' => $label, 'label_en' => $label, 'sort_order' => $sort, 'is_active' => true]
            );
        }

        SocialLink::query()->updateOrCreate(
            ['platform' => 'instagram'],
            ['url' => 'https://instagram.com/', 'icon' => 'instagram', 'is_active' => true, 'sort_order' => 1]
        );
        SocialLink::query()->updateOrCreate(
            ['platform' => 'whatsapp'],
            ['url' => 'https://wa.me/201000000000', 'icon' => 'whatsapp', 'is_active' => true, 'sort_order' => 2]
        );

        foreach ([
            ['order_placed', 'تم استلام طلبك', 'طلب رقم {order_number} تم تسجيله بنجاح'],
            ['payment_approved', 'تم قبول الدفع', 'تم قبول دفع طلب {order_number}'],
            ['payment_rejected', 'تعذّر قبول الدفع', 'يرجى رفع إيصال جديد لطلب {order_number}'],
            ['status_changed', 'تحديث حالة الطلب', 'طلب {order_number}: {status}'],
            ['back_in_stock', 'المنتج متاح مجدداً', '{product_name} أصبح متاحاً للطلب'],
        ] as [$key, $title, $body]) {
            NotificationTemplate::query()->updateOrCreate(
                ['key' => $key],
                [
                    'title_ar' => $title,
                    'title_en' => $title,
                    'body_ar' => $body,
                    'body_en' => $body,
                    'variables' => ['customer_name', 'order_number', 'status', 'total', 'delivery_date', 'product_name'],
                    'channels' => ['database', 'push'],
                    'is_active' => true,
                ]
            );
        }

        $intentDefs = [
            ['greeting', 'ترحيب', ['اهلا' => 2, 'السلام' => 2, 'هاي' => 1, 'مرحبا' => 2], 10],
            ['search_product', 'بحث منتج', ['دور' => 1, 'عايز' => 1, 'دونت' => 2, 'تورت' => 2, 'كيك' => 2, 'كوكيز' => 2], 20],
            ['available_now', 'المتاح الآن', ['متاح دلوقتي' => 3, 'المتاح' => 2, 'موجود' => 1, 'متاح الان' => 3], 30],
            ['available_tomorrow', 'متاح بكرة', ['بكره' => 3, 'بكرة' => 3, 'غدا' => 2], 30],
            ['preorder', 'حجز', ['حجز' => 3, 'بري اوردر' => 2, 'preorder' => 2], 25],
            ['add_to_cart', 'إضافة للسلة', ['ضيف' => 3, 'حط' => 2, 'اضف' => 2], 40],
            ['show_cart', 'عرض السلة', ['السله' => 3, 'السلة' => 3, 'كارت' => 1], 35],
            ['track_order', 'تتبع طلب', ['طلبي' => 3, 'تتبع' => 3, 'فين طلب' => 3, 'order' => 1], 40],
            ['delivery_info', 'التوصيل', ['توصيل' => 3, 'بتوصلوا' => 3, 'شحن' => 2], 20],
            ['payment_methods', 'الدفع', ['دفع' => 2, 'انستا باي' => 3, 'فودافون' => 3, 'كاش' => 2], 20],
            ['working_hours', 'مواعيد', ['مواعيد' => 3, 'ساعات' => 2, 'افتحين' => 2], 15],
            ['offers', 'عروض', ['عرض' => 2, 'خصم' => 2, 'كوبون' => 3], 15],
            ['faq', 'أسئلة', ['الغاء' => 2, 'استرجاع' => 2, 'حساسيه' => 2], 10],
            ['human_contact', 'تواصل بشري', ['اكلم حد' => 3, 'واتساب' => 2, 'رقم' => 1], 20],
            ['thanks', 'شكر', ['شكرا' => 3, 'تسلم' => 2], 5],
            ['bye', 'وداع', ['باي' => 2, 'مع السلامه' => 2], 5],
        ];

        foreach ($intentDefs as [$key, $name, $keywords, $priority]) {
            $intent = ChatbotIntent::query()->updateOrCreate(
                ['key' => $key],
                ['name_ar' => $name, 'keywords' => $keywords, 'patterns' => [], 'priority' => $priority, 'is_active' => true]
            );
            ChatbotResponse::query()->updateOrCreate(
                ['intent_id' => $intent->id, 'text_ar' => $name],
                ['text_en' => $name, 'weight' => 1]
            );
        }

        foreach ([
            ['دونتس', 'دوناتس'], ['دونتس', 'donut'], ['دونتس', 'دونات'],
            ['تورتة', 'كيك'], ['تورتة', 'cake'], ['بكرة', 'غدا'], ['بكرة', 'بكره'],
            ['السلة', 'السله'], ['شوكولاتة', 'شوكولاته'],
        ] as [$canonical, $synonym]) {
            ChatbotSynonym::query()->updateOrCreate(
                ['canonical' => $canonical, 'synonym' => $synonym],
                []
            );
        }
    }
}
