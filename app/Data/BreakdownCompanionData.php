<?php

namespace App\Data;

use App\Models\Base\CompanionCategory;
use App\Models\Tenants\CompanionType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class BreakdownCompanionData extends Data
{
    public function __construct(
        public CompanionType $companion_type,
        public CompanionCategory $companion_category,
        #[Min(0)]
        public float $per_day_price,
        #[Min(0)]
        public float $half_day_price,
        #[Min(0)]
        public float $pickup_price,
        #[Min(0)]
        public int $days,
        #[Min(0)]
        public int $half_days,
        #[Min(0)]
        public int $pickups,
    ) {}

    /**
     * Calculate total price dynamically
     */
    public function getTotalPrice(): float
    {
        $dayTotal = $this->days * $this->per_day_price;
        $halfDayTotal = $this->half_days * $this->half_day_price;
        $pickupTotal = $this->pickups * $this->pickup_price;
        
        return $dayTotal + $halfDayTotal + $pickupTotal;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }
}
