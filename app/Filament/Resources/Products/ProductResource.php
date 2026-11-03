<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCake;

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    protected static string|UnitEnum|null $navigationGroup = 'الكتالوج';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('أساسي')->schema([
                TextInput::make('name_ar')->label('الاسم عربي')->required()->markAsRequired(),
                TextInput::make('name_en')->label('الاسم إنجليزي'),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
                Select::make('category_id')->label('التصنيف')->options(fn () => Category::query()->pluck('name_ar', 'id'))->searchable()->required(),
                Textarea::make('short_description_ar')->label('وصف قصير')->rows(2),
                Textarea::make('description_ar')->label('الوصف')->rows(4),
            ])->columns(2),
            Section::make('التسعير والتوفر')->schema([
                TextInput::make('base_price')->label('السعر')->numeric()->required(),
                TextInput::make('compare_at_price')->label('قبل الخصم')->numeric(),
                Select::make('availability_type')->label('حالة التوفر')->options([
                    'available_now' => 'متاح الآن',
                    'sold_out' => 'نفد مؤقتاً',
                    'available_tomorrow' => 'متاح بكرة',
                    'pre_order' => 'بالحجز',
                ])->required(),
                TextInput::make('stock_quantity')->label('الكمية')->numeric(),
                TextInput::make('lead_time_hours')->label('مدة التجهيز (ساعة)')->numeric(),
                Toggle::make('is_active')->label('مفعّل')->default(true),
                Toggle::make('is_featured')->label('مميز'),
                Toggle::make('is_new')->label('جديد'),
                Toggle::make('is_best_seller')->label('الأكثر مبيعاً'),
                Toggle::make('allow_customization')->label('يسمح بالتخصيص'),
            ])->columns(3),
            Section::make('معرض الوسائط (صور + فيديو)')->schema([
                Repeater::make('images')
                    ->relationship()
                    ->label('الوسائط')
                    ->orderColumn('sort_order')
                    ->reorderable()
                    ->collapsible()
                    ->defaultItems(1)
                    ->schema([
                        Select::make('type')
                            ->label('النوع')
                            ->options([
                                'image' => 'صورة',
                                'video' => 'فيديو',
                            ])
                            ->default('image')
                            ->required()
                            ->live(),
                        TextInput::make('path')
                            ->label('رابط الملف')
                            ->placeholder('/images/products/donut-1.jpg أو /videos/demo.mp4')
                            ->required()
                            ->helperText('ضع الصورة أو الفيديو في public ثم الصق المسار هنا'),
                        TextInput::make('poster_path')
                            ->label('غلاف الفيديو')
                            ->placeholder('/images/products/donut-1.jpg')
                            ->visible(fn ($get) => $get('type') === 'video'),
                        TextInput::make('mime')->label('MIME')->placeholder('image/jpeg أو video/mp4'),
                        TextInput::make('alt_ar')->label('النص البديل عربي'),
                        TextInput::make('alt_en')->label('النص البديل إنجليزي'),
                        Toggle::make('is_primary')->label('أساسية'),
                    ])
                    ->columns(2)
                    ->itemLabel(fn (array $state): ?string => ($state['type'] ?? 'image') === 'video' ? 'فيديو' : 'صورة'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')->label('الاسم')->searchable()->sortable(),
                TextColumn::make('category.name_ar')->label('التصنيف'),
                TextColumn::make('base_price')->label('السعر')->money('EGP'),
                TextColumn::make('images_count')->counts('images')->label('وسائط'),
                SelectColumn::make('availability_type')->label('التوفر')->options([
                    'available_now' => 'متاح الآن',
                    'sold_out' => 'نفد مؤقتاً',
                    'available_tomorrow' => 'متاح بكرة',
                    'pre_order' => 'بالحجز',
                ]),
                TextColumn::make('stock_quantity')->label('كمية'),
                ToggleColumn::make('is_active')->label('مفعّل'),
            ])
            ->filters([
                SelectFilter::make('availability_type')->options([
                    'available_now' => 'متاح الآن',
                    'sold_out' => 'نفد مؤقتاً',
                    'available_tomorrow' => 'متاح بكرة',
                    'pre_order' => 'بالحجز',
                ]),
                SelectFilter::make('category_id')->label('تصنيف')->options(fn () => Category::query()->pluck('name_ar', 'id')),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
