<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOffer extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'vehicle_type_id',
        'leaders_qty',
        'leader_room_category_id',
        'pax_qty',
        'drivers_qty',
        'markup',
        'vehicle_days_qty',
        'vehicle_half_days_qty',
        'vehicle_airport_transfers_qty',
        'vehicle_day_price',
        'vehicle_half_day_price',
        'vehicle_airport_transfer_price',
        'tenant_id',
    ];

    protected $casts = [
        'leaders_qty' => 'integer',
        'pax_qty' => 'integer',
        'drivers_qty' => 'integer',
        'markup' => 'decimal:2',
        'vehicle_days_qty' => 'integer',
        'vehicle_half_days_qty' => 'integer',
        'vehicle_airport_transfers_qty' => 'integer',
        'vehicle_day_price' => 'decimal:2',
        'vehicle_half_day_price' => 'decimal:2',
        'vehicle_airport_transfer_price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group for this offer.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the vehicle type for this offer.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Get the leader room category for this offer.
     */
    public function leaderRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'leader_room_category_id');
    }

    /**
     * Get the quotation offer prices for this offer (one-to-many relationship).
     */
    public function quotationOfferPrices(): HasMany
    {
        return $this->hasMany(QuotationOfferPrice::class);
    }

    /**
     * Get the quotation offer leader meals for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderMeals(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderMeal::class);
    }

    /**
     * Get the quotation offer leader tickets for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderTickets(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderTicket::class);
    }

    /**
     * Get the quotation offer leader attractions for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderAttractions(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderAttraction::class);
    }

    /**
     * Get the quotation offer leader expenses for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderExpenses(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderExpense::class);
    }

    /**
     * Get the quotation offer leader experiences for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderExperiences(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderExperience::class);
    }

    /**
     * Get the quotation offer leader accommodations for this offer (one-to-many relationship).
     */
    public function quotationOfferLeaderAccommodations(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderAccommodation::class);
    }

    /**
     * Get the quotation offer driver meals for this offer (one-to-many relationship).
     */
    public function quotationOfferDriverMeals(): HasMany
    {
        return $this->hasMany(QuotationOfferDriverMeal::class);
    }

    /**
     * Get the quotation offer driver accommodations for this offer (one-to-many relationship).
     */
    public function quotationOfferDriverAccommodations(): HasMany
    {
        return $this->hasMany(QuotationOfferDriverAccommodation::class);
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by vehicle type.
     */
    public function scopeByVehicleType($query, $vehicleTypeId)
    {
        return $query->where('vehicle_type_id', $vehicleTypeId);
    }

    /**
     * Scope a query to filter by leader room category.
     */
    public function scopeByLeaderRoomCategory($query, $roomCategoryId)
    {
        return $query->where('leader_room_category_id', $roomCategoryId);
    }

    /**
     * Scope a query to filter by leaders quantity range.
     */
    public function scopeByLeadersQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('leaders_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by pax quantity range.
     */
    public function scopeByPaxQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('pax_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by drivers quantity range.
     */
    public function scopeByDriversQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('drivers_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by markup range.
     */
    public function scopeByMarkupRange($query, $minMarkup, $maxMarkup)
    {
        return $query->whereBetween('markup', [$minMarkup, $maxMarkup]);
    }


    /**
     * Scope a query to filter by minimum capacity.
     */
    public function scopeByMinCapacity($query, $capacity)
    {
        return $query->whereHas('vehicleType', function ($q) use ($capacity) {
            $q->where('capacity_from', '<=', $capacity)
              ->where('capacity_to', '>=', $capacity);
        });
    }

    /**
     * Get the formatted markup percentage.
     */
    public function getFormattedMarkupAttribute(): string
    {
        return number_format((float) $this->markup, 2) . '%';
    }

    /**
     * Get the markup as decimal (e.g., 0.15 for 15%).
     */
    public function getMarkupDecimalAttribute(): float
    {
        return (float) $this->markup / 100;
    }

    /**
     * Get the vehicle type name.
     */
    public function getVehicleTypeNameAttribute(): ?string
    {
        return $this->vehicleType?->name;
    }

    /**
     * Get the leader room category name.
     */
    public function getLeaderRoomCategoryNameAttribute(): ?string
    {
        return $this->leaderRoomCategory?->name;
    }

    /**
     * Get the total people count (leaders + pax + drivers).
     */
    public function getTotalPeopleAttribute(): int
    {
        return $this->leaders_qty + $this->pax_qty + $this->drivers_qty;
    }

    /**
     * Check if this offer has leaders.
     */
    public function getHasLeadersAttribute(): bool
    {
        return $this->leaders_qty > 0;
    }

    /**
     * Check if this offer has drivers.
     */
    public function getHasDriversAttribute(): bool
    {
        return $this->drivers_qty > 0;
    }

    /**
     * Check if this offer has pax.
     */
    public function getHasPaxAttribute(): bool
    {
        return $this->pax_qty > 0;
    }

    /**
     * Get the vehicle capacity range.
     */
    public function getVehicleCapacityRangeAttribute(): ?string
    {
        return $this->vehicleType?->capacity_range;
    }

    /**
     * Check if the total people fit in the vehicle capacity.
     */
    public function getFitsInVehicleAttribute(): bool
    {
        if (!$this->vehicleType) {
            return false;
        }
        
        $totalPeople = $this->total_people;
        return $totalPeople >= $this->vehicleType->capacity_from && 
               $totalPeople <= $this->vehicleType->capacity_to;
    }

    /**
     * Get capacity utilization percentage.
     */
    public function getCapacityUtilizationAttribute(): float
    {
        if (!$this->vehicleType || $this->vehicleType->capacity_to == 0) {
            return 0;
        }
        
        return ($this->total_people / $this->vehicleType->capacity_to) * 100;
    }

    /**
     * Get formatted capacity utilization.
     */
    public function getFormattedCapacityUtilizationAttribute(): string
    {
        return number_format($this->capacity_utilization, 1) . '%';
    }

    /**
     * Get total vehicle days (full days + half days).
     */
    public function getTotalVehicleDaysAttribute(): float
    {
        return $this->vehicle_days_qty + ($this->vehicle_half_days_qty * 0.5);
    }

    /**
     * Get total vehicle cost.
     */
    public function getTotalVehicleCostAttribute(): float
    {
        return ($this->vehicle_days_qty * (float) $this->vehicle_day_price) +
               ($this->vehicle_half_days_qty * (float) $this->vehicle_half_day_price) +
               ($this->vehicle_airport_transfers_qty * (float) $this->vehicle_airport_transfer_price);
    }

    /**
     * Get formatted total vehicle cost.
     */
    public function getFormattedTotalVehicleCostAttribute(): string
    {
        return number_format($this->total_vehicle_cost, 2);
    }

    /**
     * Get formatted vehicle day price.
     */
    public function getFormattedVehicleDayPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_day_price, 2);
    }

    /**
     * Get formatted vehicle half day price.
     */
    public function getFormattedVehicleHalfDayPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_half_day_price, 2);
    }

    /**
     * Get formatted vehicle airport transfer price.
     */
    public function getFormattedVehicleAirportTransferPriceAttribute(): string
    {
        return number_format((float) $this->vehicle_airport_transfer_price, 2);
    }

    /**
     * Get vehicle pricing breakdown.
     */
    public function getVehiclePricingBreakdownAttribute(): array
    {
        return [
            'days' => [
                'qty' => $this->vehicle_days_qty,
                'price' => (float) $this->vehicle_day_price,
                'total' => $this->vehicle_days_qty * (float) $this->vehicle_day_price,
            ],
            'half_days' => [
                'qty' => $this->vehicle_half_days_qty,
                'price' => (float) $this->vehicle_half_day_price,
                'total' => $this->vehicle_half_days_qty * (float) $this->vehicle_half_day_price,
            ],
            'airport_transfers' => [
                'qty' => $this->vehicle_airport_transfers_qty,
                'price' => (float) $this->vehicle_airport_transfer_price,
                'total' => $this->vehicle_airport_transfers_qty * (float) $this->vehicle_airport_transfer_price,
            ],
            'total_cost' => $this->total_vehicle_cost,
            'total_days' => $this->total_vehicle_days,
        ];
    }

    /**
     * Scope a query to filter by vehicle days quantity range.
     */
    public function scopeByVehicleDaysQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by vehicle half days quantity range.
     */
    public function scopeByVehicleHalfDaysQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_half_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by vehicle airport transfers quantity range.
     */
    public function scopeByVehicleAirportTransfersQtyRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('vehicle_airport_transfers_qty', [$minQty, $maxQty]);
    }

    /**
     * Check if this offer has vehicle costs.
     */
    public function getHasVehicleCostsAttribute(): bool
    {
        return $this->total_vehicle_cost > 0;
    }

    /**
     * Get total leader meal cost.
     */
    public function getTotalLeaderMealCostAttribute(): float
    {
        return $this->quotationOfferLeaderMeals()->sum('price');
    }

    /**
     * Get total leader ticket cost.
     */
    public function getTotalLeaderTicketCostAttribute(): float
    {
        return $this->quotationOfferLeaderTickets()->sum('price');
    }

    /**
     * Get total leader attraction cost.
     */
    public function getTotalLeaderAttractionCostAttribute(): float
    {
        $attractionsTotal = $this->quotationOfferLeaderAttractions()->sum('price');
        $subAttractionsTotal = $this->quotationOfferLeaderAttractions()
            ->with('subAttractions')
            ->get()
            ->sum(function ($attraction) {
                return $attraction->subAttractions->sum('price');
            });
        return $attractionsTotal + $subAttractionsTotal;
    }

    /**
     * Get total leader expense cost.
     */
    public function getTotalLeaderExpenseCostAttribute(): float
    {
        return $this->quotationOfferLeaderExpenses()->sum('price');
    }

    /**
     * Get total leader experience cost.
     */
    public function getTotalLeaderExperienceCostAttribute(): float
    {
        return $this->quotationOfferLeaderExperiences()->sum('price');
    }

    /**
     * Get total leader accommodation cost.
     */
    public function getTotalLeaderAccommodationCostAttribute(): float
    {
        return $this->quotationOfferLeaderAccommodations()
            ->get()
            ->sum(function ($accommodation) {
                return $accommodation->nights * $accommodation->night_price;
            });
    }

    /**
     * Get total leader cost (all categories).
     */
    public function getTotalLeaderCostAttribute(): float
    {
        return $this->total_leader_meal_cost +
               $this->total_leader_ticket_cost +
               $this->total_leader_attraction_cost +
               $this->total_leader_expense_cost +
               $this->total_leader_experience_cost +
               $this->total_leader_accommodation_cost;
    }

    /**
     * Get formatted total leader cost.
     */
    public function getFormattedTotalLeaderCostAttribute(): string
    {
        return number_format($this->total_leader_cost, 2);
    }

    /**
     * Get total driver meal cost.
     */
    public function getTotalDriverMealCostAttribute(): float
    {
        return $this->quotationOfferDriverMeals()
            ->get()
            ->sum(function ($meal) {
                return $meal->qty * $meal->price;
            });
    }

    /**
     * Get total driver accommodation cost.
     */
    public function getTotalDriverAccommodationCostAttribute(): float
    {
        return $this->quotationOfferDriverAccommodations()
            ->get()
            ->sum(function ($accommodation) {
                return $accommodation->nights * $accommodation->night_price;
            });
    }

    /**
     * Get total driver cost (all categories).
     */
    public function getTotalDriverCostAttribute(): float
    {
        return $this->total_driver_meal_cost + $this->total_driver_accommodation_cost;
    }

    /**
     * Get formatted total driver cost.
     */
    public function getFormattedTotalDriverCostAttribute(): string
    {
        return number_format($this->total_driver_cost, 2);
    }

    /**
     * Get formatted total driver meal cost.
     */
    public function getFormattedTotalDriverMealCostAttribute(): string
    {
        return number_format($this->total_driver_meal_cost, 2);
    }

    /**
     * Get formatted total driver accommodation cost.
     */
    public function getFormattedTotalDriverAccommodationCostAttribute(): string
    {
        return number_format($this->total_driver_accommodation_cost, 2);
    }


    /**
     * Calculate and create driver meal costs based on offer group settings.
     */
    public function calculateDriverMealCosts(): void
    {
        $offerGroup = $this->quotationOfferGroup;
        
        // Check if driver meal should be included
        if (!$offerGroup || !$offerGroup->is_include_driver_meal) {
            return;
        }

        // Check if drivers quantity is at least 1
        if ($this->drivers_qty < 1) {
            return;
        }

        // Get breakdown for base meal budget
        $breakdown = $offerGroup->quotationItinerary->breakdown;
        if (!$breakdown) {
            return;
        }

        // Check if driver same meal is false (case 1: base budget calculation)
        if (!$offerGroup->is_driver_same_meal) {
            // Case 1: Base budget calculation
            if (!$breakdown->driver_base_meal_budget) {
                return;
            }
            $this->calculateDriverMealCostsCase1($breakdown);
        } else {
            // Case 2: Same meal as passengers (from itinerary meals)
            $this->calculateDriverMealCostsCase2($breakdown);
        }
    }

    /**
     * Case 1: Calculate driver meal costs using base budget.
     */
    private function calculateDriverMealCostsCase1($breakdown): void
    {
        // Calculate meal quantities based on vehicle days from breakdown
        $mealQuantities = $this->calculateMealQuantities($breakdown);
        
        if (empty($mealQuantities)) {
            return;
        }

        // Create driver meal records
        $this->createDriverMealRecords($mealQuantities, $breakdown->driver_base_meal_budget);
    }

    /**
     * Case 2: Calculate driver meal costs using same meals as passengers.
     * Logic: For each day with vehicle usage, check meal types and count them properly.
     */
    private function calculateDriverMealCostsCase2($breakdown): void
    {
        $offerGroup = $this->quotationOfferGroup;
        
        // Get itinerary to access meal data
        $itinerary = $offerGroup->quotationItinerary->itinerary;
        if (!$itinerary) {
            \Illuminate\Support\Facades\Log::info('No itinerary found for offer group');
            return;
        }

        // Get itinerary days with vehicle usage
        $itineraryDays = $itinerary->days()
            ->whereIn('vehicle_usage_mode', [
                \App\Enums\VehicleUsageModeEnum::FULL_DAY,
                \App\Enums\VehicleUsageModeEnum::HALF_DAY
            ])
            ->with(['activities.meal.mealType'])
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('No itinerary days with vehicle usage found');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Found ' . $itineraryDays->count() . ' days with vehicle usage');

        // Get breakdown meals for pricing
        $breakdownMeals = $breakdown->meals()->get()->keyBy('meal_type_id');
        \Illuminate\Support\Facades\Log::info('Found ' . $breakdownMeals->count() . ' breakdown meals');

        // Step 1: Collect meal types day by day
        $dailyMealTypes = [];
        
        foreach ($itineraryDays as $day) {
            $dayNumber = $day->day_number;
            $vehicleMode = $day->vehicle_usage_mode;
            
            // Determine meal parts based on vehicle usage
            $mealParts = [];
            if ($vehicleMode === \App\Enums\VehicleUsageModeEnum::FULL_DAY) {
                $mealParts = [\App\Enums\MealPartEnum::LUNCH, \App\Enums\MealPartEnum::DINNER];
            } elseif ($vehicleMode === \App\Enums\VehicleUsageModeEnum::HALF_DAY) {
                $mealParts = [\App\Enums\MealPartEnum::LUNCH];
            }
            
            \Illuminate\Support\Facades\Log::info("Day {$dayNumber}: Vehicle mode = " . $vehicleMode->value . ", Meal parts = " . implode(', ', array_map(fn($part) => $part->value, $mealParts)));
            
            // Get activities for this day with meals
            $activities = $day->activities()
                ->whereHas('meal')
                ->with(['meal.mealType'])
                ->get();
            
            $dayMealTypes = [];
            foreach ($activities as $activity) {
                if ($activity->meal && $activity->meal->mealType) {
                    $mealTypeId = $activity->meal->mealType->id;
                    $mealPart = $activity->meal->meal_part;
                    
                    // Only include if meal part matches vehicle usage
                    if (in_array($mealPart, $mealParts)) {
                        if (!isset($dayMealTypes[$mealTypeId])) {
                            $dayMealTypes[$mealTypeId] = 0;
                        }
                        $dayMealTypes[$mealTypeId]++;
                    }
                }
            }
            
            $dailyMealTypes[$dayNumber] = $dayMealTypes;
            \Illuminate\Support\Facades\Log::info("Day {$dayNumber} meal types: " . json_encode($dayMealTypes));
        }
        
        // Step 2: Aggregate all meal types and their total quantities
        $totalMealTypeQuantities = [];
        
        foreach ($dailyMealTypes as $dayNumber => $dayMealTypes) {
            foreach ($dayMealTypes as $mealTypeId => $count) {
                if (!isset($totalMealTypeQuantities[$mealTypeId])) {
                    $totalMealTypeQuantities[$mealTypeId] = 0;
                }
                $totalMealTypeQuantities[$mealTypeId] += $count;
            }
        }
        
        \Illuminate\Support\Facades\Log::info('Total meal type quantities: ' . json_encode($totalMealTypeQuantities));
        
        // Step 3: Create driver meal records for each meal type
        foreach ($totalMealTypeQuantities as $mealTypeId => $mealsCount) {
            $breakdownMeal = $breakdownMeals->get($mealTypeId);
            if (!$breakdownMeal) {
                \Illuminate\Support\Facades\Log::warning("No breakdown meal found for meal type: {$mealTypeId}");
                continue;
            }

            // Calculate quantity: meals count × number of drivers
            $qty = $mealsCount * $this->drivers_qty;
            
            \Illuminate\Support\Facades\Log::info("Creating driver meal record: Meal Type {$mealTypeId}, Count {$mealsCount}, Drivers {$this->drivers_qty}, Total Qty {$qty}, Price {$breakdownMeal->price}");

            // Create driver meal record
            $this->quotationOfferDriverMeals()->create([
                'meal_type_id' => $mealTypeId,
                'qty' => $qty,
                'price' => $breakdownMeal->price,
                'is_base_budget' => false,
                'tenant_id' => $this->tenant_id,
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info('Case 2 calculation completed. Created ' . count($totalMealTypeQuantities) . ' driver meal records');
    }

    /**
     * Manually trigger driver meal cost calculation (for testing purposes).
     */
    public function triggerDriverMealCalculation(): void
    {
        \Illuminate\Support\Facades\Log::info('Manually triggering driver meal calculation');
        $this->calculateDriverMealCosts();
    }

    /**
     * Calculate meal quantities based on vehicle days from breakdown.
     * Half day = 1 meal, Full day = 2 meals
     */
    private function calculateMealQuantities($breakdown): array
    {
        $quantities = [];

        // Use breakdown values if available, otherwise use offer values
        $vehicleHalfDays = $breakdown->vehicle_half_days_qty ?? $this->vehicle_half_days_qty ?? 0;
        $vehicleDays = $breakdown->vehicle_days_qty ?? $this->vehicle_days_qty ?? 0;

        // Half days = 1 meal each
        if ($vehicleHalfDays > 0) {
            $quantities['half_day_meals'] = $vehicleHalfDays;
        }

        // Full days = 2 meals each
        if ($vehicleDays > 0) {
            $quantities['full_day_meals'] = $vehicleDays * 2;
        }

        return $quantities;
    }

    /**
     * Create driver meal records in the database.
     */
    private function createDriverMealRecords(array $mealQuantities, float $baseMealBudget): void
    {
        // Create records for half day meals
        if (isset($mealQuantities['half_day_meals'])) {
            $halfDayQty = $mealQuantities['half_day_meals'] * $this->drivers_qty;
            $this->quotationOfferDriverMeals()->create([
                'meal_type_id' => null, // null for base budget meals
                'qty' => $halfDayQty,
                'price' => $baseMealBudget,
                'is_base_budget' => true,
                'tenant_id' => $this->tenant_id,
            ]);
        }

        // Create records for full day meals
        if (isset($mealQuantities['full_day_meals'])) {
            $fullDayQty = $mealQuantities['full_day_meals'] * $this->drivers_qty;
            $this->quotationOfferDriverMeals()->create([
                'meal_type_id' => null, // null for base budget meals
                'qty' => $fullDayQty,
                'price' => $baseMealBudget,
                'is_base_budget' => true,
                'tenant_id' => $this->tenant_id,
            ]);
        }
    }
}

