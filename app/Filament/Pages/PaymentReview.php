<?php
namespace App\Filament\Pages;
use App\Models\Payment;
use App\Services\OrderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;
class PaymentReview extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'مراجعة الدفع';
    protected static string|UnitEnum|null $navigationGroup = 'المبيعات';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'مراجعة الإيصالات';
    protected string $view = 'filament.pages.payment-review';
    public function table(Table $table): Table
    {
        return $table
            ->query(Payment::query()->with(['order', 'method'])->where('status', 'pending')->latest())
            ->columns([
                TextColumn::make('order.number')->label('طلب'),
                TextColumn::make('amount')->money('EGP'),
                TextColumn::make('sender_name')->label('المرسل'),
                TextColumn::make('submitted_at')->since(),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('approve')->label('قبول')->color('success')
                    ->action(function (Payment $record) {
                        app(OrderService::class)->approvePayment($record, auth('admin')->id());
                        Notification::make()->title('تم القبول')->success()->send();
                    }),
                \Filament\Tables\Actions\Action::make('reject')->label('رفض')->color('danger')
                    ->form([Textarea::make('reason')->required()->label('السبب')])
                    ->action(function (Payment $record, array $data) {
                        app(OrderService::class)->rejectPayment($record, auth('admin')->id(), $data['reason']);
                        Notification::make()->title('تم الرفض')->warning()->send();
                    }),
            ]);
    }
}
