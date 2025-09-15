<?php

namespace App\Data;

use App\Models\Base\VehicleCategory;
use App\Models\Tenants\VehicleType;
use Spatie\LaravelData\Data;

class BreakdownVehicleTypeData extends Data
{
    public function __construct(
        public VehicleType $vehicle_type,
        public VehicleCategory $vehicle_category,
        public float $per_day_price,
        public float $extra_hour_price,
        public float $half_day_price,
        public int $days,
        public int $half_days,
        public int $extra_hours,
        public float $airport_transfer_price,
        public float $empty_back_price = 0.0,
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
