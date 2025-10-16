<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum HireModeEnum: string
{
    use TranslatableEnum;

    case DAILY = 'daily';
    case HALF_DAY = 'half_day';
    case HOURLY = 'hourly';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}