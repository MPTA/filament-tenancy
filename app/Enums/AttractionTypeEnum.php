<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum AttractionTypeEnum: string
{
    use TranslatableEnum;

    case NATURAL = 'natural';
    case MAN_MADE = 'man_made';
    case CULTURAL = 'cultural';
    case SPORT = 'sport';
    case EVENTS = 'events';
    case LEISURE = 'leisure';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
