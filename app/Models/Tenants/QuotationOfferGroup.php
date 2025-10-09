<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroup extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'number',
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
        'number' => 'integer',
        'is_include_driver_meal' => 'boolean',
        'is_include_driver_hotel' => 'boolean',
        'is_driver_stay_same_hotel' => 'boolean',
        'is_driver_same_meal' => 'boolean',
        'driver_meal_cost' => 'decimal:2',
        'driver_accommodation_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($offerGroup) {
            if (empty($offerGroup->number)) {
                $offerGroup->number = static::generateNextNumber($offerGroup->quotation_itinerary_id);
            }
        });
    }

    /**
     * Generate next offer group number for a quotation itinerary
     */
    protected static function generateNextNumber($quotationItineraryId): int
    {
        $lastGroup = static::where('quotation_itinerary_id', $quotationItineraryId)
            ->orderBy('number', 'desc')
            ->first();
        
        return $lastGroup ? $lastGroup->number + 1 : 1;
    }

    /**
     * Get the quotation itinerary for this offer group.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class);
    }

    /**
     * Get the full formatted offer group number (e.g., 100013-2)
     */
    public function getFullNumberAttribute(): string
    {
        $quotationNumber = $this->quotationItinerary
            ?->quotation
            ?->number ?? 'N/A';
        
        $groupNumber = $this->number ?? 'N/A';
        
        return "{$quotationNumber}-{$groupNumber}";
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
     * Get the quotation offer group experiences for this offer group (one-to-many relationship).
     */
    public function quotationOfferGroupExperiences(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupExperience::class);
    }


    /**
     * Calculate all costs from breakdown and create detailed records.
     */
    public function calculateAllCostsFromBreakdown(): void
    {
        $breakdown = $this->quotationItinerary->breakdown;
        
        if (!$breakdown) {
            return;
        }

        // Use database transaction to ensure all operations succeed or none
        DB::transaction(function () use ($breakdown) {
            $this->performCostCalculations($breakdown);
        });
    }

    /**
     * Calculate all costs from breakdown without wrapping in transaction.
     * Used when already inside a transaction (e.g., during edit).
     */
    public function calculateAllCostsFromBreakdownWithoutTransaction(): void
    {
        $breakdown = $this->quotationItinerary->breakdown;
        
        if (!$breakdown) {
            return;
        }

        $this->performCostCalculations($breakdown);
    }

    /**
     * Perform all cost calculations within transaction
     */
    private function performCostCalculations($breakdown): void
    {

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
            $this->calculateAndCreateCompanionMealRecords($companion, $itinerary, $breakdown);
            
            // Calculate ticket cost and create ticket records
            $this->calculateAndCreateCompanionTicketRecords($companion, $breakdown);
            
            // Calculate experience cost and create experience records
            $this->calculateAndCreateCompanionExperienceRecords($companion, $breakdown);
            
            // Calculate attraction cost and create attraction records
            $this->calculateAndCreateCompanionAttractionRecords($companion, $breakdown);
            
            // Calculate expense cost and create expense records
            $this->calculateAndCreateCompanionExpenseRecords($companion, $breakdown);
            
            // Calculate accommodation cost and create accommodation records
            $this->calculateAndCreateCompanionAccommodationRecords($companion, $breakdown, $itinerary);
            
            // No need to update ticket_cost as it's now calculated dynamically
        }
        
        // Calculate and create offer group records from breakdown
        $this->calculateAndCreateOfferGroupRecords($breakdown);
    }

    /**
     * Calculate and create offer group records from breakdown
     */
    private function calculateAndCreateOfferGroupRecords($breakdown): void
    {
        // Calculate and create offer group meals from breakdown
        $this->calculateAndCreateOfferGroupMeals($breakdown);
        
        // Calculate and create offer group expenses from breakdown
        $this->calculateAndCreateOfferGroupExpenses($breakdown);
        
        // Calculate and create offer group attractions from breakdown
        $this->calculateAndCreateOfferGroupAttractions($breakdown);
        
        // Calculate and create offer group tickets from breakdown
        $this->calculateAndCreateOfferGroupTickets($breakdown);
        
        // Calculate and create offer group experiences from breakdown
        $this->calculateAndCreateOfferGroupExperiences($breakdown);
    }

    /**
     * Calculate ticket cost and create ticket records
     */
    private function calculateAndCreateCompanionTicketRecords($companion, $breakdown): void
    {
        // Clear existing ticket records for this companion
        $companion->tickets()->delete();
        
        $itinerary = $this->quotationItinerary->itinerary;
        if (!$itinerary) {
            return;
        }
        
        // Get all breakdown tickets with their corresponding itinerary days
        foreach ($breakdown->tickets as $breakdownTicket) {
            if (($breakdownTicket->price ?? 0) <= 0) {
                continue;
            }
            
            // Find the day where this ticket is used
            $ticketDay = $itinerary->days()
                ->whereHas('activities.ticket', function ($query) use ($breakdownTicket) {
                    $query->where('to_city_id', $breakdownTicket->to_city_id)
                          ->where('transport_mode', $breakdownTicket->transport_mode?->value ?? $breakdownTicket->transport_mode);
                })
                ->first();
            
            if (!$ticketDay) {
                continue;
            }
            
            // Check if companion exists on the next day after ticket
            $nextDay = $itinerary->days()
                ->where('day_number', $ticketDay->day_number + 1)
                ->first();
            
            // If there's no next day, or companion exists on next day, include the ticket
            $shouldIncludeTicket = !$nextDay || $nextDay->companion_hire_mode;
            
            if ($shouldIncludeTicket) {
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
    private function calculateAndCreateCompanionMealRecords($companion, $itinerary, $breakdown): void
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
            
            // Check if this day has free hotel breakfast
            $hasFreeBreakfast = $this->checkIfDayHasFreeBreakfast($day, $breakdown);
            
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
                        // Full day: include lunch and dinner, plus breakfast if not free
                        if ($mealPart === 'breakfast') {
                            $shouldIncludeMeal = !$hasFreeBreakfast;
                        } else {
                            $shouldIncludeMeal = in_array($mealPart, ['lunch', 'dinner']);
                        }
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
     * Check if a day has free breakfast from hotel
     */
    private function checkIfDayHasFreeBreakfast($day, $breakdown): bool
    {
        // If no accommodation, breakfast is not free (needs to be paid)
        if (!$day->accommodation_id) {
            return false;
        }
        
        // Check if this accommodation in breakdown has free breakfast
        $breakdownAccommodation = $breakdown->accommodations()
            ->where('accommodation_id', $day->accommodation_id)
            ->first();
        
        // If accommodation found and has_breakfast is true, breakfast is free
        return $breakdownAccommodation?->has_breakfast ?? false;
    }

    /**
     * Calculate experience cost and create experience records
     */
    private function calculateAndCreateCompanionExperienceRecords($companion, $breakdown): void
    {
        // Clear existing experience records for this companion
        $companion->experiences()->delete();
        
        // Get companion type and its category
        $companionType = $companion->companionType;
        if (!$companionType || !$companionType->companionCategory) {
            return;
        }
        
        $categoryType = $companionType->companionCategory->category_type;
        
        // Get all breakdown experiences (only PER_PERSON experiences for companions)
        foreach ($breakdown->experiences as $breakdownExperience) {
            // Skip PER_GROUP experiences - companions should only have PER_PERSON experiences
            if ($breakdownExperience->experience && 
                $breakdownExperience->experience->charge_mode !== \App\Enums\ChargeModeEnum::PER_PERSON) {
                continue;
            }
            
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
    private function calculateAndCreateCompanionAttractionRecords($companion, $breakdown): void
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
    private function calculateAndCreateCompanionExpenseRecords($companion, $breakdown): void
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
    private function calculateAndCreateCompanionAccommodationRecords($companion, $breakdown, $itinerary): void
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
     * Calculate and create offer group meals from breakdown
     */
    private function calculateAndCreateOfferGroupMeals($breakdown): void
    {
        // Clear existing offer group meal records
        $this->quotationOfferGroupMeals()->delete();
        
        $itinerary = $this->quotationItinerary->itinerary;
        if (!$itinerary) {
            return;
        }
        
        $mealCounts = [];
        $days = $itinerary->days->sortBy('day_number');
        
        foreach ($days as $index => $day) {
            // Get meal activities for this day
            $mealActivities = $day->activities()
                ->whereHas('activityCategory', function ($query) {
                    $query->where('type', \App\Enums\ActivityCategoryTypeEnum::MEAL->value);
                })
                ->with('meal.mealType')
                ->get();
            
            foreach ($mealActivities as $activity) {
                if (!$activity->meal?->mealType) {
                    continue;
                }
                
                $mealTypeId = $activity->meal->meal_type_id;
                $mealPart = $activity->meal->meal_part?->value ?? $activity->meal->meal_part;
                
                // Check if this meal should be counted
                $shouldCountMeal = $this->shouldCountMeal($day, $mealPart, $index, $days, $breakdown);
                
                if ($shouldCountMeal) {
                    if (!isset($mealCounts[$mealTypeId])) {
                        $mealCounts[$mealTypeId] = 0;
                    }
                    $mealCounts[$mealTypeId]++;
                }
            }
        }
        
        // Create meal records for each meal type
        foreach ($mealCounts as $mealTypeId => $qty) {
            $breakdownMeal = $breakdown->meals()
                ->where('meal_type_id', $mealTypeId)
                ->first();
            
            if ($breakdownMeal && $qty > 0) {
                $this->quotationOfferGroupMeals()->create([
                    'meal_type_id' => $mealTypeId,
                    'qty' => $qty,
                    'price' => $breakdownMeal->price ?? 0,
                ]);
            }
        }
    }
    
    /**
     * Check if a meal should be counted based on accommodation and breakfast logic
     */
    private function shouldCountMeal($currentDay, $mealPart, $dayIndex, $days, $breakdown): bool
    {
        // For lunch and dinner, always count them
        if (in_array($mealPart, ['lunch', 'dinner'])) {
            return true;
        }
        
        // For breakfast, check accommodation logic
        if ($mealPart === 'breakfast') {
            // If it's the first day, count breakfast (no previous night accommodation)
            if ($dayIndex === 0) {
                return true;
            }
            
            // Check if there was accommodation the previous night
            $previousDay = $days->get($dayIndex - 1);
            if (!$previousDay || !$previousDay->accommodation_id) {
                // No accommodation previous night, count breakfast
                return true;
            }
            
            // Check if the accommodation includes breakfast
            $breakdownAccommodation = $breakdown->accommodations()
                ->where('accommodation_id', $previousDay->accommodation_id)
                ->first();
            
            if (!$breakdownAccommodation) {
                // No breakdown accommodation found, count breakfast
                return true;
            }
            
            // Check if accommodation includes breakfast
            // This would need to be determined based on your business logic
            // For now, we'll assume if accommodation exists, breakfast is included
            // You might need to add a field to track this in your accommodation model
            $accommodationIncludesBreakfast = $this->checkAccommodationIncludesBreakfast($breakdownAccommodation);
            
            if ($accommodationIncludesBreakfast) {
                // Breakfast is included in accommodation, don't count it
                return false;
            } else {
                // Breakfast is not included in accommodation, count it
                return true;
            }
        }
        
        return true;
    }
    
    /**
     * Check if accommodation includes breakfast
     */
    private function checkAccommodationIncludesBreakfast($breakdownAccommodation): bool
    {
        // Check the has_breakfast field in breakdown_accommodations table
        return (bool) ($breakdownAccommodation->has_breakfast ?? false);
    }

    /**
     * Calculate and create offer group expenses from breakdown
     */
    private function calculateAndCreateOfferGroupExpenses($breakdown): void
    {
        // Clear existing offer group expense records
        $this->quotationOfferGroupExpenses()->delete();
        
        // Get all breakdown expenses
        foreach ($breakdown->expenses as $breakdownExpense) {
            if (($breakdownExpense->price ?? 0) > 0) {
                $this->quotationOfferGroupExpenses()->create([
                    'description' => $breakdownExpense->description ?? 'Expense',
                    'price' => $breakdownExpense->price ?? 0,
                    'charge_mode' => $breakdownExpense->charge_mode ?? \App\Enums\ChargeModeEnum::PER_GROUP->value,
                ]);
            }
        }
    }

    /**
     * Calculate and create offer group attractions from breakdown
     */
    private function calculateAndCreateOfferGroupAttractions($breakdown): void
    {
        // Clear existing offer group attraction records
        $this->quotationOfferGroupAttractions()->delete();
        
        // Get all breakdown attractions (including free ones)
        foreach ($breakdown->attractions()->get() as $breakdownAttraction) {
            $offerGroupAttraction = $this->quotationOfferGroupAttractions()->create([
                'attraction_id' => $breakdownAttraction->attraction_id,
                'price' => $breakdownAttraction->entry_price ?? 0,
                'is_outview' => $breakdownAttraction->is_outview ?? false,
            ]);
            
            // Create sub-attraction records for this attraction
            $this->createOfferGroupSubAttractions($offerGroupAttraction, $breakdownAttraction);
        }
    }

    /**
     * Create offer group sub-attractions for a given attraction
     */
    private function createOfferGroupSubAttractions($offerGroupAttraction, $breakdownAttraction): void
    {
        // Get sub-attractions from breakdown for this attraction (including free ones)
        $breakdownSubAttractions = $breakdownAttraction->subAttractions()->get();
        
        foreach ($breakdownSubAttractions as $breakdownSubAttraction) {
            $offerGroupAttraction->quotationOfferGroupSubAttractions()->create([
                'sub_attraction_id' => $breakdownSubAttraction->sub_attraction_id,
                'price' => $breakdownSubAttraction->price ?? 0,
            ]);
        }
    }

    /**
     * Calculate and create offer group tickets from breakdown
     */
    private function calculateAndCreateOfferGroupTickets($breakdown): void
    {
        // Clear existing offer group ticket records
        $this->quotationOfferGroupTickets()->delete();
        
        // Get all breakdown tickets (including free ones)
        foreach ($breakdown->tickets()->get() as $breakdownTicket) {
            $this->quotationOfferGroupTickets()->create([
                'from_city_id' => $breakdownTicket->from_city_id,
                'to_city_id' => $breakdownTicket->to_city_id,
                'class' => $breakdownTicket->class ?? \App\Enums\TicketClassEnum::ECONOMY->value,
                'price' => $breakdownTicket->price ?? 0,
            ]);
        }
    }

    /**
     * Calculate and create offer group experiences from breakdown
     */
    private function calculateAndCreateOfferGroupExperiences($breakdown): void
    {
        // Clear existing offer group experience records
        $this->quotationOfferGroupExperiences()->delete();
        
        // Get all breakdown experiences (including free ones)
        foreach ($breakdown->experiences()->get() as $breakdownExperience) {
            $this->quotationOfferGroupExperiences()->create([
                'experience_id' => $breakdownExperience->experience_id,
                'price' => $breakdownExperience->price ?? 0,
            ]);
        }
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

    /**
     * Get total offer group meal cost (per person).
     */
    public function getTotalOfferGroupMealCostAttribute(): float
    {
        $total = 0;
        foreach ($this->quotationOfferGroupMeals as $meal) {
            $total += ($meal->qty ?? 1) * ($meal->price ?? 0);
        }
        return (float) $total;
    }

    /**
     * Get formatted total offer group meal cost.
     */
    public function getFormattedTotalOfferGroupMealCostAttribute(): string
    {
        return number_format($this->total_offer_group_meal_cost, 2);
    }

    /**
     * Get offer group meal breakdown as array.
     */
    public function getOfferGroupMealBreakdownAttribute(): array
    {
        $breakdown = [];
        foreach ($this->quotationOfferGroupMeals as $meal) {
            $breakdown[] = [
                'meal_type' => $meal->mealType?->name ?? 'Unknown',
                'qty' => $meal->qty ?? 1,
                'price' => (float) ($meal->price ?? 0),
                'total' => ($meal->qty ?? 1) * ($meal->price ?? 0),
            ];
        }
        return $breakdown;
    }

    /**
     * Get total individual expenses (per person).
     */
    public function getTotalIndividualExpensesAttribute(): float
    {
        $total = 0;
        foreach ($this->quotationOfferGroupExpenses as $expense) {
            if ($expense->charge_mode === \App\Enums\ChargeModeEnum::PER_PERSON->value) {
                $total += (float) ($expense->price ?? 0);
            }
        }
        return $total;
    }

    /**
     * Get total group expenses (one time).
     */
    public function getTotalGroupExpensesAttribute(): float
    {
        $total = 0;
        foreach ($this->quotationOfferGroupExpenses as $expense) {
            if ($expense->charge_mode === \App\Enums\ChargeModeEnum::PER_GROUP->value) {
                $total += (float) ($expense->price ?? 0);
            }
        }
        return $total;
    }

    /**
     * Get formatted total individual expenses.
     */
    public function getFormattedTotalIndividualExpensesAttribute(): string
    {
        return number_format($this->total_individual_expenses, 2);
    }

    /**
     * Get formatted total group expenses.
     */
    public function getFormattedTotalGroupExpensesAttribute(): string
    {
        return number_format($this->total_group_expenses, 2);
    }

    /**
     * Get offer group expense breakdown as array.
     */
    public function getOfferGroupExpenseBreakdownAttribute(): array
    {
        $breakdown = [
            'individual' => [],
            'group' => [],
            'total_individual' => 0,
            'total_group' => 0,
        ];

        foreach ($this->quotationOfferGroupExpenses as $expense) {
            $expenseData = [
                'description' => $expense->description ?? 'Expense',
                'price' => (float) ($expense->price ?? 0),
                'charge_mode' => $expense->charge_mode,
            ];

            if ($expense->charge_mode === \App\Enums\ChargeModeEnum::PER_PERSON->value) {
                $breakdown['individual'][] = $expenseData;
                $breakdown['total_individual'] += $expenseData['price'];
            } else {
                $breakdown['group'][] = $expenseData;
                $breakdown['total_group'] += $expenseData['price'];
            }
        }

        return $breakdown;
    }

    /**
     * Get total attraction cost (per person).
     */
    public function getTotalAttractionCostAttribute(): float
    {
        $total = 0;
        
        // Add main attraction costs
        foreach ($this->quotationOfferGroupAttractions as $attraction) {
            $total += (float) ($attraction->price ?? 0);
        }
        
        // Add sub-attraction costs
        foreach ($this->quotationOfferGroupAttractions as $attraction) {
            foreach ($attraction->quotationOfferGroupSubAttractions as $subAttraction) {
                $total += (float) ($subAttraction->price ?? 0);
            }
        }
        
        return $total;
    }

    /**
     * Get formatted total attraction cost.
     */
    public function getFormattedTotalAttractionCostAttribute(): string
    {
        return number_format($this->total_attraction_cost, 2);
    }

    /**
     * Get offer group attraction breakdown as array.
     */
    public function getOfferGroupAttractionBreakdownAttribute(): array
    {
        $breakdown = [];
        
        foreach ($this->quotationOfferGroupAttractions as $attraction) {
            $attractionData = [
                'attraction_name' => $attraction->attraction?->name ?? 'Unknown',
                'attraction_price' => (float) ($attraction->price ?? 0),
                'sub_attractions' => [],
                'total_price' => (float) ($attraction->price ?? 0),
            ];
            
            // Add sub-attractions
            foreach ($attraction->quotationOfferGroupSubAttractions as $subAttraction) {
                $subAttractionData = [
                    'sub_attraction_name' => $subAttraction->subAttraction?->name ?? 'Unknown',
                    'price' => (float) ($subAttraction->price ?? 0),
                ];
                
                $attractionData['sub_attractions'][] = $subAttractionData;
                $attractionData['total_price'] += $subAttractionData['price'];
            }
            
            $breakdown[] = $attractionData;
        }
        
        return $breakdown;
    }

    /**
     * Get total ticket cost (per person).
     */
    public function getTotalTicketCostAttribute(): float
    {
        $total = 0;
        foreach ($this->quotationOfferGroupTickets as $ticket) {
            $total += (float) ($ticket->price ?? 0);
        }
        return $total;
    }

    /**
     * Get formatted total ticket cost.
     */
    public function getFormattedTotalTicketCostAttribute(): string
    {
        return number_format($this->total_ticket_cost, 2);
    }

    /**
     * Get offer group ticket breakdown as array.
     */
    public function getOfferGroupTicketBreakdownAttribute(): array
    {
        $breakdown = [];
        
        foreach ($this->quotationOfferGroupTickets as $ticket) {
            $breakdown[] = [
                'from_city' => $ticket->fromCity?->name ?? 'Unknown',
                'to_city' => $ticket->toCity?->name ?? 'Unknown',
                'class' => $ticket->class,
                'price' => (float) ($ticket->price ?? 0),
            ];
        }
        
        return $breakdown;
    }

    /**
     * Get total experience cost (per person).
     */
    public function getTotalExperienceCostAttribute(): float
    {
        $total = 0;
        foreach ($this->quotationOfferGroupExperiences as $experience) {
            $total += (float) ($experience->price ?? 0);
        }
        return $total;
    }

    /**
     * Get formatted total experience cost.
     */
    public function getFormattedTotalExperienceCostAttribute(): string
    {
        return number_format($this->total_experience_cost, 2);
    }

    /**
     * Get offer group experience breakdown as array.
     */
    public function getOfferGroupExperienceBreakdownAttribute(): array
    {
        $breakdown = [];
        
        foreach ($this->quotationOfferGroupExperiences as $experience) {
            $breakdown[] = [
                'experience_name' => $experience->experience?->name ?? 'Unknown',
                'price' => (float) ($experience->price ?? 0),
            ];
        }
        
        return $breakdown;
    }

    /**
     * Recalculate all offers in this offer group.
     * Used when offer group settings or companions change.
     */
    public function recalculateAllOffers(): void
    {
        foreach ($this->quotationOffers as $offer) {
            $offer->recalculateAllCosts();
        }
    }

    /**
     * Recalculate all offers without wrapping in transaction.
     * Used when already inside a transaction (e.g., during edit).
     */
    public function recalculateAllOffersWithoutTransaction(): void
    {
        foreach ($this->quotationOffers as $offer) {
            $offer->recalculateAllCostsWithoutTransaction();
        }
    }
}
