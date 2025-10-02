<?php

namespace App\Data;

use App\Models\Base\MealCategory;
use App\Models\Tenants\MealType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class BreakdownMealData extends Data
{
    public function __construct(
        public MealType $meal_type,
        public MealCategory $meal_category,
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
}
