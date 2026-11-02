<?php
namespace App\Filament\Resources\Orders;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Services\OrderService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'الطلبات';
    protected static string|UnitEnum|null $navigationGroup = 'المبيعات';
    protected static ?int $navigationSort = 1;
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الطلب')->schema([
                TextInput::make('number')->disabled(),
                Select::make('status')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->labelAr()])),
                Textarea::make('admin_notes')->label('ملاحظات داخلية'),
            ]),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('number')->label('رقم')->searchable()->copyable(),
            TextColumn::make('guest_name')->label('العميل')->formatStateUsing(fn ($state, Order $r) => $r->customerName())->searchable(query: function ($q, $s) {
                $q->where('guest_name', 'like', "%{$s}%")->orWhere('guest_phone', 'like', "%{$s}%")->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            }),
            TextColumn::make('guest_phone')->label('موبايل')->formatStateUsing(fn ($s, Order $r) => $r->customerPhone()),
            TextColumn::make('delivery_date')->date()->label('التسليم'),
            TextColumn::make('total')->money('EGP')->label('الإجمالي'),
            TextColumn::make('paymentMethod.name_ar')->label('الدفع'),
            TextColumn::make('payment_status')->badge(),
            TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->labelAr() : OrderStatus::from($state)->labelAr()),
            TextColumn::make('placed_at')->since()->label('منذ'),
        ])->filters([
            SelectFilter::make('status')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->labelAr()])),
            SelectFilter::make('payment_status')->options([
                'unpaid' => 'unpaid', 'under_review' => 'under_review', 'paid' => 'paid', 'rejected' => 'rejected',
            ]),
        ])->actions([
            Action::make('next')
                ->label('الخطوة التالية')
                ->visible(fn (Order $r) => $r->status->next() !== null)
                ->action(function (Order $record) {
                    $next = $record->status->next();
                    if ($next) {
                        app(OrderService::class)->changeStatus($record, $next, 'admin', auth('admin')->id(), 'تحديث من الجدول');
                        Notification::make()->title('تم تحديث الحالة')->success()->send();
                    }
                }),
        ])->defaultSort('created_at', 'desc');
    }
    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
