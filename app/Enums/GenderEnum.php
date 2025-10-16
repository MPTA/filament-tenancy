<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum GenderEnum: string
{
    use TranslatableEnum;

    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}


