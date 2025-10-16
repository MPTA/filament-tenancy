<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum InquiryDateTypeEnum: string
{
    use TranslatableEnum;

    case FIXED_DATE = 'fixed_date';
    case FLEXIBLE_DATE = 'flexible_date';
    case SERIES = 'series';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
