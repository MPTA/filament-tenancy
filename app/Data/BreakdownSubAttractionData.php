<?php

namespace App\Data;

use App\Models\Base\SubAttraction;
use Spatie\LaravelData\Data;

class BreakdownSubAttractionData extends Data
{
    public function __construct(
        public SubAttraction $sub_attraction,
        public float $entry_price,
    ) {}

    /**
     * Get formatted entry price
     */
    public function getFormattedEntryPrice(): string
    {
        return number_format($this->entry_price, 2);
    }

    /**
     * Get sub attraction name
     */
    public function getSubAttractionName(): string
    {
        return $this->sub_attraction->getTranslation('name', app()->getLocale()) ?? $this->sub_attraction->name['en'] ?? '';
    }

    /**
     * Get sub attraction description
     */
    public function getSubAttractionDescription(): string
    {
        return $this->sub_attraction->getTranslation('description', app()->getLocale()) ?? $this->sub_attraction->description['en'] ?? '';
    }
}
