<?php
namespace App\Filament\Resources\Categories;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel = 'التصنيفات';
    protected static string|UnitEnum|null $navigationGroup = 'الكتالوج';
    protected static ?int $navigationSort = 2;
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_ar')->label('الاسم عربي')->required(),
            TextInput::make('name_en')->label('الاسم إنجليزي'),
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            TextInput::make('color')->label('اللون'),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->default(true),
            Toggle::make('is_featured')->default(false),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name_ar')->searchable(),
            TextColumn::make('slug'),
            ToggleColumn::make('is_active'),
            ToggleColumn::make('is_featured'),
            TextColumn::make('sort_order')->sortable(),
        ])->defaultSort('sort_order');
    }
    public static function getPages(): array
    {
        return ['index' => ManageCategories::route('/')];
    }
}
