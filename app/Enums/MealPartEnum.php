<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum MealPartEnum: string
{
    use TranslatableEnum;

    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
