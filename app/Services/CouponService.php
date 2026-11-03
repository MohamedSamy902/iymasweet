<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if (! $coupon->is_active) {
            return 0;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return 0;
        }

        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            return 0;
        }

        if ($coupon->min_order && $subtotal < (float) $coupon->min_order) {
            return 0;
        }

        if ($coupon->usage_limit !== null && $coupon->usages()->count() >= $coupon->usage_limit) {
            return 0;
        }

        $discount = match ($coupon->type) {
            'percent' => $subtotal * ((float) $coupon->value / 100),
            'fixed' => (float) $coupon->value,
            'free_delivery' => 0,
            default => 0,
        };

        if ($coupon->max_discount) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        return round(max(0, $discount), 2);
    }
}
