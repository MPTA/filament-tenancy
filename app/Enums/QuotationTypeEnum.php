<?php

namespace App\Enums;

enum QuotationTypeEnum: string
{
    case GENERAL = 'general';
    case ITINERARY = 'itinerary';

    public function getLabel(): string
    {
        return match ($this) {
            self::GENERAL => 'General',
            self::ITINERARY => 'Itinerary',
        };
    }
}
