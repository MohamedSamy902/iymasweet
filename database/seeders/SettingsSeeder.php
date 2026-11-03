<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $settings->set('general', 'brand_name', [
            'ar' => 'Iyma Sweet',
            'en' => 'Iyma Sweet',
        ], 'string', true);

        $settings->set('general', 'tagline', [
            'ar' => 'Handmade with love',
            'en' => 'Handmade with love',
        ], 'string', true);

        $settings->set('general', 'phone', '01007366484', 'string');
        $settings->set('general', 'whatsapp', '201007366484', 'string');
        $settings->set('general', 'whatsapp_handle', '@iymasweet', 'string');
        $settings->set('general', 'email', 'hello@iymasweet.com', 'string');
        $settings->set('general', 'address', [
            'ar' => 'القاهرة، مصر',
            'en' => 'Cairo, Egypt',
        ], 'text', true);
        $settings->set('general', 'about_short', [
            'ar' => '٦ سنوات خبرة في صناعة الحلويات. حلويات وتورتات هاند ميد بمكونات ممتازة، واهتمام بالتفاصيل والجودة قبل أي شيء.',
            'en' => '6 years of handmade sweets experience — quality ingredients and attention to detail first.',
        ], 'text', true);
        $settings->set('general', 'instagram', 'https://instagram.com/eman.sayed.940641', 'string');
        $settings->set('general', 'facebook', 'https://www.facebook.com/iymasweet', 'string');
        $settings->set('general', 'logo', null, 'image');
        $settings->set('general', 'favicon', null, 'image');
        $settings->set('general', 'currency', 'EGP', 'string');
        $settings->set('general', 'currency_symbol', 'ج.م', 'string');
        $settings->set('general', 'timezone', 'Africa/Cairo', 'string');
        $settings->set('general', 'english_enabled', true, 'boolean');
        $settings->set('features', 'chatbot_enabled', false, 'boolean');

        foreach (config('iyma.theme_defaults') as $cssVar => $value) {
            $key = str_replace('--color-', '', $cssVar);
            $settings->set('theme', $key, $value, 'color');
        }

        $settings->set('theme', 'show_sparkles', true, 'boolean');
        $settings->set('theme', 'border_radius', '1.5rem', 'string');
        $settings->set('theme', 'heading_font', 'Baloo Bhaijaan 2', 'string');
        $settings->set('theme', 'body_font', 'Cairo', 'string');

        $settings->set('store', 'guest_checkout', true, 'boolean');
        $settings->set('store', 'closed', false, 'boolean');
        $settings->set('store', 'closed_message', [
            'ar' => 'المتجر مغلق مؤقتاً. نعود قريباً.',
            'en' => 'Store temporarily closed. We will be back soon.',
        ], 'text', true);
        $settings->set('store', 'sold_out_at_end', true, 'boolean');
        $settings->set('store', 'payment_timeout_hours', 6, 'integer');
        $settings->set('store', 'reviews_manual_approval', true, 'boolean');
        $settings->set('store', 'order_number_format', 'IS-{YY}-{SEQ6}', 'string');

        $settings->set('content', 'announcement_enabled', true, 'boolean');
        $settings->set('content', 'announcement_text', [
            'ar' => 'توصيل مجاني للطلبات فوق ٥٠٠ ج.م',
            'en' => 'Free delivery on orders over 500 EGP',
        ], 'text', true);
    }
}
