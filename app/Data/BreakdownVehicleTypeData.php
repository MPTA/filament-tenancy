<?php

namespace App\Data;

use App\Models\Base\VehicleCategory;
use App\Models\Tenants\VehicleType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class BreakdownVehicleTypeData extends Data
{
    public function __construct(
        public VehicleType $vehicle_type,
        public VehicleCategory $vehicle_category,
        #[Min(0)]
        public float $per_day_price,
        #[Min(0)]
        public float $extra_hour_price,
        #[Min(0)]
        public float $half_day_price,
        #[Min(0)]
        public int $days,
        #[Min(0)]
        public int $half_days,
        #[Min(0)]
        public int $extra_hours,
        #[Min(0)]
        public float $airport_transfer_price,
        #[Min(0)]
        public float $empty_back_price = 0.0,
        #[Min(0)]
        public int $airport_transfers = 0,
    ) {}

    /**
     * Calculate total price dynamically
     */
    public function getTotalPrice(): float
    {
        $dayTotal = $this->days * $this->per_day_price;
        $halfDayTotal = $this->half_days * $this->half_day_price;
        $extraHoursTotal = $this->extra_hours * $this->extra_hour_price;
        $airportTransferTotal = $this->airport_transfers * $this->airport_transfer_price;
        
        return $dayTotal + $halfDayTotal + $extraHoursTotal + $airportTransferTotal + $this->empty_back_price;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPrice(): string
    {
        return number_format($this->getTotalPrice(), 2);
    }
}
