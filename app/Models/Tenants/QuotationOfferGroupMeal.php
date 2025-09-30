<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupMeal extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'meal_type_id',
        'qty',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group for this meal.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the meal type for this offer group meal.
     */
    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by meal type.
     */
    public function scopeByMealType($query, $mealTypeId)
    {
        return $query->where('meal_type_id', $mealTypeId);
    }

    /**
     * Scope a query to filter by quantity range.
     */
    public function scopeByQuantityRange($query, $minQty, $maxQty)
    {
        return $query->whereBetween('qty', [$minQty, $maxQty]);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Get the meal type name.
     */
    public function getMealTypeNameAttribute(): ?string
    {
        return $this->mealType?->name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get total cost (qty * price).
     */
    public function getTotalCostAttribute(): float
    {
        return $this->qty * (float) $this->price;
    }

    /**
     * Get formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }
}
