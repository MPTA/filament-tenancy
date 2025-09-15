<?php

namespace App\Data;

use App\Models\Base\Currency;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class ItineraryBreakdownData extends Data
{
    public function __construct(
        public Currency $currency,
        public int $vehicle_days,
        public int $vehicle_half_days,
        public int $vehicle_extra_hours,
        #[DataCollectionOf(BreakdownMealData::class)]
        public array $meals = [],
        #[DataCollectionOf(BreakdownCompanionData::class)]
        public array $companions = [],
        #[DataCollectionOf(BreakdownTicketData::class)]
        public array $tickets = [],
        #[DataCollectionOf(BreakdownExperienceData::class)]
        public array $experiences = [],
        #[DataCollectionOf(BreakdownAccommodationData::class)]
        public array $accommodations = [],
        #[DataCollectionOf(BreakdownVehicleTypeData::class)]
        public array $vehicle_types = [],
        #[DataCollectionOf(BreakdownAttractionData::class)]
        public array $attractions = [],
        #[DataCollectionOf(BreakdownExpensesData::class)]
        public array $expenses = [],
    ) {}
}
