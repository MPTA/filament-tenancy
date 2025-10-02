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

            // Calculate meal cost
            if (!$companion->is_same_meal) {
                // Use base meal budget for different meals
                $totalMeals = $fullDays * 2 + $halfDays * 1; // 2 meals for full day, 1 meal for half day
                $mealCost = $totalMeals * ($breakdown->companion_base_meal_budget ?? 0);
            } else {
                // Calculate based on actual meal types from itinerary
                $mealCost = $this->calculateMealCostFromItinerary($companion, $itinerary, $breakdown);
            }
            
            // Calculate ticket cost (sum of all breakdown tickets)
            $ticketCost = $breakdown->tickets->sum('price') ?? 0;
            
            // Calculate experience cost based on companion category
            $experienceCost = $this->calculateExperienceCost($companion, $breakdown);
            
            // Calculate attraction cost based on companion category
            $attractionCost = $this->calculateAttractionCost($companion, $breakdown);
            
            // Calculate expense cost for per_person expenses
            $expenseCost = $this->calculateExpenseCost($breakdown);
            
            // Calculate accommodation cost
            $accommodationCost = $this->calculateAccommodationCost($companion, $breakdown, $itinerary);
            
            $companion->update([
                'meal_cost' => $mealCost,
                'ticket_cost' => $ticketCost,
                'experience_cost' => $experienceCost,
                'attraction_cost' => $attractionCost,
                'expense_cost' => $expenseCost,
                'accommodation_cost' => $accommodationCost,
            ]);
        }
    }

    /**
     * Calculate meal cost from itinerary for companions with same meal
     */
    private function calculateMealCostFromItinerary($companion, $itinerary, $breakdown): float
    {
        $totalMealCost = 0;
        
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
                        // Find meal price in breakdown
                        $breakdownMeal = $breakdown->meals()
                            ->where('meal_type_id', $mealTypeId)
                            ->first();
                        
                        if ($breakdownMeal) {
                            $mealPrice = $breakdownMeal->price ?? 0;
                            $totalMealCost += $mealPrice;
                        }
                    }
                }
            }
        }
        
        return $totalMealCost;
    }

    /**
     * Calculate experience cost based on companion category from breakdown experiences
     */
    private function calculateExperienceCost($companion, $breakdown): float
    {
        $totalExperienceCost = 0;
        
        // Get companion type and its category
        $companionType = $companion->companionType;
        if (!$companionType || !$companionType->companionCategory) {
            return 0;
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
            
            if ($shouldIncludeExperience) {
                $totalExperienceCost += $breakdownExperience->price ?? 0;
            }
        }
        
        return $totalExperienceCost;
    }

    /**
     * Calculate attraction cost based on companion category
     */
    private function calculateAttractionCost($companion, $breakdown): float
    {
        // Get companion type and its category
        $companionType = $companion->companionType;
        if (!$companionType || !$companionType->companionCategory) {
            return 0;
        }
        
        $categoryType = $companionType->companionCategory->category_type;
        
        // If companion is tour guide, attractions are free
        if ($categoryType === \App\Enums\CompanionCategoryEnum::TOUR_GUIDE) {
            return 0;
        }
        
        // For other companions, calculate total attraction cost from breakdown
        $totalAttractionCost = 0;
        
        foreach ($breakdown->attractions as $breakdownAttraction) {
            // Add main attraction entry price
            $totalAttractionCost += $breakdownAttraction->entry_price ?? 0;
            
            // Add sub-attraction prices
            foreach ($breakdownAttraction->subAttractions as $subAttraction) {
                $totalAttractionCost += $subAttraction->price ?? 0;
            }
        }
        
        return $totalAttractionCost;
    }

    /**
     * Calculate expense cost for per_person expenses
     */
    private function calculateExpenseCost($breakdown): float
    {
        $totalExpenseCost = 0;
        
        // Get all breakdown expenses that are per_person
        foreach ($breakdown->expenses as $breakdownExpense) {
            if ($breakdownExpense->charge_mode === \App\Enums\ChargeModeEnum::PER_PERSON) {
                $totalExpenseCost += $breakdownExpense->price ?? 0;
            }
        }
        
        return $totalExpenseCost;
    }

    /**
     * Calculate accommodation cost for companion
     */
    private function calculateAccommodationCost($companion, $breakdown, $itinerary): float
    {
        // Calculate number of nights companion needs accommodation
        $accommodationNights = $this->calculateAccommodationNights($companion, $itinerary);
        
        if ($accommodationNights <= 0) {
            return 0;
        }
        
        // If companion stays in same hotel as group
        if ($companion->is_stay_same_hotel) {
            return $this->calculateSameHotelAccommodationCost($companion, $breakdown, $itinerary);
        } else {
            // Use base accommodation budget
            return $accommodationNights * ($breakdown->companion_base_accommodation_budget ?? 0);
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
    private function calculateSameHotelAccommodationCost($companion, $breakdown, $itinerary): float
    {
        if (!$companion->room_category_id) {
            return 0;
        }
        
        $totalCost = 0;
        $livingCityId = $companion->living_city_id;
        $days = $itinerary->days->sortBy('day_number');
        
        // Calculate cost for each night based on the hotel for that specific day
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
                $totalCost += $costPerPerson;
            }
        }
        
        return $totalCost;
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
