<?php

namespace App\Enums\Stock;

enum StockReferenceType: string
{
    case ADMIN = 'admin';
    case HOLD  = 'hold';
    case ORDER = 'order';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
