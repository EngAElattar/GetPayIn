<?php

namespace App\Enums\Stock;

enum StockDirection: string
{
    case IN  = 'in';
    case OUT = 'out';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
