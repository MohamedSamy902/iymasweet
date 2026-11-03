<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    public function resolve(?string $sessionToken = null): Cart
    {
        $sessionToken ??= session()->getId();

        if (Auth::check()) {
            $cart = Cart::query()->firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_token' => $sessionToken, 'expires_at' => now()->addDays(14)]
            );

            $guest = Cart::query()
                ->whereNull('user_id')
                ->where('session_token', $sessionToken)
                ->with('items')
                ->first();

            if ($guest && $guest->id !== $cart->id) {
                foreach ($guest->items as $item) {
                    $item->update(['cart_id' => $cart->id]);
                }
                $guest->delete();
            }

            return $cart->load(['items.purchasable', 'items.variant', 'coupon']);
        }

        return Cart::query()->firstOrCreate(
            ['session_token' => $sessionToken, 'user_id' => null],
            ['expires_at' => now()->addDays(7)]
        )->load(['items.purchasable', 'items.variant', 'coupon']);
    }

    public function addProduct(Cart $cart, Product $product, int $quantity = 1, ?int $variantId = null, array $addons = [], array $customization = [], ?string $referenceImage = null): CartItem
    {
        if ($product->availability_type === AvailabilityType::SoldOut || ($product->stock_quantity !== null && $product->stock_quantity <= 0)) {
            abort(422, t('product.sold_out', 'خلص'));
        }

        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $product->id)->findOrFail($variantId)
            : $product->variants()->where('is_default', true)->first();

        $unit = (float) ($variant?->price ?? $product->base_price);
        foreach ($addons as $addon) {
            $unit += (float) ($addon['price'] ?? 0);
        }

        $quantity = max($product->min_qty ?: 1, $quantity);
        if ($product->max_qty_per_order) {
            $quantity = min($quantity, $product->max_qty_per_order);
        }

        return $cart->items()->create([
            'purchasable_type' => Product::class,
            'purchasable_id' => $product->id,
            'variant_id' => $variant?->id,
            'quantity' => $quantity,
            'addons' => $addons ?: null,
            'customization' => $customization ?: null,
            'reference_image_path' => $referenceImage,
            'unit_price_snapshot' => $unit,
        ]);
    }

    public function addBundle(Cart $cart, Bundle $bundle, int $quantity = 1, array $customization = []): CartItem
    {
        return $cart->items()->create([
            'purchasable_type' => Bundle::class,
            'purchasable_id' => $bundle->id,
            'quantity' => $quantity,
            'customization' => $customization ?: null,
            'unit_price_snapshot' => $bundle->price,
        ]);
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => max(1, $quantity)]);

        return $item->fresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function applyCoupon(Cart $cart, string $code): Cart
    {
        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->where('is_active', true)->firstOrFail();
        $cart->update(['coupon_id' => $coupon->id]);

        return $cart->fresh(['items', 'coupon']);
    }

    /** @return array{subtotal: float, discount: float, total: float, count: int, farthest_availability: ?string} */
    public function summary(Cart $cart): array
    {
        $subtotal = 0.0;
        $count = 0;
        $farthest = null;
        $rank = [
            AvailabilityType::AvailableNow->value => 1,
            AvailabilityType::AvailableTomorrow->value => 2,
            AvailabilityType::PreOrder->value => 3,
            AvailabilityType::SoldOut->value => 0,
        ];

        foreach ($cart->items as $item) {
            $subtotal += $item->lineTotal();
            $count += $item->quantity;
            $availability = $item->purchasable->availability_type ?? null;
            $value = is_object($availability) ? $availability->value : $availability;
            if ($value && ($rank[$value] ?? 0) >= ($rank[$farthest] ?? 0)) {
                $farthest = $value;
            }
        }

        $discount = 0.0;
        if ($cart->coupon) {
            $discount = app(CouponService::class)->calculateDiscount($cart->coupon, $subtotal);
        }

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'total' => round(max(0, $subtotal - $discount), 2),
            'count' => $count,
            'farthest_availability' => $farthest,
            'coupon_code' => $cart->coupon?->code,
        ];
    }

    public function toArray(Cart $cart): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $cart->id,
            'items' => $cart->items->map(function (CartItem $item) use ($locale) {
                $p = $item->purchasable;

                return [
                    'id' => $item->id,
                    'name' => method_exists($p, 'name') ? $p->name($locale) : ($p->name_ar ?? ''),
                    'image' => $p instanceof Product ? ($p->primaryImage()?->path) : ($p->image ?? null),
                    'variant' => $item->variant?->name($locale),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price_snapshot,
                    'line_total' => $item->lineTotal(),
                    'addons' => $item->addons,
                    'customization' => $item->customization,
                    'availability' => is_object($p->availability_type ?? null)
                        ? $p->availability_type->value
                        : ($p->availability_type ?? null),
                    'slug' => $p->slug ?? null,
                    'type' => class_basename($item->purchasable_type),
                ];
            })->values(),
            'summary' => $this->summary($cart),
        ];
    }
}
