<?php

namespace App\Models\Tenants;

use App\Models\Base\SubAttraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupSubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_attraction_id',
        'sub_attraction_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group attraction for this sub attraction.
     */
    public function quotationOfferGroupAttraction(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupAttraction::class);
    }

    /**
     * Get the sub attraction for this offer group sub attraction.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(SubAttraction::class);
    }

    /**
     * Scope a query to filter by quotation offer group attraction.
     */
    public function scopeByQuotationOfferGroupAttraction($query, $quotationOfferGroupAttractionId)
    {
        return $query->where('quotation_offer_group_attraction_id', $quotationOfferGroupAttractionId);
    }

    /**
     * Scope a query to filter by sub attraction.
     */
    public function scopeBySubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Get the sub attraction name.
     */
    public function getSubAttractionNameAttribute(): ?string
    {
        return $this->subAttraction?->name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }
}
