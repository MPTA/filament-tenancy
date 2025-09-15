<?php

namespace App\Data;

use App\Models\Base\Accommodation;
use App\Models\Base\City;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class BreakdownAccommodationData extends Data
{
    public function __construct(
        public Accommodation $accommodation,
        public City $city,
        public int $stay_nights,
        #[DataCollectionOf(BreakdownAccommodationRoomData::class)]
        public array $rooms = [],
    ) {}

    /**
     * Get accommodation name
     */
    public function getAccommodationName(): string
    {
        return $this->accommodation->getTranslation('name', app()->getLocale()) ?? $this->accommodation->name['en'] ?? '';
    }

    /**
     * Get city name
     */
    public function getCityName(): string
    {
        return $this->city->getTranslation('name', app()->getLocale()) ?? $this->city->name['en'] ?? '';
    }

    /**
     * Calculate total price for all rooms
     */
    public function getTotalPrice(): float
    {
        return collect($this->rooms)
            ->sum(fn($room) => $room->price * $this->stay_nights);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }

    /**
     * Get total number of rooms
     */
    public function getTotalRooms(): int
    {
        return count($this->rooms);
    }

    /**
     * Get accommodation star rating
     */
    public function getStarRating(): int
    {
        return $this->accommodation->star_rating;
    }
}
