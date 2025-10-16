<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum ChargeModeEnum: string
{
    use TranslatableEnum;

    case PER_PERSON = 'per_person';
    case PER_GROUP = 'per_group';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
