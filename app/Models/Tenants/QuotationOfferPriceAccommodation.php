<?php

namespace App\Models\Tenants;

use App\Models\Base\Accommodation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferPriceAccommodation extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_price_id',
        'accommodation_id',
        'nights',
        'price',
        'is_include_breakfast',
        'is_include_lunch',
        'is_include_dinner',
        'tenant_id',
    ];

    protected $casts = [
        'nights' => 'integer',
        'price' => 'decimal:2',
        'is_include_breakfast' => 'boolean',
        'is_include_lunch' => 'boolean',
        'is_include_dinner' => 'boolean',
    ];

    /**
     * Get the quotation offer price for this accommodation.
     */
    public function quotationOfferPrice(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferPrice::class);
    }

    /**
     * Get the accommodation for this offer price accommodation.
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * Scope a query to filter by quotation offer price.
     */
    public function scopeByQuotationOfferPrice($query, $quotationOfferPriceId)
    {
        return $query->where('quotation_offer_price_id', $quotationOfferPriceId);
    }

    /**
     * Scope a query to filter by accommodation.
     */
    public function scopeByAccommodation($query, $accommodationId)
    {
        return $query->where('accommodation_id', $accommodationId);
    }

    /**
     * Scope a query to filter by nights range.
     */
    public function scopeByNightsRange($query, $minNights, $maxNights)
    {
        return $query->whereBetween('nights', [$minNights, $maxNights]);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by breakfast inclusion.
     */
    public function scopeIncludeBreakfast($query, $value = true)
    {
        return $query->where('is_include_breakfast', $value);
    }

    /**
     * Scope a query to filter by lunch inclusion.
     */
    public function scopeIncludeLunch($query, $value = true)
    {
        return $query->where('is_include_lunch', $value);
    }

    /**
     * Scope a query to filter by dinner inclusion.
     */
    public function scopeIncludeDinner($query, $value = true)
    {
        return $query->where('is_include_dinner', $value);
    }

    /**
     * Get the accommodation name.
     */
    public function getAccommodationNameAttribute(): ?string
    {
        return $this->accommodation?->name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Get total cost (nights * price).
     */
    public function getTotalCostAttribute(): float
    {
        return $this->nights * (float) $this->price;
    }

    /**
     * Get formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }

    /**
     * Get price per night.
     */
    public function getPricePerNightAttribute(): float
    {
        return (float) $this->price;
    }

    /**
     * Get formatted price per night.
     */
    public function getFormattedPricePerNightAttribute(): string
    {
        return number_format($this->price_per_night, 2);
    }

    /**
     * Check if any meal is included.
     */
    public function getHasMealsIncludedAttribute(): bool
    {
        return $this->is_include_breakfast || $this->is_include_lunch || $this->is_include_dinner;
    }

    /**
     * Get count of included meals per day.
     */
    public function getIncludedMealsCountAttribute(): int
    {
        $count = 0;
        if ($this->is_include_breakfast) $count++;
        if ($this->is_include_lunch) $count++;
        if ($this->is_include_dinner) $count++;
        return $count;
    }

    /**
     * Get list of included meals.
     */
    public function getIncludedMealsListAttribute(): array
    {
        $meals = [];
        if ($this->is_include_breakfast) $meals[] = 'Breakfast';
        if ($this->is_include_lunch) $meals[] = 'Lunch';
        if ($this->is_include_dinner) $meals[] = 'Dinner';
        return $meals;
    }

    /**
     * Get included meals as string.
     */
    public function getIncludedMealsStringAttribute(): string
    {
        $meals = $this->included_meals_list;
        if (empty($meals)) {
            return 'No meals included';
        }
        return implode(', ', $meals);
    }

    /**
     * Get accommodation details breakdown.
     */
    public function getAccommodationBreakdownAttribute(): array
    {
        return [
            'accommodation_name' => $this->accommodation_name,
            'nights' => $this->nights,
            'price_per_night' => (float) $this->price,
            'total_cost' => $this->total_cost,
            'meals_included' => $this->included_meals_list,
            'meals_count' => $this->included_meals_count,
        ];
    }
}
