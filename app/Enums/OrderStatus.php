<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case PaymentReview = 'payment_review';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case PaymentRejected = 'payment_rejected';
    case Refunded = 'refunded';

    public function labelAr(): string
    {
        return match ($this) {
            self::PendingPayment => 'في انتظار الدفع',
            self::PaymentReview => 'مراجعة الإيصال',
            self::Confirmed => 'مؤكد',
            self::Preparing => 'قيد التجهيز',
            self::Ready => 'جاهز للتوصيل',
            self::OutForDelivery => 'خرج للتوصيل',
            self::Delivered => 'تم التسليم',
            self::Cancelled => 'ملغي',
            self::PaymentRejected => 'الدفع مرفوض',
            self::Refunded => 'مسترجع',
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::PendingPayment => self::PaymentReview,
            self::PaymentReview => self::Confirmed,
            self::Confirmed => self::Preparing,
            self::Preparing => self::Ready,
            self::Ready => self::OutForDelivery,
            self::OutForDelivery => self::Delivered,
            default => null,
        };
    }
}
