<?php

namespace App\Enums;

enum CompanionCategoryEnum: string
{
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

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match($this) {
            self::TOUR_GUIDE => 'Tour Guide',
            self::STAFF => 'Staff',
            self::TRANSLATOR => 'Translator',
            self::DRIVER => 'Driver',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::TOUR_GUIDE => 'Professional tour guide for sightseeing and cultural experiences',
            self::STAFF => 'General staff member for support and assistance',
            self::TRANSLATOR => 'Language translator for communication support',
            self::DRIVER => 'Professional driver for transportation services',
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
