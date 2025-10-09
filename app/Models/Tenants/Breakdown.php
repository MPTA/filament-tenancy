<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Breakdown extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_itinerary_id',
        'tenant_id',
        'creator_user_id',
        'currency_id',
        'vehicle_days_qty',
        'vehicle_half_days_qty',
        'vehicle_airport_transfers_qty',
        'vehicle_hours_qty',
        'vehicle_empty_backs_qty',
        'driver_base_meal_budget',
        'driver_base_accommodation_budget',
        'companion_base_meal_budget',
        'companion_base_accommodation_budget',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_days_qty' => 'integer',
            'vehicle_half_days_qty' => 'integer',
            'vehicle_airport_transfers_qty' => 'integer',
            'vehicle_hours_qty' => 'integer',
            'vehicle_empty_backs_qty' => 'integer',
            'driver_base_meal_budget' => 'decimal:2',
            'driver_base_accommodation_budget' => 'decimal:2',
            'companion_base_meal_budget' => 'decimal:2',
            'companion_base_accommodation_budget' => 'decimal:2',
            'is_completed' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // When breakdown is updated (but not when is_completed is being set to true), mark as incomplete
        static::updating(function ($breakdown) {
            if ($breakdown->isDirty() && !$breakdown->isDirty('is_completed')) {
                $breakdown->is_completed = false;
            }
        });

        // When breakdown completion status changes, manage offer groups
        static::updated(function ($breakdown) {
            if ($breakdown->wasChanged('is_completed')) {
                if ($breakdown->is_completed) {
                    // Unlock and recalculate all offer groups when completed
                    $breakdown->syncAllOfferGroups();
                } else {
                    // Lock all offer groups when becomes incomplete
                    $breakdown->lockAllOfferGroups();
                }
            }
        });
    }

    /**
     * Get the quotation itinerary that owns the breakdown.
     */
    public function quotationItinerary(): BelongsTo
    {
        return $this->belongsTo(QuotationItinerary::class, 'quotation_itinerary_id');
    }

    /**
     * Get the user who created this breakdown.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_user_id');
    }

    /**
     * Get the currency for this breakdown.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Currency::class, 'currency_id');
    }

    /**
     * Get the vehicle types with pricing for this breakdown.
     */
    public function vehicleTypes(): HasMany
    {
        return $this->hasMany(BreakdownVehicleType::class);
    }

    /**
     * Get the companions with pricing for this breakdown.
     */
    public function companions(): HasMany
    {
        return $this->hasMany(BreakdownCompanion::class);
    }

    /**
     * Get the tickets with pricing for this breakdown.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(BreakdownTicket::class);
    }

    /**
     * Get the meals with pricing for this breakdown.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(BreakdownMeal::class);
    }

    /**
     * Get the experiences with pricing for this breakdown.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(BreakdownExperience::class);
    }

    /**
     * Get the expenses for this breakdown.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(BreakdownExpense::class);
    }

    /**
     * Get the accommodations for this breakdown.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(BreakdownAccommodation::class);
    }

    /**
     * Get the attractions for this breakdown.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(BreakdownAttraction::class);
    }

    /**
     * Get paid meals from itinerary with quantities and prices.
     * Returns array of meals that need to be paid for (excluding free breakfasts).
     */
    public function getPaidMealsFromItinerary(): array
    {
        $itinerary = $this->quotationItinerary->itinerary;
        if (!$itinerary) {
            return [];
        }

        // Get all itinerary days with meals
        $itineraryDays = $itinerary->days()
            ->with([
                'activities.meal.mealType',
                'accommodation'
            ])
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            return [];
        }

        // Collect meal quantities by meal type
        $mealQuantities = collect();
        $lastDayNumber = $itineraryDays->max('day_number');
        $firstDayNumber = $itineraryDays->min('day_number');

        foreach ($itineraryDays as $day) {
            // Check if this is the first or last day
            $isFirstDay = $day->day_number === $firstDayNumber;
            $isLastDay = $day->day_number === $lastDayNumber;
            
            // Check if accommodation has breakfast (from breakdown_accommodations)
            $hasBreakfast = false;
            if ($day->accommodation) {
                $breakdownAccommodation = $this->accommodations()
                    ->where('accommodation_id', $day->accommodation->id)
                    ->first();
                
                if ($breakdownAccommodation) {
                    $hasBreakfast = $breakdownAccommodation->has_breakfast ?? false;
                }
            }

            foreach ($day->activities as $activity) {
                if ($activity->meal) {
                    $mealTypeId = $activity->meal->meal_type_id;
                    $mealPart = $activity->meal->meal_part;

                    // Skip breakfast on the last day (guests check out before breakfast)
                    if ($isLastDay && $mealPart === \App\Enums\MealPartEnum::BREAKFAST) {
                        continue;
                    }

                    // Skip breakfast on middle days if accommodation has free breakfast
                    // BUT count breakfast on first day (guests check in later, breakfast is before check-in)
                    if (!$isFirstDay && $hasBreakfast && $mealPart === \App\Enums\MealPartEnum::BREAKFAST) {
                        continue;
                    }

                    // Count this meal type
                    if (!$mealQuantities->has($mealTypeId)) {
                        $mealQuantities->put($mealTypeId, 0);
                    }
                    $mealQuantities->put($mealTypeId, $mealQuantities->get($mealTypeId) + 1);
                }
            }
        }

        // Get prices from breakdown and return array
        $paidMeals = [];
        foreach ($mealQuantities as $mealTypeId => $qty) {
            $breakdownMeal = $this->meals()
                ->where('meal_type_id', $mealTypeId)
                ->first();

            if ($breakdownMeal) {
                $paidMeals[] = [
                    'meal_type_id' => $mealTypeId,
                    'qty' => $qty,
                    'price' => $breakdownMeal->price,
                ];
            }
        }

        return $paidMeals;
    }

    /**
     * Calculate vehicle usage quantities based on itinerary days.
     * Returns array with vehicle_days_qty, vehicle_half_days_qty, vehicle_hours_qty.
     */
    public function calculateVehicleUsageQuantities(): array
    {
        $itinerary = $this->quotationItinerary->itinerary;
        if (!$itinerary) {
            return [
                'vehicle_days_qty' => 0,
                'vehicle_half_days_qty' => 0,
                'vehicle_hours_qty' => 0,
            ];
        }

        // Get all itinerary days
        $itineraryDays = $itinerary->days()
            ->orderBy('day_number')
            ->get();

        if ($itineraryDays->isEmpty()) {
            return [
                'vehicle_days_qty' => 0,
                'vehicle_half_days_qty' => 0,
                'vehicle_hours_qty' => 0,
            ];
        }

        $fullDays = 0;
        $halfDays = 0;
        $hours = 0;

        foreach ($itineraryDays as $day) {
            // Check vehicle usage mode directly on the day
            if ($day->vehicle_usage_mode) {
                switch ($day->vehicle_usage_mode) {
                    case \App\Enums\VehicleUsageModeEnum::FULL_DAY:
                        $fullDays++;
                        break;
                    case \App\Enums\VehicleUsageModeEnum::HALF_DAY:
                        $halfDays++;
                        break;
                    case \App\Enums\VehicleUsageModeEnum::HOUR:
                        $hours += $day->vehicle_hours ?? 1; // Use actual hours or default to 1
                        break;
                }
            }
        }

        return [
            'vehicle_days_qty' => $fullDays,
            'vehicle_half_days_qty' => $halfDays,
            'vehicle_hours_qty' => $hours,
        ];
    }

    /**
     * Get vehicle days quantity attribute (accessor).
     */
    public function getCalculatedVehicleDaysQtyAttribute(): int
    {
        return $this->calculateVehicleUsageQuantities()['vehicle_days_qty'];
    }

    /**
     * Get vehicle half days quantity attribute (accessor).
     */
    public function getCalculatedVehicleHalfDaysQtyAttribute(): int
    {
        return $this->calculateVehicleUsageQuantities()['vehicle_half_days_qty'];
    }

    /**
     * Get vehicle hours quantity attribute (accessor).
     */
    public function getCalculatedVehicleHoursQtyAttribute(): int
    {
        return $this->calculateVehicleUsageQuantities()['vehicle_hours_qty'];
    }

    /**
     * Sync all offer groups - unlock and recalculate.
     * Called when breakdown becomes complete.
     */
    public function syncAllOfferGroups(): void
    {
        $offerGroups = $this->quotationItinerary->quotationOfferGroups;
        
        if ($offerGroups->isEmpty()) {
            return;
        }

        // Use transaction to ensure all operations are atomic
        DB::transaction(function () use ($offerGroups) {
            foreach ($offerGroups as $group) {
                // Skip if decoupled (Phase 2 feature)
                if ($group->link_status === \App\Enums\OfferGroupLinkStatusEnum::DECOUPLED) {
                    continue;
                }

                // Unlock and update sync timestamp
                $group->update([
                    'is_locked' => false,
                    'last_breakdown_sync_at' => now(),
                ]);

                // Recalculate all costs from breakdown (without nested transaction)
                $group->calculateAllCostsFromBreakdownWithoutTransaction();
                
                // Recalculate all offers in this group (without nested transaction)
                $group->recalculateAllOffersWithoutTransaction();
            }
        });
    }

    /**
     * Lock all offer groups.
     * Called when breakdown becomes incomplete (e.g., when itinerary or breakdown is edited).
     */
    public function lockAllOfferGroups(): void
    {
        $this->quotationItinerary->quotationOfferGroups()
            ->update(['is_locked' => true]);
    }
}
