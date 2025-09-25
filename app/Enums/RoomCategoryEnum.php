<?php

namespace App\Enums;

enum RoomCategoryEnum: string
{
    case TWIN = 'twin';
    case SINGLE = 'single';
    case DOUBLE_FOR_TWO = 'double_for_two';
    case DOUBLE_FOR_ONE = 'double_for_one';
    case TRIPLE = 'triple';
    case SUITE_FOR_ONE = 'suite_for_one';
    case SUITE_FOR_TWO = 'suite_for_two';

    /**
     * Get the display name for the enum value.
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::TWIN => 'Twin',
            self::SINGLE => 'Single',
            self::DOUBLE_FOR_TWO => 'Double For Two',
            self::DOUBLE_FOR_ONE => 'Double For One',
            self::TRIPLE => 'Triple',
            self::SUITE_FOR_ONE => 'Suite For One',
            self::SUITE_FOR_TWO => 'Suite For Two',
        };
    }

    /**
     * Get all enum values as an array.
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum values with their display names.
     */
    public static function getOptions(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->getDisplayName()],
            self::cases()
        );
    }
}
