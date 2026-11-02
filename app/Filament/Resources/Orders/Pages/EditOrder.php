<?php
namespace App\Filament\Resources\Orders\Pages;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Payment;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Action::make('approvePayment')
                ->label('قبول الدفع')
                ->color('success')
                ->visible(fn () => $this->record->status === OrderStatus::PaymentReview)
                ->action(function () {
                    $payment = $this->record->payments()->latest()->first();
                    if ($payment) {
                        app(OrderService::class)->approvePayment($payment, auth('admin')->id());
                        Notification::make()->title('تم قبول الدفع')->success()->send();
                        $this->refreshFormData(['status', 'payment_status']);
                    }
                }),
            Action::make('rejectPayment')
                ->label('رفض الدفع')
                ->color('danger')
                ->visible(fn () => $this->record->status === OrderStatus::PaymentReview)
                ->form([Textarea::make('reason')->label('سبب الرفض')->required()])
                ->action(function (array $data) {
                    $payment = $this->record->payments()->latest()->first();
                    if ($payment) {
                        app(OrderService::class)->rejectPayment($payment, auth('admin')->id(), $data['reason']);
                        Notification::make()->title('تم رفض الدفع')->warning()->send();
                        $this->refreshFormData(['status', 'payment_status']);
                    }
                }),
            Action::make('copyDelivery')
                ->label('انسخ بيانات التوصيل')
                ->action(function () {
                    $a = $this->record->address_snapshot ?? [];
                    $text = trim(($a['recipient_name'] ?? '')."\n".($a['phone'] ?? '')."\n".($a['governorate'] ?? '').' - '.($a['area'] ?? '')."\n".($a['street'] ?? '').' '.($a['building'] ?? '')."\n".($a['landmark'] ?? ''));
                    Notification::make()->title('انسخ من هنا')->body($text)->success()->persistent()->send();
                }),
        ];
    }
}
