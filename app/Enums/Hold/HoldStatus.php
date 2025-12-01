<?php

namespace App\Enums\Hold;

enum HoldStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED   = 'expired';
    case USED = 'used';
    case RELEASED = 'released';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
