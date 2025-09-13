<?php

namespace App\Enums;

enum InquiryDateTypeEnum: string
{
    case FIXED_DATE = 'fixed_date';
    case FLEXIBLE_DATE = 'flexible_date';
    case SERIES = 'series';

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
        return match($this) {
            self::FIXED_DATE => 'Fixed Date',
            self::FLEXIBLE_DATE => 'Flexible Date',
            self::SERIES => 'Series',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::FIXED_DATE => 'Specific fixed dates for travel',
            self::FLEXIBLE_DATE => 'Flexible date range for travel',
            self::SERIES => 'Recurring travel series',
        };
    }

    /**
     * Get options for select inputs.
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get options with descriptions for select inputs.
     */
    public static function getOptionsWithDescriptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => [
                    'label' => $case->label(),
                    'description' => $case->description(),
                ]
            ])
            ->toArray();
    }
}
