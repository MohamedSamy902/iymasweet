<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Area;
use App\Models\Cart;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $carts,
        protected CouponService $coupons,
    ) {}

    public function place(
        Cart $cart,
        PaymentMethod $method,
        array $addressData,
        array $slot,
        ?string $customerNotes = null,
        ?string $guestName = null,
        ?string $guestPhone = null,
    ): Order {
        return DB::transaction(function () use ($cart, $method, $addressData, $slot, $customerNotes, $guestName, $guestPhone) {
            $summary = $this->carts->summary($cart);
            $area = isset($addressData['area_id'])
                ? Area::query()->find($addressData['area_id'])
                : null;

            $deliveryFee = (float) ($area?->delivery_fee ?? 0);
            if ($cart->coupon?->type === 'free_delivery') {
                $deliveryFee = 0;
            } elseif ($area?->free_delivery_over && $summary['subtotal'] >= (float) $area->free_delivery_over) {
                $deliveryFee = 0;
            }

            $total = $summary['subtotal'] - $summary['discount'] + $deliveryFee;
            $isCash = $method->type === 'cash';

            $order = Order::query()->create([
                'number' => $this->generateNumber(),
                'user_id' => $cart->user_id,
                'guest_name' => $guestName,
                'guest_phone' => $guestPhone,
                'status' => $isCash ? OrderStatus::Confirmed : OrderStatus::PendingPayment,
                'payment_method_id' => $method->id,
                'payment_status' => $isCash ? PaymentStatus::Unpaid : PaymentStatus::Unpaid,
                'subtotal' => $summary['subtotal'],
                'discount_total' => $summary['discount'],
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'coupon_id' => $cart->coupon_id,
                'coupon_code' => $cart->coupon?->code,
                'delivery_date' => $slot['date'] ?? null,
                'delivery_slot_label' => $slot['label'] ?? null,
                'delivery_slot_start' => $slot['start'] ?? null,
                'delivery_slot_end' => $slot['end'] ?? null,
                'address_snapshot' => $addressData,
                'customer_notes' => $customerNotes,
                'placed_at' => now(),
                'confirmed_at' => $isCash ? now() : null,
                'payment_due_at' => $isCash ? null : now()->addHours((int) setting('store.payment_timeout_hours', 6)),
                'ip' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
            ]);

            foreach ($cart->items as $item) {
                $p = $item->purchasable;
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'purchasable_type' => $item->purchasable_type,
                    'purchasable_id' => $item->purchasable_id,
                    'name_ar' => $p->name_ar ?? $p->name ?? '',
                    'name_en' => $p->name_en ?? null,
                    'image' => $p instanceof Product ? $p->primaryImage()?->path : ($p->image ?? null),
                    'variant_name' => $item->variant?->name_ar,
                    'unit_price' => $item->unit_price_snapshot,
                    'quantity' => $item->quantity,
                    'addons' => $item->addons,
                    'customization' => $item->customization,
                    'reference_image_path' => $item->reference_image_path,
                    'line_total' => $item->lineTotal(),
                    'availability_type_at_order' => is_object($p->availability_type ?? null)
                        ? $p->availability_type->value
                        : ($p->availability_type ?? null),
                ]);

                if ($p instanceof Product && $p->stock_quantity !== null) {
                    $p->decrement('stock_quantity', $item->quantity);
                    if ($p->fresh()->stock_quantity <= 0) {
                        $p->update(['availability_type' => 'sold_out']);
                    }
                }
            }

            if ($cart->coupon && $summary['discount'] > 0) {
                CouponUsage::query()->create([
                    'coupon_id' => $cart->coupon->id,
                    'user_id' => $cart->user_id,
                    'order_id' => $order->id,
                    'discount_amount' => $summary['discount'],
                ]);
            }

            $this->recordStatus($order, null, $order->status->value, 'system', null, 'تم إنشاء الطلب');

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return $order->load(['items', 'paymentMethod', 'statusHistory']);
        });
    }

    public function changeStatus(Order $order, OrderStatus $to, string $byType = 'admin', ?int $byId = null, ?string $note = null): Order
    {
        $from = $order->status;
        $order->update([
            'status' => $to,
            'confirmed_at' => $to === OrderStatus::Confirmed ? ($order->confirmed_at ?? now()) : $order->confirmed_at,
            'delivered_at' => $to === OrderStatus::Delivered ? now() : $order->delivered_at,
        ]);

        $this->recordStatus($order, $from->value, $to->value, $byType, $byId, $note);

        if ($order->user) {
            try {
                $order->user->notify(new \App\Notifications\OrderStatusChangedNotification($order->fresh()));
            } catch (\Throwable) {
            }
        }

        return $order->fresh(['statusHistory']);
    }

    public function submitReceipt(Order $order, string $path, string $hash, ?string $senderName = null, ?string $reference = null): Payment
    {
        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'payment_method_id' => $order->payment_method_id,
            'amount' => $order->total,
            'status' => 'pending',
            'receipt_path' => $path,
            'receipt_hash' => $hash,
            'sender_name' => $senderName,
            'sender_phone_or_reference' => $reference,
            'submitted_at' => now(),
        ]);

        $order->update([
            'status' => OrderStatus::PaymentReview,
            'payment_status' => PaymentStatus::UnderReview,
        ]);

        $this->recordStatus($order, OrderStatus::PendingPayment->value, OrderStatus::PaymentReview->value, 'customer', $order->user_id, 'تم رفع إيصال الدفع');

        return $payment;
    }

    public function approvePayment(Payment $payment, int $adminId): Order
    {
        $payment->update([
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);

        $order = $payment->order;
        $order->update(['payment_status' => PaymentStatus::Paid]);

        return $this->changeStatus($order, OrderStatus::Confirmed, 'admin', $adminId, 'تم قبول الدفع');
    }

    public function rejectPayment(Payment $payment, int $adminId, string $reason): Order
    {
        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
        ]);

        $order = $payment->order;
        $order->update(['payment_status' => PaymentStatus::Rejected]);

        return $this->changeStatus($order, OrderStatus::PaymentRejected, 'admin', $adminId, $reason);
    }

    protected function recordStatus(Order $order, ?string $from, string $to, string $byType, ?int $byId, ?string $note): void
    {
        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by_type' => $byType,
            'changed_by_id' => $byId,
            'note' => $note,
            'notified' => false,
            'created_at' => now(),
        ]);
    }

    public function generateNumber(): string
    {
        $format = (string) setting('store.order_number_format', 'IS-{YY}-{SEQ6}');
        $seq = str_pad((string) (Order::withTrashed()->count() + 1), 6, '0', STR_PAD_LEFT);

        return str_replace(
            ['{YY}', '{YYYY}', '{SEQ6}', '{SEQ}'],
            [now()->format('y'), now()->format('Y'), $seq, (string) (Order::withTrashed()->count() + 1)],
            $format
        );
    }
}
