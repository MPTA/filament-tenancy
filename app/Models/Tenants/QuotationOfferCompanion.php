<?php

namespace App\Models\Tenants;

use App\Models\Base\City;
use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferCompanion extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'companion_type_id',
        'half_days_qty',
        'full_days_qty',
        'is_stay_same_hotel',
        'is_same_meal',
        'room_category_id',
        'living_city_id',
        'accommodation_cost',
        'ticket_cost',
        'experience_cost',
        'meal_cost',
        'attraction_cost',
        'expense_cost',
        'tenant_id',
    ];

    protected $casts = [
        'half_days_qty' => 'integer',
        'full_days_qty' => 'integer',
        'is_stay_same_hotel' => 'boolean',
        'is_same_meal' => 'boolean',
        'accommodation_cost' => 'decimal:2',
        'ticket_cost' => 'decimal:2',
        'experience_cost' => 'decimal:2',
        'meal_cost' => 'decimal:2',
        'attraction_cost' => 'decimal:2',
        'expense_cost' => 'decimal:2',
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
            'total_quantity' => $this->total_quantity,
            'total_days' => $this->total_days,
        ];
    }

    /**
     * Check if this companion has any costs.
     */
    public function getHasCostsAttribute(): bool
    {
        return $this->total_cost > 0;
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
