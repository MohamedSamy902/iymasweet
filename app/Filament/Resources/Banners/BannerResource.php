<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'البانرات';

    protected static ?string $modelLabel = 'بانر';

    protected static ?string $pluralModelLabel = 'البانرات';

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('نوع البانر')->schema([
                Select::make('position')
                    ->label('الموضع')
                    ->options([
                        'welcome_popup' => 'بانر أول فتح (Popup)',
                        'home_hero' => 'هيرو الصفحة الرئيسية',
                    ])
                    ->required()
                    ->native(false),
                Toggle::make('is_active')->label('مفعّل')->default(true),
                TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
            ])->columns(3),
            Section::make('المحتوى')->schema([
                TextInput::make('title_ar')->label('العنوان عربي')->required(),
                TextInput::make('title_en')->label('العنوان إنجليزي'),
                Textarea::make('subtitle_ar')->label('النص عربي')->rows(3),
                Textarea::make('subtitle_en')->label('النص إنجليزي')->rows(3),
                TextInput::make('cta_text_ar')->label('نص الزر عربي'),
                TextInput::make('cta_text_en')->label('نص الزر إنجليزي'),
                TextInput::make('link')->label('رابط الزر')->placeholder('/shop أو https://wa.me/...'),
                TextInput::make('image_mobile')->label('صورة الموبايل')->placeholder('/images/products/...'),
                TextInput::make('image_desktop')->label('صورة الديسكتوب')->placeholder('/images/products/...'),
            ])->columns(2),
            Section::make('الجدولة (اختياري)')->schema([
                DateTimePicker::make('starts_at')->label('يبدأ من'),
                DateTimePicker::make('ends_at')->label('ينتهي في'),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')->label('العنوان')->searchable(),
                TextColumn::make('position')->label('الموضع')->badge()->formatStateUsing(fn (?string $state) => match ($state) {
                    'welcome_popup' => 'أول فتح',
                    'home_hero' => 'هيرو',
                    default => $state,
                }),
                ToggleColumn::make('is_active')->label('مفعّل'),
                TextColumn::make('sort_order')->label('ترتيب')->sortable(),
                TextColumn::make('updated_at')->label('آخر تعديل')->since(),
            ])
            ->filters([
                SelectFilter::make('position')->options([
                    'welcome_popup' => 'أول فتح',
                    'home_hero' => 'هيرو',
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBanners::route('/'),
        ];
    }
}
