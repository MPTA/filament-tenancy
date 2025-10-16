<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum ActivityCategoryTypeEnum: string
{
    use TranslatableEnum;

    case MEAL = 'meal';
    case ATTRACTION = 'attraction';
    case TICKET = 'ticket';
    case EXPERIENCE = 'experience';
    case CUSTOM = 'custom';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
