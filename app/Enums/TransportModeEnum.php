<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum TransportModeEnum: string
{
    use TranslatableEnum;

    case AIR = 'air';
    case TRAIN = 'train';
    case LAND = 'land';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}