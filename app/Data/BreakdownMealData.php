<?php

namespace App\Data;

use App\Models\Base\MealCategory;
use App\Models\Tenants\MealType;
use Spatie\LaravelData\Data;

class BreakdownMealData extends Data
{
    public function __construct(
        public MealType $meal_type,
        public MealCategory $meal_category,
        public int $qty,
        public float $price,
    ) {}

    /**
     * Calculate total price dynamically
     */
    public function getTotal(): float
    {
        return $this->qty * $this->price;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotal(): string
    {
        return number_format($this->getTotal(), 2);
    }
}
