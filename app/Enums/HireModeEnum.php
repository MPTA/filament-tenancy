<?php

namespace App\Enums;

enum HireModeEnum: string
{
    case DAILY = 'daily';
    case HALF_DAY = 'half_day';
    case HOURLY = 'hourly';

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
            self::DAILY => 'Daily',
            self::HALF_DAY => 'Half Day',
            self::HOURLY => 'Hourly',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match ($this) {
            self::DAILY => 'Full day hire (24 hours)',
            self::HALF_DAY => 'Half day hire (up to 8 hours)',
            self::HOURLY => 'Hourly hire',
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