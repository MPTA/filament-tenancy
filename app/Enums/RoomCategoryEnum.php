<?php

namespace App\Enums;

use App\Traits\TranslatableEnum;

enum RoomCategoryEnum: string
{
    use TranslatableEnum;

    case TWIN = 'twin';
    case SINGLE = 'single';
    case DOUBLE_FOR_TWO = 'double_for_two';
    case DOUBLE_FOR_ONE = 'double_for_one';
    case TRIPLE = 'triple';
    case SUITE_FOR_ONE = 'suite_for_one';
    case SUITE_FOR_TWO = 'suite_for_two';

    /**
     * Backward compatibility alias for getDisplayName()
     */
    public function getDisplayName(): string
    {
        return $this->label();
    }

    /**
     * Get all enum values as an array.
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
