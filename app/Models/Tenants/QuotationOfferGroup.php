<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
