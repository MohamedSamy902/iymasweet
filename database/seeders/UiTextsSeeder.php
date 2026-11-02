<?php

namespace Database\Seeders;

use App\Models\UiText;
use App\Services\UiTextService;
use Illuminate\Database\Seeder;

class UiTextsSeeder extends Seeder
{
    public function run(): void
    {
        $texts = [
            // Nav / layout
            ['layout', 'nav_home', 'الرئيسية', 'Home'],
            ['layout', 'nav_shop', 'المنيو', 'Menu'],
            ['layout', 'nav_cart', 'السلة', 'Cart'],
            ['layout', 'nav_orders', 'طلباتي', 'My Orders'],
            ['layout', 'nav_account', 'حسابي', 'Account'],
            ['layout', 'nav_search', 'دور على حاجة حلوة…', 'Search for something sweet…'],
            ['layout', 'nav_bundles', 'الباكدجات', 'Bundles'],
            ['layout', 'cart_items_summary', '{count} منتجات — {total}', '{count} items — {total}'],
            ['layout', 'cart_continue', 'كمّل الطلب', 'Checkout'],
            ['layout', 'install_app', 'ثبّت التطبيق', 'Install app'],
            ['layout', 'chatbot_open', 'اسأل المساعد', 'Ask assistant'],
            ['layout', 'footer_rights', 'جميع الحقوق محفوظة', 'All rights reserved'],
            ['layout', 'locale_ar', 'عربي', 'Arabic'],
            ['layout', 'locale_en', 'English', 'English'],

            // Home
            ['home', 'hero_title', 'حلويات منزلية معمولة بحب', 'Homemade sweets made with love'],
            ['home', 'hero_subtitle', 'دونتس، تورتات، وبوكسات هدايا بطعم يفرّحك أنتِ واللي بتحبيهم', 'Donuts, cakes, and gift boxes that make you smile'],
            ['home', 'cta_order_now', 'اطلب دلوقتي', 'Order now'],
            ['home', 'cta_see_menu', 'شوف المنيو', 'See menu'],
            ['home', 'status_available_now', 'متاح دلوقتي', 'Available now'],
            ['home', 'status_tomorrow', 'متاح بكرة', 'Available tomorrow'],
            ['home', 'status_preorder', 'بالحجز', 'Pre-order'],
            ['home', 'status_sold_out', 'خلص (وهيرجع قريب)', 'Sold out (coming back)'],
            ['home', 'categories_title', 'التصنيفات', 'Categories'],
            ['home', 'featured_title', 'الأكثر طلباً', 'Best sellers'],
            ['home', 'new_title', 'جديد عندنا', 'New arrivals'],
            ['home', 'bundles_title', 'الباكدجات والبوكسات', 'Bundles & boxes'],
            ['home', 'how_title', 'إزاي تطلب؟', 'How to order?'],
            ['home', 'reviews_title', 'آراء العملاء', 'Customer reviews'],
            ['home', 'faq_title', 'أسئلة بتسألونا عليها كتير', 'FAQ'],
            ['home', 'cta_final_title', 'جعانين لحاجة حلوة؟', 'Craving something sweet?'],
            ['home', 'cta_final_button', 'يلا نبدأ الطلب', "Let's order"],
            ['home', 'welcome', 'أهلاً بيكي يا قمر 🍩', 'Welcome, sunshine 🍩'],
            ['home', 'step_pick', 'اختاري المنتج', 'Pick your treat'],
            ['home', 'step_slot', 'احجزي ميعاد', 'Book a slot'],
            ['home', 'step_pay', 'ادفعي براحتك', 'Pay your way'],
            ['home', 'step_receive', 'استلمي الطلب', 'Receive your order'],

            // Shop / product
            ['shop', 'title', 'المنيو', 'Menu'],
            ['shop', 'filters', 'الفلاتر', 'Filters'],
            ['shop', 'clear_filters', 'مسح الفلاتر', 'Clear filters'],
            ['shop', 'sort_latest', 'الأحدث', 'Latest'],
            ['shop', 'sort_bestseller', 'الأكثر مبيعاً', 'Best sellers'],
            ['shop', 'sort_price_asc', 'السعر: الأقل', 'Price: low to high'],
            ['shop', 'sort_price_desc', 'السعر: الأعلى', 'Price: high to low'],
            ['shop', 'sort_rating', 'التقييم', 'Rating'],
            ['shop', 'empty_title', 'ملاقيناش اللي بتدور عليه', "We couldn't find that"],
            ['shop', 'empty_hint', 'جرّب فلتر تاني أو اسأل المساعد', 'Try another filter or ask the assistant'],
            ['shop', 'load_more', 'عرض المزيد', 'Load more'],
            ['product', 'add_to_cart', 'أضف للسلة', 'Add to cart'],
            ['product', 'book_tomorrow', 'احجز لبكرة', 'Book for tomorrow'],
            ['product', 'preorder', 'احجز دلوقتي', 'Pre-order now'],
            ['product', 'notify_me', 'نبّهني لما يتوفر', 'Notify me when available'],
            ['product', 'sold_out', 'خلص', 'Sold out'],
            ['product', 'available_now', 'متاح دلوقتي', 'Available now'],
            ['product', 'available_tomorrow', 'متاح بكرة', 'Available tomorrow'],
            ['product', 'pre_order_badge', 'بالحجز', 'Pre-order'],
            ['product', 'lead_time', 'بيتجهز في {hours} ساعة', 'Ready in {hours} hours'],
            ['product', 'from_price', 'من', 'From'],
            ['product', 'quantity', 'الكمية', 'Quantity'],
            ['product', 'notes', 'ملاحظات', 'Notes'],
            ['product', 'occasion_date', 'تاريخ المناسبة', 'Occasion date'],
            ['product', 'description', 'الوصف', 'Description'],
            ['product', 'ingredients', 'المكونات', 'Ingredients'],
            ['product', 'allergens', 'مسببات الحساسية', 'Allergens'],
            ['product', 'storage', 'التخزين', 'Storage'],
            ['product', 'reviews', 'التقييمات', 'Reviews'],
            ['product', 'related', 'منتجات مشابهة', 'Related products'],
            ['product', 'added_toast', 'تمام ✨ اتضافت للسلة', 'Added to cart ✨'],

            // Cart / checkout
            ['cart', 'title', 'السلة', 'Cart'],
            ['cart', 'empty', 'السلة فاضية… يلا نملّاها حاجات حلوة؟', 'Your cart is empty… shall we fill it?'],
            ['cart', 'coupon', 'كود الخصم', 'Coupon code'],
            ['cart', 'apply_coupon', 'تطبيق', 'Apply'],
            ['cart', 'subtotal', 'المجموع', 'Subtotal'],
            ['cart', 'discount', 'الخصم', 'Discount'],
            ['cart', 'delivery', 'التوصيل', 'Delivery'],
            ['cart', 'delivery_by_area', 'حسب المنطقة', 'Depends on area'],
            ['cart', 'total', 'الإجمالي', 'Total'],
            ['cart', 'farthest_delivery', 'أبعد موعد تسليم للطلب', 'Latest delivery date for this order'],
            ['checkout', 'title', 'إتمام الطلب', 'Checkout'],
            ['checkout', 'step_info', 'البيانات', 'Details'],
            ['checkout', 'step_address', 'العنوان', 'Address'],
            ['checkout', 'step_slot', 'الموعد', 'Time slot'],
            ['checkout', 'step_payment', 'الدفع', 'Payment'],
            ['checkout', 'guest_continue', 'متابعة كضيف', 'Continue as guest'],
            ['checkout', 'confirm', 'تأكيد الطلب', 'Place order'],
            ['checkout', 'upload_receipt', 'ارفع صورة التحويل', 'Upload transfer receipt'],
            ['checkout', 'copy_number', 'نسخ الرقم', 'Copy number'],
            ['checkout', 'pay_and_upload', 'ادفع وارفع الإيصال', 'Pay & upload receipt'],

            // Auth / account
            ['auth', 'login', 'دخول', 'Login'],
            ['auth', 'register', 'تسجيل', 'Register'],
            ['auth', 'phone', 'رقم الموبايل', 'Phone'],
            ['auth', 'password', 'كلمة السر', 'Password'],
            ['auth', 'forgot', 'نسيت كلمة السر؟', 'Forgot password?'],
            ['auth', 'logout', 'تسجيل خروج', 'Logout'],
            ['account', 'title', 'حسابي', 'My account'],
            ['account', 'orders', 'طلباتي', 'My orders'],
            ['account', 'addresses', 'عناويني', 'Addresses'],
            ['account', 'favorites', 'المفضلة', 'Favorites'],
            ['account', 'reviews', 'تقييماتي', 'My reviews'],
            ['account', 'notifications', 'الإشعارات', 'Notifications'],
            ['account', 'profile', 'بياناتي', 'Profile'],
            ['account', 'reorder', 'أعد الطلب', 'Reorder'],
            ['account', 'cancel_order', 'إلغاء الطلب', 'Cancel order'],
            ['account', 'track_order', 'تتبع الطلب', 'Track order'],

            // Errors / system
            ['errors', 'generic', 'حصلت مشكلة بسيطة، جرّب تاني بعد شوية', 'Something went wrong. Please try again.'],
            ['errors', 'required', 'الحقل ده مطلوب يا قمر', 'This field is required'],
            ['errors', 'invalid_phone', 'رقم الموبايل مش مظبوط، استخدم رقم مصري', 'Please enter a valid Egyptian phone number'],
            ['errors', 'not_found', 'الصفحة دي مش موجودة', 'Page not found'],
            ['errors', 'offline', 'مفيش نت دلوقتي… تقدر تشوف المنيو المحفوظ', "You're offline… you can still browse the cached menu"],
            ['errors', 'server', 'السيرفر تعبان شوية، جرّب بعد دقايق', 'Server is busy. Try again in a bit.'],
            ['common', 'save', 'حفظ', 'Save'],
            ['common', 'cancel', 'إلغاء', 'Cancel'],
            ['common', 'edit', 'تعديل', 'Edit'],
            ['common', 'delete', 'حذف', 'Delete'],
            ['common', 'back', 'رجوع', 'Back'],
            ['common', 'loading', 'ثواني…', 'Loading…'],
            ['common', 'currency', 'ج.م', 'EGP'],
            ['common', 'search', 'بحث', 'Search'],
            ['common', 'see_all', 'شوف الكل', 'See all'],
        ];

        foreach ($texts as [$group, $key, $ar, $en]) {
            UiText::query()->updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value_ar' => $ar, 'value_en' => $en]
            );
        }

        app(UiTextService::class)->forgetCache();
    }
}
