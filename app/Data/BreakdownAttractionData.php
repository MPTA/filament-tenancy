<?php

namespace App\Data;

use App\Models\Base\Attraction;
use App\Models\Base\City;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class BreakdownAttractionData extends Data
{
    public function __construct(
        public Attraction $attraction,
        public bool $is_outview,
        public City $city,
        public float $entry_price,
        #[DataCollectionOf(BreakdownSubAttractionData::class)]
        public array $sub_attractions = [],
    ) {}

    /**
     * Get formatted entry price
     */
    public function getFormattedEntryPrice(): string
    {
        return number_format($this->entry_price, 2);
    }

    /**
     * Get attraction name
     */
    public function getAttractionName(): string
    {
        return $this->attraction->getTranslation('name', app()->getLocale()) ?? $this->attraction->name['en'] ?? '';
    }

    /**
     * Get attraction description
     */
    public function getAttractionDescription(): string
    {
        return $this->attraction->getTranslation('description', app()->getLocale()) ?? $this->attraction->description['en'] ?? '';
    }

    /**
     * Get city name
     */
    public function getCityName(): string
    {
        return $this->city->getTranslation('name', app()->getLocale()) ?? $this->city->name['en'] ?? '';
    }

    /**
     * Calculate total price including sub attractions
     */
    public function getTotalPrice(): float
    {
        $subAttractionsTotal = collect($this->sub_attractions)
            ->sum(fn($subAttraction) => $subAttraction->entry_price);
        
        return $this->entry_price + $subAttractionsTotal;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }
}
