<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تحديث على طلبك',
            'body' => 'طلب '.$this->order->number.' بقى: '.$this->order->status->labelAr(),
            'url' => '/account/orders/'.$this->order->number,
            'order_number' => $this->order->number,
            'status' => $this->order->status->value,
        ];
    }
}
