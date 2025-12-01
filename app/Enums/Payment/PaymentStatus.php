<?php

namespace App\Enums\Payment;

enum PaymentStatus: string
{
    case PENDING         = 'pending';
    case SUCCESS            = 'success';
    case FAILED          = 'failed';
    case CANCELED        = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
