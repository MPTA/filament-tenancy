<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum TicketClassEnum: string
{
    use TranslatableEnum;

    case ECONOMY = 'economy';
    case BUSINESS = 'business';
    case FIRST = 'first';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
