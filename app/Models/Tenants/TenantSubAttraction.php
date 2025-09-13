<?php

namespace App\Models\Tenants;

use App\Models\Base\SubAttraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantSubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'tenant_attraction_id',
        'sub_attraction_id',
        'local_price',
        'foreigner_price',
        'tenant_id',
    ];

    protected $casts = [
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
    ];

    /**
     * Get the tenant attraction that owns this sub attraction.
     */
    public function tenantAttraction(): BelongsTo
    {
        return $this->belongsTo(TenantAttraction::class);
    }

    /**
     * Get the sub attraction for this tenant sub attraction.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(SubAttraction::class);
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
     * Scope a query to filter by tenant attraction.
     */
    public function scopeByTenantAttraction($query, $tenantAttractionId)
    {
        return $query->where('tenant_attraction_id', $tenantAttractionId);
    }

    /**
     * Scope a query to filter by sub attraction.
     */
    public function scopeBySubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }

    /**
     * Scope a query to search tenant sub attractions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('subAttraction', function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->fa', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%")
              ->orWhere('description->fa', 'like', "%{$search}%");
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
     * Get the sub attraction name.
     */
    public function getSubAttractionNameAttribute(): string
    {
        return $this->subAttraction ? $this->subAttraction->name : 'Unknown Sub Attraction';
    }

    /**
     * Get the parent attraction name through tenant attraction.
     */
    public function getParentAttractionNameAttribute(): string
    {
        if ($this->tenantAttraction && $this->tenantAttraction->attraction) {
            return $this->tenantAttraction->attraction->name;
        }
        return 'Unknown Parent Attraction';
    }
}