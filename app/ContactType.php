<?php

namespace App;

enum ContactType: string
{
    case USER = 'user';
    case LEAD = 'lead';
    case CUSTOMER = 'customer';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum labels for display.
     */
    public function label(): string
    {
        return match($this) {
            self::USER => 'User',
            self::LEAD => 'Lead',
            self::CUSTOMER => 'Customer',
        };
    }
}
