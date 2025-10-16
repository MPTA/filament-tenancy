<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum CompanionCategoryEnum: string
{
    use TranslatableEnum;

    case TOUR_GUIDE = 'tour_guide';
    case STAFF = 'staff';
    case TRANSLATOR = 'translator';
    case DRIVER = 'driver';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
