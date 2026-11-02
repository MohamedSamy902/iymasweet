<?php

namespace App\Filament\Resources\UiTexts;

use App\Filament\Resources\UiTexts\Pages\ManageUiTexts;
use App\Models\UiText;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class UiTextResource extends Resource
{
    protected static ?string $model = UiText::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'نصوص الواجهة';

    protected static ?string $modelLabel = 'نص واجهة';

    protected static ?string $pluralModelLabel = 'نصوص الواجهة';

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('group')
                ->label('المجموعة')
                ->options([
                    'layout' => 'layout',
                    'home' => 'home',
                    'shop' => 'shop',
                    'product' => 'product',
                    'cart' => 'cart',
                    'checkout' => 'checkout',
                    'auth' => 'auth',
                    'account' => 'account',
                    'errors' => 'errors',
                    'common' => 'common',
                ])
                ->searchable()
                ->required(),
            TextInput::make('key')->label('المفتاح')->required(),
            Textarea::make('value_ar')->label('عربي')->required()->rows(2),
            Textarea::make('value_en')->label('English')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->label('المجموعة')->badge()->sortable()->searchable(),
                TextColumn::make('key')->label('المفتاح')->searchable()->copyable(),
                TextInputColumn::make('value_ar')->label('عربي')->searchable(),
                TextInputColumn::make('value_en')->label('English'),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('المجموعة')
                    ->options(fn () => UiText::query()->distinct()->orderBy('group')->pluck('group', 'group')->all()),
            ])
            ->defaultSort('group')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUiTexts::route('/'),
        ];
    }
}
