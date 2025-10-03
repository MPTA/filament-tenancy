<?php

namespace App\Models\Tenants;

use App\Models\Base\City;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanion extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companions';

    protected $fillable = [
        'quotation_offer_group_id',
        'companion_type_id',
        'half_days_qty',
        'full_days_qty',
        'hours_qty',
        'day_price',
        'half_day_price',
        'is_stay_same_hotel',
        'is_same_meal',
        'room_category_id',
        'living_city_id',
        'tenant_id',
    ];

    protected $casts = [
        'half_days_qty' => 'integer',
        'full_days_qty' => 'integer',
        'hours_qty' => 'integer',
        'day_price' => 'decimal:2',
        'half_day_price' => 'decimal:2',
        'is_stay_same_hotel' => 'boolean',
        'is_same_meal' => 'boolean',
    ];

    /**
     * Get the quotation offer group for this companion.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the companion type for this offer companion.
     */
    public function companionType(): BelongsTo
    {
        return $this->belongsTo(CompanionType::class);
    }

    /**
     * Get the room category for this companion.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the living city for this companion.
     */
    public function livingCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'living_city_id');
    }

    /**
     * Get the meals for this companion.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionMeal::class);
    }

    /**
     * Get the attractions for this companion.
     */
    public function attractions(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionAttraction::class);
    }

    /**
     * Get the experiences for this companion.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionExperience::class);
    }

    /**
     * Get the accommodations for this companion.
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionAccommodation::class);
    }

    /**
     * Get the expenses for this companion.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionExpense::class);
    }

    /**
     * Get the tickets for this companion.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupCompanionTicket::class);
    }

    /**
     * Get the total meal cost for this companion.
     */
    public function getMealCostAttribute(): float
    {
        return $this->meals->sum(function($meal) {
            return $meal->qty * $meal->price;
        });
    }

    /**
     * Get the total experience cost for this companion.
     */
    public function getExperienceCostAttribute(): float
    {
        return $this->experiences->sum('price');
    }

    /**
     * Get the total attraction cost for this companion.
     */
    public function getAttractionCostAttribute(): float
    {
        $totalCost = 0;
        
        foreach ($this->attractions as $attraction) {
            $totalCost += $attraction->price ?? 0;
            
            // Add sub-attraction costs
            foreach ($attraction->subAttractions as $subAttraction) {
                $totalCost += $subAttraction->price ?? 0;
            }
        }
        
        return $totalCost;
    }

    /**
     * Get the total expense cost for this companion.
     */
    public function getExpenseCostAttribute(): float
    {
        return $this->expenses->sum('price');
    }

    /**
     * Get the total accommodation cost for this companion.
     */
    public function getAccommodationCostAttribute(): float
    {
        return $this->accommodations->sum(function($accommodation) {
            return $accommodation->nights * $accommodation->night_price;
        });
    }

    /**
     * Get the total ticket cost for this companion.
     */
    public function getTicketCostAttribute(): float
    {
        return $this->tickets->sum('price');
    }

    /**
     * Get the base budget meal cost for this companion.
     */
    public function getBaseBudgetMealCostAttribute(): float
    {
        return $this->meals()->baseBudget()->get()->sum(function($meal) {
            return $meal->qty * $meal->price;
        });
    }

    /**
     * Get the specific meal type cost for this companion.
     */
    public function getSpecificMealTypeCostAttribute(): float
    {
        return $this->meals()->whereNotNull('meal_type_id')->get()->sum(function($meal) {
            return $meal->qty * $meal->price;
        });
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by companion type.
     */
    public function scopeByCompanionType($query, $companionTypeId)
    {
        return $query->where('companion_type_id', $companionTypeId);
    }

    /**
     * Scope a query to filter by stay same hotel.
     */
    public function scopeStaySameHotel($query, $value = true)
    {
        return $query->where('is_stay_same_hotel', $value);
    }

    /**
     * Scope a query to filter by same meal.
     */
    public function scopeSameMeal($query, $value = true)
    {
        return $query->where('is_same_meal', $value);
    }

    /**
     * Scope a query to filter by room category.
     */
    public function scopeByRoomCategory($query, $roomCategoryId)
    {
        return $query->where('room_category_id', $roomCategoryId);
    }

    /**
     * Scope a query to filter by living city.
     */
    public function scopeByLivingCity($query, $cityId)
    {
        return $query->where('living_city_id', $cityId);
    }

    /**
     * Scope a query to filter by half days quantity.
     */
    public function scopeByHalfDaysQty($query, $minQty = 0, $maxQty = null)
    {
        if ($maxQty === null) {
            return $query->where('half_days_qty', '>=', $minQty);
        }
        return $query->whereBetween('half_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by full days quantity.
     */
    public function scopeByFullDaysQty($query, $minQty = 0, $maxQty = null)
    {
        if ($maxQty === null) {
            return $query->where('full_days_qty', '>=', $minQty);
        }
        return $query->whereBetween('full_days_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by hours quantity.
     */
    public function scopeByHoursQty($query, $minQty = 0, $maxQty = null)
    {
        if ($maxQty === null) {
            return $query->where('hours_qty', '>=', $minQty);
        }
        return $query->whereBetween('hours_qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by day price range.
     */
    public function scopeByDayPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('day_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by half day price range.
     */
    public function scopeByHalfDayPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('half_day_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by cost range.
     */
    public function scopeByCostRange($query, $minCost, $maxCost, $costField = 'accommodation_cost')
    {
        return $query->whereBetween($costField, [$minCost, $maxCost]);
    }

    /**
     * Scope a query to filter by total cost range.
     */
    public function scopeByTotalCostRange($query, $minCost, $maxCost)
    {
        return $query->whereRaw('(accommodation_cost + ticket_cost + experience_cost + meal_cost + attraction_cost + expense_cost) BETWEEN ? AND ?', [$minCost, $maxCost]);
    }

    /**
     * Get the total cost for this companion.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->accommodation_cost + 
               (float) $this->ticket_cost + 
               (float) $this->experience_cost + 
               (float) $this->meal_cost + 
               (float) $this->attraction_cost + 
               (float) $this->expense_cost;
    }

    /**
     * Get the formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }

    /**
     * Get the formatted accommodation cost.
     */
    public function getFormattedAccommodationCostAttribute(): string
    {
        return number_format((float) $this->accommodation_cost, 2);
    }

    /**
     * Get the formatted ticket cost.
     */
    public function getFormattedTicketCostAttribute(): string
    {
        return number_format((float) $this->ticket_cost, 2);
    }

    /**
     * Get the formatted experience cost.
     */
    public function getFormattedExperienceCostAttribute(): string
    {
        return number_format((float) $this->experience_cost, 2);
    }

    /**
     * Get the formatted meal cost.
     */
    public function getFormattedMealCostAttribute(): string
    {
        return number_format((float) $this->meal_cost, 2);
    }

    /**
     * Get the formatted attraction cost.
     */
    public function getFormattedAttractionCostAttribute(): string
    {
        return number_format((float) $this->attraction_cost, 2);
    }

    /**
     * Get the formatted expense cost.
     */
    public function getFormattedExpenseCostAttribute(): string
    {
        return number_format((float) $this->expense_cost, 2);
    }

    /**
     * Get the companion type name.
     */
    public function getCompanionTypeNameAttribute(): ?string
    {
        return $this->companionType?->name;
    }

    /**
     * Get the room category name.
     */
    public function getRoomCategoryNameAttribute(): ?string
    {
        return $this->roomCategory?->name;
    }

    /**
     * Get the living city name.
     */
    public function getLivingCityNameAttribute(): ?string
    {
        return $this->livingCity?->name;
    }

    /**
     * Check if companion stays in the same hotel.
     */
    public function getStaysSameHotelAttribute(): bool
    {
        return $this->is_stay_same_hotel;
    }

    /**
     * Check if companion has same meal.
     */
    public function getHasSameMealAttribute(): bool
    {
        return $this->is_same_meal;
    }

    /**
     * Get total quantity (half days + full days).
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->half_days_qty + $this->full_days_qty;
    }

    /**
     * Get total days (half days + full days).
     */
    public function getTotalDaysAttribute(): int
    {
        return $this->half_days_qty + $this->full_days_qty;
    }

    /**
     * Get quantity breakdown as array.
     */
    public function getQuantityBreakdownAttribute(): array
    {
        return [
            'half_days' => $this->half_days_qty,
            'full_days' => $this->full_days_qty,
            'hours' => $this->hours_qty,
            'total_quantity' => $this->total_quantity,
            'total_days' => $this->total_days,
        ];
    }

    /**
     * Get formatted day price.
     */
    public function getFormattedDayPriceAttribute(): string
    {
        return number_format((float) $this->day_price, 2);
    }

    /**
     * Get formatted half day price.
     */
    public function getFormattedHalfDayPriceAttribute(): string
    {
        return number_format((float) $this->half_day_price, 2);
    }

    /**
     * Get calculated cost based on quantities and prices.
     */
    public function getCalculatedPriceAttribute(): float
    {
        return ($this->full_days_qty * (float) $this->day_price) + 
               ($this->half_days_qty * (float) $this->half_day_price);
    }

    /**
     * Get formatted calculated price.
     */
    public function getFormattedCalculatedPriceAttribute(): string
    {
        return number_format($this->calculated_price, 2);
    }

    /**
     * Check if this companion has any costs.
     */
    public function getHasCostsAttribute(): bool
    {
        return $this->total_cost > 0;
    }

    /**
     * Get total companion salary.
     */
    public function getTotalCompanionSalaryAttribute(): float
    {
        return ($this->full_days_qty * $this->day_price) + ($this->half_days_qty * $this->half_day_price);
    }

    /**
     * Get cost breakdown as array.
     */
    public function getCostBreakdownAttribute(): array
    {
        return [
            'accommodation' => (float) $this->accommodation_cost,
            'ticket' => (float) $this->ticket_cost,
            'experience' => (float) $this->experience_cost,
            'meal' => (float) $this->meal_cost,
            'attraction' => (float) $this->attraction_cost,
            'expense' => (float) $this->expense_cost,
            'total' => $this->total_cost,
        ];
    }
}
