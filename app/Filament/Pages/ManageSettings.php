<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'الإعدادات';

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 90;

    protected static ?string $title = 'إعدادات المتجر';

    protected string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $this->form->fill([
            'brand_name_ar' => $settings->get('general.brand_name', 'Iyma Sweet', 'ar'),
            'brand_name_en' => $settings->get('general.brand_name', 'Iyma Sweet', 'en'),
            'tagline_ar' => $settings->get('general.tagline', 'Handmade with love', 'ar'),
            'tagline_en' => $settings->get('general.tagline', 'Handmade with love', 'en'),
            'phone' => $settings->get('general.phone'),
            'whatsapp' => $settings->get('general.whatsapp'),
            'email' => $settings->get('general.email'),
            'currency' => $settings->get('general.currency', 'EGP'),
            'currency_symbol' => $settings->get('general.currency_symbol', 'ج.م'),
            'english_enabled' => (bool) $settings->get('general.english_enabled', true),
            'guest_checkout' => (bool) $settings->get('store.guest_checkout', true),
            'store_closed' => (bool) $settings->get('store.closed', false),
            'store_closed_message_ar' => $settings->get('store.closed_message', null, 'ar'),
            'announcement_enabled' => (bool) $settings->get('content.announcement_enabled', true),
            'announcement_text_ar' => $settings->get('content.announcement_text', null, 'ar'),
            'announcement_text_en' => $settings->get('content.announcement_text', null, 'en'),
            'show_sparkles' => (bool) $settings->get('theme.show_sparkles', true),
            'primary' => $settings->get('theme.primary', '#F2A0B8'),
            'primary_dark' => $settings->get('theme.primary-dark', '#E4789D'),
            'primary_soft' => $settings->get('theme.primary-soft', '#FDE8EE'),
            'denim' => $settings->get('theme.denim', '#6F93B8'),
            'denim_soft' => $settings->get('theme.denim-soft', '#E3ECF5'),
            'brown' => $settings->get('theme.brown', '#7A3E26'),
            'cream' => $settings->get('theme.cream', '#FFFAF7'),
            'text' => $settings->get('theme.text', '#4A3A36'),
            'text_muted' => $settings->get('theme.text-muted', '#8C7B76'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('settingsTabs')->tabs([
                    Tab::make('عام')->schema([
                        Section::make('الهوية')->schema([
                            TextInput::make('brand_name_ar')->label('اسم البراند (عربي)')->required(),
                            TextInput::make('brand_name_en')->label('اسم البراند (إنجليزي)'),
                            TextInput::make('tagline_ar')->label('الشعار الفرعي (عربي)'),
                            TextInput::make('tagline_en')->label('الشعار الفرعي (إنجليزي)'),
                            TextInput::make('phone')->label('الموبايل')->tel(),
                            TextInput::make('whatsapp')->label('واتساب (wa.me)'),
                            TextInput::make('email')->label('الإيميل')->email(),
                            TextInput::make('currency')->label('العملة')->maxLength(8),
                            TextInput::make('currency_symbol')->label('رمز العملة'),
                            Toggle::make('english_enabled')->label('تفعيل الإنجليزية'),
                        ])->columns(2),
                    ]),
                    Tab::make('المظهر')->schema([
                        Section::make('ألوان الهوية')->schema([
                            ColorPicker::make('primary')->label('Primary'),
                            ColorPicker::make('primary_dark')->label('Primary Dark'),
                            ColorPicker::make('primary_soft')->label('Primary Soft'),
                            ColorPicker::make('denim')->label('Denim'),
                            ColorPicker::make('denim_soft')->label('Denim Soft'),
                            ColorPicker::make('brown')->label('Brown'),
                            ColorPicker::make('cream')->label('Cream'),
                            ColorPicker::make('text')->label('Text'),
                            ColorPicker::make('text_muted')->label('Text Muted'),
                            Toggle::make('show_sparkles')->label('إظهار الزخارف (نجوم)'),
                        ])->columns(3),
                    ]),
                    Tab::make('سلوك المتجر')->schema([
                        Section::make()->schema([
                            Toggle::make('guest_checkout')->label('الطلب كضيف'),
                            Toggle::make('store_closed')->label('إغلاق المتجر مؤقتاً'),
                            Textarea::make('store_closed_message_ar')->label('رسالة الإغلاق')->rows(2),
                            Toggle::make('announcement_enabled')->label('بانر الإعلان العلوي'),
                            TextInput::make('announcement_text_ar')->label('نص الإعلان (عربي)'),
                            TextInput::make('announcement_text_en')->label('نص الإعلان (إنجليزي)'),
                        ]),
                    ]),
                ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $settings->set('general', 'brand_name', [
            'ar' => $data['brand_name_ar'],
            'en' => $data['brand_name_en'] ?? $data['brand_name_ar'],
        ], 'string', true);

        $settings->set('general', 'tagline', [
            'ar' => $data['tagline_ar'] ?? '',
            'en' => $data['tagline_en'] ?? '',
        ], 'string', true);

        $settings->set('general', 'phone', $data['phone'] ?? null);
        $settings->set('general', 'whatsapp', $data['whatsapp'] ?? null);
        $settings->set('general', 'email', $data['email'] ?? null);
        $settings->set('general', 'currency', $data['currency'] ?? 'EGP');
        $settings->set('general', 'currency_symbol', $data['currency_symbol'] ?? 'ج.م');
        $settings->set('general', 'english_enabled', (bool) ($data['english_enabled'] ?? true), 'boolean');

        $colorMap = [
            'primary' => 'primary',
            'primary_dark' => 'primary-dark',
            'primary_soft' => 'primary-soft',
            'denim' => 'denim',
            'denim_soft' => 'denim-soft',
            'brown' => 'brown',
            'cream' => 'cream',
            'text' => 'text',
            'text_muted' => 'text-muted',
        ];

        foreach ($colorMap as $formKey => $settingKey) {
            if (! empty($data[$formKey])) {
                $settings->set('theme', $settingKey, $data[$formKey], 'color');
            }
        }

        $settings->set('theme', 'show_sparkles', (bool) ($data['show_sparkles'] ?? true), 'boolean');
        $settings->set('store', 'guest_checkout', (bool) ($data['guest_checkout'] ?? true), 'boolean');
        $settings->set('store', 'closed', (bool) ($data['store_closed'] ?? false), 'boolean');
        $settings->set('store', 'closed_message', [
            'ar' => $data['store_closed_message_ar'] ?? '',
            'en' => $data['store_closed_message_ar'] ?? '',
        ], 'text', true);

        $settings->set('content', 'announcement_enabled', (bool) ($data['announcement_enabled'] ?? true), 'boolean');
        $settings->set('content', 'announcement_text', [
            'ar' => $data['announcement_text_ar'] ?? '',
            'en' => $data['announcement_text_en'] ?? '',
        ], 'text', true);

        Notification::make()
            ->title('تم حفظ الإعدادات')
            ->success()
            ->send();
    }
}
