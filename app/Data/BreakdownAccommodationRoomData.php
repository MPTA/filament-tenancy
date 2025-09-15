<?php

namespace App\Data;

use App\Models\Base\RoomCategory;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class BreakdownAccommodationRoomData extends Data
{
    public function __construct(
        public RoomCategory $room_category,
        #[Min(0)]
        public float $price,
    ) {}

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Get room category name
     */
    public function getRoomCategoryName(): string
    {
        return $this->room_category->getTranslation('name', app()->getLocale()) ?? $this->room_category->name['en'] ?? '';
    }

    /**
     * Get room capacity
     */
    public function getRoomCapacity(): int
    {
        return $this->room_category->capacity;
    }
}
