<?php
namespace App\Filament\Resources\Coupons;
use App\Filament\Resources\Coupons\Pages\ManageCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;
class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;
    protected static ?string $navigationLabel = 'الكوبونات';
    protected static string|UnitEnum|null $navigationGroup = 'التسويق';
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->required()->unique(ignoreRecord: true),
            Select::make('type')->options(['percent'=>'نسبة','fixed'=>'مبلغ','free_delivery'=>'توصيل مجاني'])->required(),
            TextInput::make('value')->numeric()->default(0),
            TextInput::make('min_order')->numeric(),
            TextInput::make('max_discount')->numeric(),
            DateTimePicker::make('starts_at'),
            DateTimePicker::make('ends_at'),
            TextInput::make('usage_limit')->numeric(),
            TextInput::make('per_user_limit')->numeric(),
            Toggle::make('first_order_only'),
            Toggle::make('is_active')->default(true),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable(),
            TextColumn::make('type'),
            TextColumn::make('value'),
            ToggleColumn::make('is_active'),
            TextColumn::make('ends_at')->dateTime(),
        ]);
    }
    public static function getPages(): array
    {
        return ['index' => ManageCoupons::route('/')];
    }
}
