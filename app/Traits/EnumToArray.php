<?php

namespace App\Traits;

trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
    public static function randomValue(): string
    {
        return array_rand(self::array());
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    /**
     * Compare the current enum instance with another.
     */
    public function is(self $type): bool
    {
        return $this === $type;
    }
    /**
     * Compare the current enum instance with another to see if they're different.
     */
    public function isNot(self $type): bool
    {
        return $this !== $type;
    }
}
