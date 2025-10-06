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
     * Calculate and create driver accommodation costs based on offer group settings.
     */
    public function calculateDriverAccommodationCosts(): void
    {
        $offerGroup = $this->quotationOfferGroup;
        
        // Check if driver accommodation should be included
        if (!$offerGroup || !$offerGroup->is_include_driver_hotel) {
            \Illuminate\Support\Facades\Log::info('Driver accommodation not included or offer group not found');
            return;
        }

        // Check if drivers quantity is at least 1
        if ($this->drivers_qty < 1) {
            \Illuminate\Support\Facades\Log::info('Drivers quantity is less than 1', ['drivers_qty' => $this->drivers_qty]);
            return;
        }

        // Get breakdown for base accommodation budget
        $breakdown = $offerGroup->quotationItinerary->breakdown;
        if (!$breakdown) {
            \Illuminate\Support\Facades\Log::info('No breakdown found');
            return;
        }

        // Check if driver stay same hotel is false (case 1: base budget calculation)
        if (!$offerGroup->is_driver_stay_same_hotel) {
            // Case 1: Base budget calculation
            if (!$breakdown->driver_base_accommodation_budget) {
                \Illuminate\Support\Facades\Log::info('No driver base accommodation budget found');
                return;
            }
            $this->calculateDriverAccommodationCostsCase1($breakdown);
        } else {
            // Case 2: Same accommodation as passengers (from itinerary accommodations)
            $this->calculateDriverAccommodationCostsCase2($breakdown);
        }
    }

    /**
     * Case 1: Calculate driver accommodation costs using base budget.
     */
    private function calculateDriverAccommodationCostsCase1($breakdown): void
    {
        $offerGroup = $this->quotationOfferGroup;
        
        // Get itinerary to access vehicle usage data
        $itinerary = $offerGroup->quotationItinerary->itinerary;
        if (!$itinerary) {
            \Illuminate\Support\Facades\Log::info('No itinerary found for accommodation calculation');
            return;
        }

        // Get itinerary days with vehicle usage
        $itineraryDays = $itinerary->days()
            ->whereIn('vehicle_usage_mode', [
                \App\Enums\VehicleUsageModeEnum::FULL_DAY,
                \App\Enums\VehicleUsageModeEnum::HALF_DAY
            ])
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('No itinerary days with vehicle usage found for accommodation');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Found ' . $itineraryDays->count() . ' days with vehicle usage for accommodation calculation');

        // Calculate nights needed for driver accommodation
        $nightsNeeded = $this->calculateDriverNightsNeeded($itineraryDays);
        
        if ($nightsNeeded <= 0) {
            \Illuminate\Support\Facades\Log::info('No nights needed for driver accommodation');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Driver needs ' . $nightsNeeded . ' nights accommodation');

        // Create driver accommodation record
        $this->quotationOfferDriverAccommodations()->create([
            'accommodation_id' => null, // null for base budget accommodation
            'room_category_id' => null, // null for base budget accommodation
            'city_id' => null, // null for base budget accommodation
            'nights' => $nightsNeeded * $this->drivers_qty,
            'night_price' => $breakdown->driver_base_accommodation_budget,
            'is_base_budget' => true,
            'tenant_id' => $this->tenant_id,
        ]);

        \Illuminate\Support\Facades\Log::info('Driver accommodation record created successfully', [
            'nights' => $nightsNeeded * $this->drivers_qty,
            'night_price' => $breakdown->driver_base_accommodation_budget,
            'drivers_qty' => $this->drivers_qty
        ]);
    }

    /**
     * Case 2: Calculate driver accommodation costs using same accommodation as passengers.
     * Logic: For each hotel where driver stays, create a separate record based on itinerary accommodations.
     */
    private function calculateDriverAccommodationCostsCase2($breakdown): void
    {
        $offerGroup = $this->quotationOfferGroup;
        
        // Check if driver_room_category_id is set
        if (!$offerGroup->driver_room_category_id) {
            \Illuminate\Support\Facades\Log::info('No driver room category ID found in offer group');
            return;
        }

        // Get itinerary to access accommodation data
        $itinerary = $offerGroup->quotationItinerary->itinerary;
        if (!$itinerary) {
            \Illuminate\Support\Facades\Log::info('No itinerary found for accommodation calculation');
            return;
        }

        // Get itinerary days with vehicle usage
        $itineraryDays = $itinerary->days()
            ->whereIn('vehicle_usage_mode', [
                \App\Enums\VehicleUsageModeEnum::FULL_DAY,
                \App\Enums\VehicleUsageModeEnum::HALF_DAY
            ])
            ->with(['accommodation', 'accommodationCity'])
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('No itinerary days with vehicle usage found for accommodation');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Found ' . $itineraryDays->count() . ' days with vehicle usage for accommodation calculation');

        // Get driver accommodation nights by hotel
        $driverAccommodationsByHotel = $this->calculateDriverAccommodationsByHotel($itineraryDays);
        
        if (empty($driverAccommodationsByHotel)) {
            \Illuminate\Support\Facades\Log::info('No driver accommodations needed');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Driver accommodations by hotel: ' . json_encode($driverAccommodationsByHotel));

        // Create driver accommodation records for each hotel
        foreach ($driverAccommodationsByHotel as $hotelData) {
            $accommodationId = $hotelData['accommodation_id'];
            $cityId = $hotelData['city_id'];
            $nights = $hotelData['nights'];

            // Find the price for the driver room category from breakdown accommodations
            $driverRoomPrice = $this->findDriverRoomPriceForAccommodation($breakdown, $offerGroup->driver_room_category_id, $accommodationId);
            
            if (!$driverRoomPrice) {
                \Illuminate\Support\Facades\Log::warning('No price found for driver room category in accommodation: ' . $accommodationId);
                continue;
            }

            \Illuminate\Support\Facades\Log::info('Found driver room price for accommodation ' . $accommodationId . ': ' . $driverRoomPrice);

            // Create driver accommodation record
            $this->quotationOfferDriverAccommodations()->create([
                'accommodation_id' => $accommodationId,
                'room_category_id' => $offerGroup->driver_room_category_id,
                'city_id' => $cityId,
                'nights' => $nights * $this->drivers_qty,
                'night_price' => $driverRoomPrice,
                'is_base_budget' => false,
                'tenant_id' => $this->tenant_id,
            ]);

            \Illuminate\Support\Facades\Log::info('Driver accommodation record created successfully', [
                'accommodation_id' => $accommodationId,
                'city_id' => $cityId,
                'nights' => $nights * $this->drivers_qty,
                'night_price' => $driverRoomPrice,
                'room_category_id' => $offerGroup->driver_room_category_id,
                'drivers_qty' => $this->drivers_qty
            ]);
        }
    }

    /**
     * Calculate driver accommodations by hotel based on itinerary days.
     * Logic: For each night where driver needs accommodation, determine which hotel and city.
     */
    private function calculateDriverAccommodationsByHotel($itineraryDays): array
    {
        $accommodationsByHotel = [];
        $days = $itineraryDays->sortBy('day_number');
        
        for ($i = 0; $i < $days->count() - 1; $i++) {
            $currentDay = $days->values()[$i];
            $nextDay = $days->values()[$i + 1];
            
            // Check if current day and next day are consecutive
            if ($nextDay->day_number === $currentDay->day_number + 1) {
                // Driver needs accommodation for the night between these days
                // Use the accommodation from the current day (where driver will stay)
                $accommodation = $currentDay->accommodation;
                
                if ($accommodation) {
                    $accommodationId = $accommodation->id;
                    $cityId = $currentDay->accommodation_city_id;
                    
                    // Group by accommodation and city
                    $key = $accommodationId . '_' . $cityId;
                    
                    if (!isset($accommodationsByHotel[$key])) {
                        $accommodationsByHotel[$key] = [
                            'accommodation_id' => $accommodationId,
                            'city_id' => $cityId,
                            'nights' => 0
                        ];
                    }
                    
                    $accommodationsByHotel[$key]['nights']++;
                    
                    \Illuminate\Support\Facades\Log::info('Driver needs accommodation for night between day ' . $currentDay->day_number . ' and day ' . $nextDay->day_number . ' at accommodation: ' . $accommodationId . ', city: ' . $cityId);
                }
            }
        }
        
        return array_values($accommodationsByHotel);
    }

    /**
     * Find the price for driver room category from specific breakdown accommodation.
     * Price is divided by room capacity to get per-person price.
     */
    private function findDriverRoomPriceForAccommodation($breakdown, $driverRoomCategoryId, $accommodationId): ?float
    {
        // Get the specific breakdown accommodation with its rooms
        $breakdownAccommodation = $breakdown->accommodations()
            ->where('accommodation_id', $accommodationId)
            ->with('rooms')
            ->first();

        if (!$breakdownAccommodation) {
            \Illuminate\Support\Facades\Log::warning('No breakdown accommodation found for accommodation: ' . $accommodationId);
            return null;
        }

        foreach ($breakdownAccommodation->rooms as $room) {
            if ($room->room_category_id === $driverRoomCategoryId) {
                // Get room category to access capacity
                $roomCategory = RoomCategory::find($driverRoomCategoryId);
                $capacity = $roomCategory ? $roomCategory->capacity : 1;
                
                // Calculate per-person price by dividing room price by capacity
                $perPersonPrice = (float) $room->price / $capacity;
                
                \Illuminate\Support\Facades\Log::info('Found driver room price in accommodation: ' . $accommodationId . ', room: ' . $room->id . ', room price: ' . $room->price . ', capacity: ' . $capacity . ', per-person price: ' . $perPersonPrice);
                return $perPersonPrice;
            }
        }

        \Illuminate\Support\Facades\Log::warning('No room found for driver room category: ' . $driverRoomCategoryId . ' in accommodation: ' . $accommodationId);
        return null;
    }

    /**
     * Find the price for driver room category from breakdown accommodations (legacy method for Case 1).
     */
    private function findDriverRoomPrice($breakdown, $driverRoomCategoryId): ?float
    {
        // Get all breakdown accommodations with their rooms
        $breakdownAccommodations = $breakdown->accommodations()
            ->with('rooms')
            ->get();

        foreach ($breakdownAccommodations as $accommodation) {
            foreach ($accommodation->rooms as $room) {
                if ($room->room_category_id === $driverRoomCategoryId) {
                    \Illuminate\Support\Facades\Log::info('Found driver room price in accommodation: ' . $accommodation->id . ', room: ' . $room->id . ', price: ' . $room->price);
                    return (float) $room->price;
                }
            }
        }

        return null;
    }

    /**
     * Calculate the number of nights needed for driver accommodation.
     * Logic: If vehicle is used on day N and also on day N+1, driver needs accommodation for night N.
     */
    private function calculateDriverNightsNeeded($itineraryDays): int
    {
        $nightsNeeded = 0;
        $days = $itineraryDays->sortBy('day_number');
        
        for ($i = 0; $i < $days->count() - 1; $i++) {
            $currentDay = $days->values()[$i];
            $nextDay = $days->values()[$i + 1];
            
            // Check if current day and next day are consecutive
            if ($nextDay->day_number === $currentDay->day_number + 1) {
                // Driver needs accommodation for the night between these days
                $nightsNeeded++;
                \Illuminate\Support\Facades\Log::info('Driver needs accommodation for night between day ' . $currentDay->day_number . ' and day ' . $nextDay->day_number);
            }
        }
        
        return $nightsNeeded;
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
     * Manually trigger driver accommodation cost calculation (for testing purposes).
     */
    public function triggerDriverAccommodationCalculation(): void
    {
        \Illuminate\Support\Facades\Log::info('Manually triggering driver accommodation calculation');
        $this->calculateDriverAccommodationCosts();
    }

    /**
     * Calculate and create leader accommodation costs.
     */
    public function calculateLeaderAccommodationCosts(): void
    {
        // Check if leaders quantity is at least 1
        if ($this->leaders_qty < 1) {
            \Illuminate\Support\Facades\Log::info('Leaders quantity is less than 1', ['leaders_qty' => $this->leaders_qty]);
            return;
        }

        // Check if leader_room_category_id is set
        if (!$this->leader_room_category_id) {
            \Illuminate\Support\Facades\Log::info('No leader room category ID found in offer');
            return;
        }

        // Get breakdown for pricing
        $offerGroup = $this->quotationOfferGroup;
        $breakdown = $offerGroup->quotationItinerary->breakdown;
        if (!$breakdown) {
            \Illuminate\Support\Facades\Log::info('No breakdown found for leader accommodation calculation');
            return;
        }

        // Get itinerary to access accommodation data
        $itinerary = $offerGroup->quotationItinerary->itinerary;
        if (!$itinerary) {
            \Illuminate\Support\Facades\Log::info('No itinerary found for leader accommodation calculation');
            return;
        }

        // Get itinerary days with accommodations
        $itineraryDays = $itinerary->days()
            ->whereNotNull('accommodation_id')
            ->with(['accommodation', 'accommodationCity'])
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('No itinerary days with accommodations found');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Found ' . $itineraryDays->count() . ' days with accommodations for leader calculation');

        // Get leader accommodations by hotel
        $leaderAccommodationsByHotel = $this->calculateLeaderAccommodationsByHotel($itineraryDays);
        
        if (empty($leaderAccommodationsByHotel)) {
            \Illuminate\Support\Facades\Log::info('No leader accommodations needed');
            return;
        }

        \Illuminate\Support\Facades\Log::info('Leader accommodations by hotel: ' . json_encode($leaderAccommodationsByHotel));

        // Create leader accommodation records for each hotel
        foreach ($leaderAccommodationsByHotel as $hotelData) {
            $accommodationId = $hotelData['accommodation_id'];
            $cityId = $hotelData['city_id'];
            $nights = $hotelData['nights'];

            // Find the price for the leader room category from breakdown accommodations
            $leaderRoomPrice = $this->findLeaderRoomPriceForAccommodation($breakdown, $this->leader_room_category_id, $accommodationId);
            
            if (!$leaderRoomPrice) {
                \Illuminate\Support\Facades\Log::warning('No price found for leader room category in accommodation: ' . $accommodationId);
                continue;
            }

            \Illuminate\Support\Facades\Log::info('Found leader room price for accommodation ' . $accommodationId . ': ' . $leaderRoomPrice);

            // Create leader accommodation record
            $this->quotationOfferLeaderAccommodations()->create([
                'accommodation_id' => $accommodationId,
                'room_category_id' => $this->leader_room_category_id,
                'city_id' => $cityId,
                'nights' => $nights * $this->leaders_qty,
                'night_price' => $leaderRoomPrice,
                'tenant_id' => $this->tenant_id,
            ]);

            \Illuminate\Support\Facades\Log::info('Leader accommodation record created successfully', [
                'accommodation_id' => $accommodationId,
                'city_id' => $cityId,
                'nights' => $nights * $this->leaders_qty,
                'night_price' => $leaderRoomPrice,
                'room_category_id' => $this->leader_room_category_id,
                'leaders_qty' => $this->leaders_qty
            ]);
        }
    }

    /**
     * Manually trigger leader accommodation cost calculation (for testing purposes).
     */
    public function triggerLeaderAccommodationCalculation(): void
    {
        \Illuminate\Support\Facades\Log::info('Manually triggering leader accommodation calculation');
        $this->calculateLeaderAccommodationCosts();
    }

    /**
     * Calculate leader accommodations by hotel based on itinerary days.
     * Logic: For each day with accommodation, determine which hotel and city, and count nights.
     */
    private function calculateLeaderAccommodationsByHotel($itineraryDays): array
    {
        $accommodationsByHotel = [];
        $days = $itineraryDays->sortBy('day_number');
        
        foreach ($days as $day) {
            $accommodation = $day->accommodation;
            
            if ($accommodation) {
                $accommodationId = $accommodation->id;
                $cityId = $day->accommodation_city_id;
                
                // Group by accommodation and city
                $key = $accommodationId . '_' . $cityId;
                
                if (!isset($accommodationsByHotel[$key])) {
                    $accommodationsByHotel[$key] = [
                        'accommodation_id' => $accommodationId,
                        'city_id' => $cityId,
                        'nights' => 0
                    ];
                }
                
                $accommodationsByHotel[$key]['nights']++;
                
                \Illuminate\Support\Facades\Log::info('Leader needs accommodation for day ' . $day->day_number . ' at accommodation: ' . $accommodationId . ', city: ' . $cityId);
            }
        }
        
        return array_values($accommodationsByHotel);
    }

    /**
     * Find the price for leader room category from specific breakdown accommodation.
     * Price is divided by room capacity to get per-person price.
     */
    private function findLeaderRoomPriceForAccommodation($breakdown, $leaderRoomCategoryId, $accommodationId): ?float
    {
        // Get the specific breakdown accommodation with its rooms
        $breakdownAccommodation = $breakdown->accommodations()
            ->where('accommodation_id', $accommodationId)
            ->with('rooms')
            ->first();

        if (!$breakdownAccommodation) {
            \Illuminate\Support\Facades\Log::warning('No breakdown accommodation found for accommodation: ' . $accommodationId);
            return null;
        }

        foreach ($breakdownAccommodation->rooms as $room) {
            if ($room->room_category_id === $leaderRoomCategoryId) {
                // Get room category to access capacity
                $roomCategory = RoomCategory::find($leaderRoomCategoryId);
                $capacity = $roomCategory ? $roomCategory->capacity : 1;
                
                // Calculate per-person price by dividing room price by capacity
                $perPersonPrice = (float) $room->price / $capacity;
                
                \Illuminate\Support\Facades\Log::info('Found leader room price in accommodation: ' . $accommodationId . ', room: ' . $room->id . ', room price: ' . $room->price . ', capacity: ' . $capacity . ', per-person price: ' . $perPersonPrice);
                return $perPersonPrice;
            }
        }

        \Illuminate\Support\Facades\Log::warning('No room found for leader room category: ' . $leaderRoomCategoryId . ' in accommodation: ' . $accommodationId);
        return null;
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

