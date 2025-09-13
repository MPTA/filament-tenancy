<?php

namespace App\Models\Tenants;

use App\Models\Base\Attraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'attraction_id',
        'local_price',
        'foreigner_price',
    ];

    protected $casts = [
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
    ];

    /**
     * Get the attraction for this tenant attraction.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    /**
     * Scope a query to filter by local price range.
     */
    public function scopeByLocalPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('local_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by foreigner price range.
     */
    public function scopeByForeignerPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('foreigner_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to filter by maximum local price.
     */
    public function scopeMaxLocalPrice($query, $maxPrice)
    {
        return $query->where('local_price', '<=', $maxPrice);
    }

    /**
     * Scope a query to filter by maximum foreigner price.
     */
    public function scopeMaxForeignerPrice($query, $maxPrice)
    {
        return $query->where('foreigner_price', '<=', $maxPrice);
    }

    /**
     * Scope a query to filter by attraction.
     */
    public function scopeByAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }

    /**
     * Scope a query to search tenant attractions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('attraction', function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%")
              ->orWhere('address->en', 'like', "%{$search}%")
              ->orWhere('address->fa', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted local price.
     */
    public function getFormattedLocalPriceAttribute(): string
    {
        if (!$this->local_price) {
            return 'Not specified';
        }
        return number_format((float) $this->local_price, 2) . ' USD';
    }

    /**
     * Get the formatted foreigner price.
     */
    public function getFormattedForeignerPriceAttribute(): string
    {
        if (!$this->foreigner_price) {
            return 'Not specified';
        }
        return number_format((float) $this->foreigner_price, 2) . ' USD';
    }

    /**
     * Get the price difference between foreigner and local.
     */
    public function getPriceDifferenceAttribute(): ?float
    {
        if (!$this->local_price || !$this->foreigner_price) {
            return null;
        }
        return (float) $this->foreigner_price - (float) $this->local_price;
    }

    /**
     * Get the formatted price difference.
     */
    public function getFormattedPriceDifferenceAttribute(): string
    {
        $difference = $this->price_difference;
        if ($difference === null) {
            return 'Not calculable';
        }
        
        $sign = $difference > 0 ? '+' : '';
        return $sign . number_format($difference, 2) . ' USD';
    }

    /**
     * Check if local price is available.
     */
    public function getHasLocalPriceAttribute(): bool
    {
        return !is_null($this->local_price);
    }

    /**
     * Check if foreigner price is available.
     */
    public function getHasForeignerPriceAttribute(): bool
    {
        return !is_null($this->foreigner_price);
    }

    /**
     * Get the attraction name.
     */
    public function getAttractionNameAttribute(): string
    {
        return $this->attraction ? $this->attraction->name : 'Unknown Attraction';
    }
}