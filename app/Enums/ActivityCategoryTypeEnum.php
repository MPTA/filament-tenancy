<?php

namespace App\Enums;

enum ActivityCategoryTypeEnum: string
{
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

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match($this) {
            self::MEAL => 'Meal',
            self::ATTRACTION => 'Attraction',
            self::TICKET => 'Ticket',
            self::EXPERIENCE => 'Experience',
            self::CUSTOM => 'Custom',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::MEAL => 'Food and dining related activities',
            self::ATTRACTION => 'Tourist attractions and landmarks',
            self::TICKET => 'Ticketed events and shows',
            self::EXPERIENCE => 'Unique experiences and activities',
            self::CUSTOM => 'Custom user-defined activities',
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
