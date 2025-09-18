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

    /**
     * Get options for select inputs.
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
