<?php

namespace App\Enums;

enum AvailabilityType: string
{
    case AvailableNow = 'available_now';
    case SoldOut = 'sold_out';
    case AvailableTomorrow = 'available_tomorrow';
    case PreOrder = 'pre_order';

    public function labelAr(): string
    {
        return match ($this) {
            self::AvailableNow => 'متاح الآن',
            self::SoldOut => 'نفد مؤقتاً',
            self::AvailableTomorrow => 'متاح بكرة',
            self::PreOrder => 'بالحجز',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AvailableNow => 'success',
            self::SoldOut => 'gray',
            self::AvailableTomorrow => 'info',
            self::PreOrder => 'warning',
        };
    }
}
