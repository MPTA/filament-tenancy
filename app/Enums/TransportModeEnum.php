<?php

namespace App\Enums;

enum TransportModeEnum: string
{
    case AIR = 'air';
    case TRAIN = 'train';
    case LAND = 'land';

    /**
     * Get all enum values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::AIR => 'Air',
            self::TRAIN => 'Train',
            self::LAND => 'Land',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match ($this) {
            self::AIR => 'Air transportation (flights)',
            self::TRAIN => 'Train transportation',
            self::LAND => 'Land transportation (bus, car, etc.)',
        };
    }

    /**
     * Get options for select fields.
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get options with descriptions for select fields.
     */
    public static function getOptionsWithDescriptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label() . ' - ' . $case->description()])
            ->toArray();
    }
}