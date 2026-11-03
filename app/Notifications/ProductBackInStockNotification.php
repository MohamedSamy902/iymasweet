<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProductBackInStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'المنتج متاح مجدداً',
            'body' => $this->product->name_ar.' بقى متاح دلوقتي',
            'url' => '/product/'.$this->product->slug,
            'product_id' => $this->product->id,
        ];
    }
}
