<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum ContactTypeEnum: string
{
    use TranslatableEnum;

    case USER = 'user';
    case LEAD = 'lead';
    case CUSTOMER = 'customer';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
