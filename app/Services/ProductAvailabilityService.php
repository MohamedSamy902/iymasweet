<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Models\Product;
use App\Models\StockAlert;
use App\Notifications\ProductBackInStockNotification;
use Illuminate\Support\Facades\Notification;

class ProductAvailabilityService
{
    public function syncScheduled(): int
    {
        $updated = 0;

        Product::query()
            ->where('availability_type', AvailabilityType::AvailableTomorrow)
            ->whereNotNull('available_from')
            ->where('available_from', '<=', now())
            ->each(function (Product $product) use (&$updated) {
                $product->update(['availability_type' => AvailabilityType::AvailableNow]);
                $this->notifySubscribers($product);
                $updated++;
            });

        Product::query()
            ->whereNotNull('available_until')
            ->where('available_until', '<', now())
            ->where('availability_type', '!=', AvailabilityType::SoldOut)
            ->update(['availability_type' => AvailabilityType::SoldOut]);

        Product::query()
            ->whereNotNull('stock_quantity')
            ->where('stock_quantity', '<=', 0)
            ->where('availability_type', '!=', AvailabilityType::SoldOut)
            ->update(['availability_type' => AvailabilityType::SoldOut]);

        return $updated;
    }

    public function markAvailable(Product $product): void
    {
        $product->update(['availability_type' => AvailabilityType::AvailableNow]);
        $this->notifySubscribers($product);
    }

    public function notifySubscribers(Product $product): void
    {
        $alerts = StockAlert::query()->where('product_id', $product->id)->whereNull('notified_at')->with('user')->get();

        foreach ($alerts as $alert) {
            if ($alert->user) {
                try {
                    $alert->user->notify(new ProductBackInStockNotification($product));
                } catch (\Throwable) {
                    // Notification channel may be unavailable in local/dev.
                }
            }
            $alert->update(['notified_at' => now()]);
        }

        StockAlert::query()->where('product_id', $product->id)->whereNotNull('notified_at')->delete();
    }

    public function rebuildSearchText(Product $product): void
    {
        $parts = [
            $product->name_ar,
            $product->name_en,
            $product->short_description_ar,
            $product->ingredients_ar,
            $product->category?->name_ar,
        ];

        foreach ($product->tags as $tag) {
            $parts[] = $tag->name_ar;
            $parts[] = $tag->name_en;
        }

        $normalized = app(ChatbotService::class)->normalize(implode(' ', array_filter($parts)));
        $product->updateQuietly(['search_text' => $normalized]);
    }
}
