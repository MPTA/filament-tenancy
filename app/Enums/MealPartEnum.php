<?php

namespace App\Enums;

enum MealPartEnum: string
{
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

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match($this) {
            self::BREAKFAST => 'Breakfast',
            self::LUNCH => 'Lunch',
            self::DINNER => 'Dinner',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::BREAKFAST => 'Morning meal',
            self::LUNCH => 'Midday meal',
            self::DINNER => 'Evening meal',
        };
    }

    /**
     * Get options for select inputs.
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get options with descriptions for select inputs.
     */
    public static function getOptionsWithDescriptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = [
                'label' => $case->label(),
                'description' => $case->description(),
            ];
        }
        return $options;
    }
}
