<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroup extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'is_include_driver_meal',
        'is_include_driver_hotel',
        'is_driver_stay_same_hotel',
        'is_driver_same_meal',
        'driver_room_category_id',
        'driver_meal_cost',
        'driver_accommodation_cost',
        'tenant_id',
    ];

    protected $casts = [
        'is_include_driver_meal' => 'boolean',
        'is_include_driver_hotel' => 'boolean',
        'is_driver_stay_same_hotel' => 'boolean',
        'is_driver_same_meal' => 'boolean',
        'driver_meal_cost' => 'decimal:2',
        'driver_accommodation_cost' => 'decimal:2',
    ];

    /**
     * Get the quotation itinerary for this offer group.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class);
    }


    /**
     * Get the driver room category for this offer group.
     */
    public function driverRoomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'driver_room_category_id');
    }

    /**
     * Get the quotation offer group companions for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupCompanions(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanion::class);
    }

    /**
     * Get the quotation offers for this offer group (one-to-many relationship).
     */
    public function quotationOffers(): HasMany
    {
        return $this->hasMany(QuotationOffer::class);
    }

    /**
     * Get the quotation offer group meals for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupMeals(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupMeal::class);
    }

    /**
     * Get the quotation offer group attractions for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupAttractions(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupAttraction::class);
    }

    /**
     * Get the quotation offer group tickets for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupTickets(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupTicket::class);
    }

    /**
     * Get the quotation offer group expenses for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupExpenses(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupExpense::class);
    }

    /**
     * Calculate and update companion costs from breakdown.
     */
    public function calculateCompanionCostsFromBreakdown(): void
    {
        $breakdown = $this->quotationItinerary->breakdown;
        
        if (!$breakdown) {
            return;
        }

        // Get itinerary to calculate quantities
        $itinerary = $this->quotationItinerary->itinerary;
        if (!$itinerary) {
            return;
        }

        // Calculate companion quantities from itinerary days
        $fullDays = $itinerary->days()
            ->where('companion_hire_mode', 'daily')
            ->count();
        
        $halfDays = $itinerary->days()
            ->where('companion_hire_mode', 'half_day')
            ->count();
        
        $hours = $itinerary->days()
            ->where('companion_hire_mode', 'hourly')
            ->sum('companion_hours') ?? 0;

        // Update each companion in this offer group
        foreach ($this->quotationOfferGroupCompanions as $companion) {
            // Find matching breakdown companion
            $breakdownCompanion = $breakdown->companions()
                ->where('companion_type_id', $companion->companion_type_id)
                ->first();

            if ($breakdownCompanion) {
                $companion->update([
                    'full_days_qty' => (int) $fullDays,
                    'half_days_qty' => (int) $halfDays,
                    'hours_qty' => (int) $hours,
                    'day_price' => $breakdownCompanion->per_day_price,
                    'half_day_price' => $breakdownCompanion->half_day_price,
                ]);
            }

            // Calculate meal cost and create meal records
            $this->calculateAndCreateMealRecords($companion, $itinerary, $breakdown);
            
            // Calculate ticket cost and create ticket records
            $this->calculateAndCreateTicketRecords($companion, $breakdown);
            
            // Calculate experience cost and create experience records
            $this->calculateAndCreateExperienceRecords($companion, $breakdown);
            
            // Calculate attraction cost and create attraction records
            $this->calculateAndCreateAttractionRecords($companion, $breakdown);
            
            // Calculate expense cost and create expense records
            $this->calculateAndCreateExpenseRecords($companion, $breakdown);
            
            // Calculate accommodation cost and create accommodation records
            $this->calculateAndCreateAccommodationRecords($companion, $breakdown, $itinerary);
            
            // No need to update ticket_cost as it's now calculated dynamically
        }
    }

    /**
     * Calculate ticket cost and create ticket records
     */
    private function calculateAndCreateTicketRecords($companion, $breakdown): void
    {
        // Clear existing ticket records for this companion
        $companion->tickets()->delete();
        
        // Get all breakdown tickets
        foreach ($breakdown->tickets as $breakdownTicket) {
            if (($breakdownTicket->price ?? 0) > 0) {
                $companion->tickets()->create([
                    'from_city_id' => $breakdownTicket->from_city_id,
                    'to_city_id' => $breakdownTicket->to_city_id,
                    'class' => $breakdownTicket->class ?? 'economy',
                    'price' => $breakdownTicket->price ?? 0,
                ]);
            }
        }
    }

    /**
     * Calculate meal cost from itinerary for companions with same meal
     */
    private function calculateAndCreateMealRecords($companion, $itinerary, $breakdown): void
    {
        // Clear existing meal records for this companion
        $companion->meals()->delete();

        $fullDays = $itinerary->days()
            ->where('companion_hire_mode', 'daily')
            ->count();

        $halfDays = $itinerary->days()
            ->where('companion_hire_mode', 'half_day')
            ->count();

        if (!$companion->is_same_meal) {
            // Use base meal budget for different meals
            $totalMeals = $fullDays * 2 + $halfDays * 1; // 2 meals for full day, 1 meal for half day
            $baseBudget = $breakdown->companion_base_meal_budget ?? 0;
            
            if ($totalMeals > 0 && $baseBudget > 0) {
                $companion->meals()->create([
                    'meal_type_id' => null, // null for base budget
                    'qty' => $totalMeals,
                    'price' => $baseBudget,
                    'is_base_budget' => true,
                ]);
            }
        } else {
            // Calculate based on actual meal types from itinerary
            $this->createSpecificMealRecords($companion, $itinerary, $breakdown);
        }
    }

    /**
     * Create specific meal records based on itinerary
     */
    private function createSpecificMealRecords($companion, $itinerary, $breakdown): void
    {
        $mealCounts = [];

        foreach ($itinerary->days as $day) {
            // Check if companion is present on this day (only check hire mode, not type)
            if (!$day->companion_hire_mode) {
                continue;
            }
            
            $companionHireMode = $day->companion_hire_mode->value;
            
            // Get meal activities for this day
            $mealActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                })
                ->with('meal.mealType')
                ->get();
            
            foreach ($mealActivities as $activity) {
                if ($activity->meal?->mealType) {
                    $mealTypeId = $activity->meal->meal_type_id;
                    $mealPart = $activity->meal->meal_part?->value ?? $activity->meal->meal_part;
                    
                    // Determine which meals to include based on companion hire mode
                    $shouldIncludeMeal = false;
                    
                    if ($companionHireMode === 'daily') {
                        // Full day: include lunch and dinner
                        $shouldIncludeMeal = in_array($mealPart, ['lunch', 'dinner']);
                    } elseif ($companionHireMode === 'half_day') {
                        // Half day: include only lunch
                        $shouldIncludeMeal = $mealPart === 'lunch';
                    }
                    
                    if ($shouldIncludeMeal) {
                        if (!isset($mealCounts[$mealTypeId])) {
                            $mealCounts[$mealTypeId] = 0;
                        }
                        $mealCounts[$mealTypeId]++;
                    }
                }
            }
        }

        // Create meal records for each meal type
        foreach ($mealCounts as $mealTypeId => $qty) {
            $breakdownMeal = $breakdown->meals()
                ->where('meal_type_id', $mealTypeId)
                ->first();

            if ($breakdownMeal && $qty > 0) {
                $companion->meals()->create([
                    'meal_type_id' => $mealTypeId,
                    'qty' => $qty,
                    'price' => $breakdownMeal->price ?? 0,
                    'is_base_budget' => false,
                ]);
            }
        }
    }

    /**
     * Calculate experience cost and create experience records
     */
    private function calculateAndCreateExperienceRecords($companion, $breakdown): void
    {
        // Clear existing experience records for this companion
        $companion->experiences()->delete();
        
        // Get companion type and its category
        $companionType = $companion->companionType;
        if (!$companionType || !$companionType->companionCategory) {
            return;
        }
        
        $categoryType = $companionType->companionCategory->category_type;
        
        // Get all breakdown experiences
        foreach ($breakdown->experiences as $breakdownExperience) {
            $shouldIncludeExperience = false;
            
            // Check if experience should be included based on companion category
            if ($categoryType === \App\Enums\CompanionCategoryEnum::TOUR_GUIDE) {
                // For tour guides, check if breakdown experience is free for guide
                $shouldIncludeExperience = !$breakdownExperience->is_free_for_guide;
            } else {
                // For other companions, check if breakdown experience is free for other companions
                $shouldIncludeExperience = !$breakdownExperience->is_free_for_other_companions;
            }
            
            if ($shouldIncludeExperience && ($breakdownExperience->price ?? 0) > 0) {
                $companion->experiences()->create([
                    'experience_id' => $breakdownExperience->experience_id,
                    'price' => $breakdownExperience->price ?? 0,
                ]);
            }
        }
    }

    /**
     * Calculate attraction cost and create attraction records
     */
    private function calculateAndCreateAttractionRecords($companion, $breakdown): void
    {
        // Clear existing attraction records for this companion
        $companion->attractions()->delete();
        
        // Get companion type and its category
        $companionType = $companion->companionType;
        if (!$companionType || !$companionType->companionCategory) {
            return;
        }
        
        $categoryType = $companionType->companionCategory->category_type;
        
        // If companion is tour guide, attractions are free
        if ($categoryType === \App\Enums\CompanionCategoryEnum::TOUR_GUIDE) {
            return;
        }
        
        // For other companions, create attraction records
        foreach ($breakdown->attractions as $breakdownAttraction) {
            if (($breakdownAttraction->entry_price ?? 0) > 0) {
                $attractionRecord = $companion->attractions()->create([
                    'attraction_id' => $breakdownAttraction->attraction_id,
                    'price' => $breakdownAttraction->entry_price ?? 0,
                ]);
                
                // Create sub-attraction records
                foreach ($breakdownAttraction->subAttractions as $subAttraction) {
                    if (($subAttraction->price ?? 0) > 0) {
                        $attractionRecord->subAttractions()->create([
                            'sub_attraction_id' => $subAttraction->sub_attraction_id,
                            'price' => $subAttraction->price ?? 0,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Calculate expense cost and create expense records
     */
    private function calculateAndCreateExpenseRecords($companion, $breakdown): void
    {
        // Clear existing expense records for this companion
        $companion->expenses()->delete();
        
        // Get all breakdown expenses that are per_person
        foreach ($breakdown->expenses as $breakdownExpense) {
            if ($breakdownExpense->charge_mode === \App\Enums\ChargeModeEnum::PER_PERSON && ($breakdownExpense->price ?? 0) > 0) {
                $companion->expenses()->create([
                    'description' => $breakdownExpense->description ?? 'Expense',
                    'price' => $breakdownExpense->price ?? 0,
                ]);
            }
        }
    }

    /**
     * Calculate accommodation cost and create accommodation records
     */
    private function calculateAndCreateAccommodationRecords($companion, $breakdown, $itinerary): void
    {
        // Clear existing accommodation records for this companion
        $companion->accommodations()->delete();
        
        // Calculate number of nights companion needs accommodation
        $accommodationNights = $this->calculateAccommodationNights($companion, $itinerary);
        
        if ($accommodationNights <= 0) {
            return;
        }
        
        // If companion stays in same hotel as group
        if ($companion->is_stay_same_hotel) {
            $this->createSameHotelAccommodationRecords($companion, $breakdown, $itinerary);
        } else {
            // Use base accommodation budget
            $baseBudget = $breakdown->companion_base_accommodation_budget ?? 0;
            if ($baseBudget > 0) {
                // Get the first city from itinerary for base budget accommodation
                $firstCityId = $itinerary->days->whereNotNull('accommodation_city_id')->first()?->accommodation_city_id;
                
                $companion->accommodations()->create([
                    'accommodation_id' => null,
                    'room_category_id' => null,
                    'city_id' => $firstCityId, // Use first city from itinerary
                    'nights' => $accommodationNights,
                    'night_price' => $baseBudget,
                    'is_base_budget' => true,
                ]);
            }
        }
    }

    /**
     * Calculate number of nights companion needs accommodation
     */
    private function calculateAccommodationNights($companion, $itinerary): int
    {
        $nights = 0;
        $livingCityId = $companion->living_city_id;
        $days = $itinerary->days->sortBy('day_number');
        
        foreach ($days as $index => $day) {
            // Skip if companion is not present on this day
            if (!$day->companion_hire_mode) {
                continue;
            }
            
            // Skip if companion lives in the same city as accommodation
            if ($livingCityId && $day->accommodation_city_id == $livingCityId) {
                continue;
            }
            
            // Check if companion needs accommodation for this night
            // Companion needs accommodation if:
            // 1. They are present on this day AND
            // 2. They are also present on the next day (so they need to stay overnight)
            $nextDay = $days->get($index + 1);
            if ($nextDay && $nextDay->companion_hire_mode) {
                // Companion is present on both current day and next day
                // So they need accommodation for this night
                $nights++;
            }
        }
        
        return $nights;
    }

    /**
     * Calculate accommodation cost when staying in same hotel
     */
    private function createSameHotelAccommodationRecords($companion, $breakdown, $itinerary): void
    {
        if (!$companion->room_category_id) {
            return;
        }
        
        $livingCityId = $companion->living_city_id;
        $days = $itinerary->days->sortBy('day_number');
        $accommodationRecords = [];
        
        // Group nights by accommodation and city
        foreach ($days as $index => $day) {
            // Skip if companion is not present on this day
            if (!$day->companion_hire_mode) {
                continue;
            }
            
            // Skip if companion lives in the same city as accommodation
            if ($livingCityId && $day->accommodation_city_id == $livingCityId) {
                continue;
            }
            
            // Check if companion needs accommodation for this night
            // Companion needs accommodation if they are also present on the next day
            $nextDay = $days->get($index + 1);
            if (!$nextDay || !$nextDay->companion_hire_mode) {
                // Companion is not present on next day, so they don't need accommodation for this night
                continue;
            }
            
            // Find breakdown accommodation for this specific day's hotel
            $breakdownAccommodation = $breakdown->accommodations()
                ->where('accommodation_id', $day->accommodation_id)
                ->first();
            
            if (!$breakdownAccommodation) {
                continue;
            }
            
            // Find room pricing for this accommodation and room category
            $room = $breakdownAccommodation->rooms()
                ->where('room_category_id', $companion->room_category_id)
                ->with('roomCategory')
                ->first();
            
            if ($room) {
                // Get room category capacity
                $capacity = $room->roomCategory ? $room->roomCategory->capacity : 1;
                
                // Calculate cost per person (divide by capacity)
                $costPerPerson = ($room->price ?? 0) / max($capacity, 1);
                
                // Group by accommodation and city
                $key = $day->accommodation_id . '_' . $day->accommodation_city_id;
                if (!isset($accommodationRecords[$key])) {
                    $accommodationRecords[$key] = [
                        'accommodation_id' => $day->accommodation_id,
                        'room_category_id' => $companion->room_category_id,
                        'city_id' => $day->accommodation_city_id,
                        'nights' => 0,
                        'night_price' => $costPerPerson,
                        'is_base_budget' => false,
                    ];
                }
                $accommodationRecords[$key]['nights']++;
            }
        }
        
        // Create accommodation records
        foreach ($accommodationRecords as $record) {
            $companion->accommodations()->create($record);
        }
    }

    /**
     * Scope a query to filter by quotation itinerary.
     */
    public function scopeByQuotationItinerary($query, $quotationItineraryId)
    {
        return $query->where('quotation_itinerary_id', $quotationItineraryId);
    }


    /**
     * Scope a query to filter by include driver meal.
     */
    public function scopeIncludeDriverMeal($query, $value = true)
    {
        return $query->where('is_include_driver_meal', $value);
    }

    /**
     * Scope a query to filter by include driver hotel.
     */
    public function scopeIncludeDriverHotel($query, $value = true)
    {
        return $query->where('is_include_driver_hotel', $value);
    }

    /**
     * Scope a query to filter by driver stay same hotel.
     */
    public function scopeDriverStaySameHotel($query, $value = true)
    {
        return $query->where('is_driver_stay_same_hotel', $value);
    }

    /**
     * Scope a query to filter by driver same meal.
     */
    public function scopeDriverSameMeal($query, $value = true)
    {
        return $query->where('is_driver_same_meal', $value);
    }


    /**
     * Scope a query to filter by driver room category.
     */
    public function scopeByDriverRoomCategory($query, $roomCategoryId)
    {
        return $query->where('driver_room_category_id', $roomCategoryId);
    }


    /**
     * Check if driver meal is included.
     */
    public function getDriverMealIncludedAttribute(): bool
    {
        return $this->is_include_driver_meal;
    }

    /**
     * Check if driver hotel is included.
     */
    public function getDriverHotelIncludedAttribute(): bool
    {
        return $this->is_include_driver_hotel;
    }

    /**
     * Check if driver stays in the same hotel.
     */
    public function getDriverStaysSameHotelAttribute(): bool
    {
        return $this->is_driver_stay_same_hotel;
    }

    /**
     * Check if driver has same meal.
     */
    public function getDriverSameMealAttribute(): bool
    {
        return $this->is_driver_same_meal;
    }


    /**
     * Get the driver room category name.
     */
    public function getDriverRoomCategoryNameAttribute(): ?string
    {
        return $this->driverRoomCategory?->name;
    }

    /**
     * Get formatted driver meal cost.
     */
    public function getFormattedDriverMealCostAttribute(): string
    {
        return number_format((float) $this->driver_meal_cost, 2);
    }

    /**
     * Get formatted driver accommodation cost.
     */
    public function getFormattedDriverAccommodationCostAttribute(): string
    {
        return number_format((float) $this->driver_accommodation_cost, 2);
    }

    /**
     * Get total driver cost.
     */
    public function getTotalDriverCostAttribute(): float
    {
        return (float) $this->driver_meal_cost + (float) $this->driver_accommodation_cost;
    }

    /**
     * Get formatted total driver cost.
     */
    public function getFormattedTotalDriverCostAttribute(): string
    {
        return number_format($this->total_driver_cost, 2);
    }

    /**
     * Scope a query to filter by driver meal cost range.
     */
    public function scopeByDriverMealCostRange($query, $minCost, $maxCost)
    {
        return $query->whereBetween('driver_meal_cost', [$minCost, $maxCost]);
    }

    /**
     * Scope a query to filter by driver accommodation cost range.
     */
    public function scopeByDriverAccommodationCostRange($query, $minCost, $maxCost)
    {
        return $query->whereBetween('driver_accommodation_cost', [$minCost, $maxCost]);
    }

    /**
     * Get driver cost breakdown as array.
     */
    public function getDriverCostBreakdownAttribute(): array
    {
        return [
            'meal' => (float) $this->driver_meal_cost,
            'accommodation' => (float) $this->driver_accommodation_cost,
            'total' => $this->total_driver_cost,
        ];
    }
}
