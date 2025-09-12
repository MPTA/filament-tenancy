<?php

namespace App\Enums;

enum ChargeModeEnum: string
{
    case PER_PERSON = 'per_person';
    case PER_GROUP = 'per_group';

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
            self::PER_PERSON => 'Per Person',
            self::PER_GROUP => 'Per Group',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::PER_PERSON => 'Charged per individual person',
            self::PER_GROUP => 'Charged per group regardless of size',
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
