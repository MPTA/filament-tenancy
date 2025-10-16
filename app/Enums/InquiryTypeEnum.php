<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum InquiryTypeEnum: string
{
    use TranslatableEnum;

    case ITINERARY = 'itinerary';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
