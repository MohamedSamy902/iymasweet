<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case UnderReview = 'under_review';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Refunded = 'refunded';
}
