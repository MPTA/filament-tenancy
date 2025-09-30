<?php

namespace App\Models\Tenants;

use App\Models\Base\RoomCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferPrice extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_id',
        'room_category_id',
        'per_person_price',
        'tenant_id',
    ];

    protected $casts = [
        'per_person_price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer for this price.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
    }

    /**
     * Get the room category for this price.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get the quotation offer price accommodations for this price (one-to-many relationship).
     */
    public function quotationOfferPriceAccommodations(): HasMany
    {
        return $this->hasMany(QuotationOfferPriceAccommodation::class);
    }

    /**
     * Scope a query to filter by quotation offer.
     */
    public function scopeByQuotationOffer($query, $quotationOfferId)
    {
        return $query->where('quotation_offer_id', $quotationOfferId);
    }

    /**
     * Scope a query to filter by room category.
     */
    public function scopeByRoomCategory($query, $roomCategoryId)
    {
        return $query->where('room_category_id', $roomCategoryId);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('per_person_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by minimum price.
     */
    public function scopeByMinPrice($query, $minPrice)
    {
        return $query->where('per_person_price', '>=', $minPrice);
    }

    /**
     * Scope a query to filter by maximum price.
     */
    public function scopeByMaxPrice($query, $maxPrice)
    {
        return $query->where('per_person_price', '<=', $maxPrice);
    }

    /**
     * Scope a query to order by price ascending.
     */
    public function scopeOrderByPriceAsc($query)
    {
        return $query->orderBy('per_person_price', 'asc');
    }

    /**
     * Scope a query to order by price descending.
     */
    public function scopeOrderByPriceDesc($query)
    {
        return $query->orderBy('per_person_price', 'desc');
    }

    /**
     * Get the formatted per person price.
     */
    public function getFormattedPerPersonPriceAttribute(): string
    {
        return number_format((float) $this->per_person_price, 2);
    }

    /**
     * Get the room category name.
     */
    public function getRoomCategoryNameAttribute(): ?string
    {
        return $this->roomCategory?->name;
    }

    /**
     * Get the room category slug.
     */
    public function getRoomCategorySlugAttribute(): ?string
    {
        return $this->roomCategory?->slug;
    }

    /**
     * Get the room category capacity.
     */
    public function getRoomCategoryCapacityAttribute(): ?int
    {
        return $this->roomCategory?->capacity;
    }

    /**
     * Calculate total price for a specific number of people.
     */
    public function calculateTotalPrice(int $numberOfPeople): float
    {
        return (float) $this->per_person_price * $numberOfPeople;
    }

    /**
     * Get formatted total price for a specific number of people.
     */
    public function getFormattedTotalPriceForPeople(int $numberOfPeople): string
    {
        return number_format($this->calculateTotalPrice($numberOfPeople), 2);
    }

    /**
     * Check if this price is free.
     */
    public function getIsFreeAttribute(): bool
    {
        return (float) $this->per_person_price == 0;
    }

    /**
     * Check if this price is premium (above a certain threshold).
     */
    public function getIsPremiumAttribute(): bool
    {
        return (float) $this->per_person_price > 100; // Assuming 100 is premium threshold
    }

    /**
     * Get price category based on price range.
     */
    public function getPriceCategoryAttribute(): string
    {
        $price = (float) $this->per_person_price;
        
        if ($price == 0) {
            return 'Free';
        } elseif ($price <= 50) {
            return 'Budget';
        } elseif ($price <= 100) {
            return 'Standard';
        } elseif ($price <= 200) {
            return 'Premium';
        } else {
            return 'Luxury';
        }
    }

    /**
     * Get price comparison with another price.
     */
    public function compareWith(QuotationOfferPrice $other): array
    {
        $thisPrice = (float) $this->per_person_price;
        $otherPrice = (float) $other->per_person_price;
        
        if ($thisPrice == $otherPrice) {
            return ['status' => 'equal', 'difference' => 0, 'percentage' => 0];
        }
        
        $difference = $thisPrice - $otherPrice;
        $percentage = $otherPrice > 0 ? ($difference / $otherPrice) * 100 : 0;
        
        return [
            'status' => $thisPrice > $otherPrice ? 'higher' : 'lower',
            'difference' => $difference,
            'percentage' => $percentage
        ];
    }

    /**
     * Get price range information.
     */
    public function getPriceRangeInfoAttribute(): array
    {
        $price = (float) $this->per_person_price;
        
        return [
            'price' => $price,
            'formatted' => $this->formatted_per_person_price,
            'category' => $this->price_category,
            'is_free' => $this->is_free,
            'is_premium' => $this->is_premium,
        ];
    }
}
