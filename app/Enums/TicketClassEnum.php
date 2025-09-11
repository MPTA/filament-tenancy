<?php

namespace App\Enums;

enum TicketClassEnum: string
{
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

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match($this) {
            self::ECONOMY => 'Economy',
            self::BUSINESS => 'Business',
            self::FIRST => 'First Class',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::ECONOMY => 'Standard economy class ticket',
            self::BUSINESS => 'Business class ticket with enhanced services',
            self::FIRST => 'First class ticket with premium services',
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
