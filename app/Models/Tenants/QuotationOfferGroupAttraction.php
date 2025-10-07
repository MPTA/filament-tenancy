<?php

namespace App\Models\Tenants;

use App\Models\Base\Attraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'quotation_offer_group_id',
        'attraction_id',
        'price',
        'is_outview',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_outview' => 'boolean',
    ];

    /**
     * Get the quotation offer group for this attraction.
     */
    public function quotationOfferGroup(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroup::class);
    }

    /**
     * Get the attraction for this offer group attraction.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    /**
     * Get the quotation offer group sub attractions for this attraction (one-to-many relationship).
     */
    public function quotationOfferGroupSubAttractions(): HasMany
    {
        return $this->hasMany(QuotationOfferGroupSubAttraction::class);
    }

    /**
     * Alias for quotationOfferGroupSubAttractions.
     */
    public function subAttractions(): HasMany
    {
        return $this->quotationOfferGroupSubAttractions();
    }

    /**
     * Scope a query to filter by quotation offer group.
     */
    public function scopeByQuotationOfferGroup($query, $quotationOfferGroupId)
    {
        return $query->where('quotation_offer_group_id', $quotationOfferGroupId);
    }

    /**
     * Scope a query to filter by attraction.
     */
    public function scopeByAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }

    /**
     * Scope a query to filter by outview status.
     */
    public function scopeOutview($query, $value = true)
    {
        return $query->where('is_outview', $value);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Get the attraction name.
     */
    public function getAttractionNameAttribute(): ?string
    {
        return $this->attraction?->name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Check if this is an outview attraction.
     */
    public function getIsOutviewAttractionAttribute(): bool
    {
        return $this->is_outview;
    }

    /**
     * Get the view type (outview or inside view).
     */
    public function getViewTypeAttribute(): string
    {
        return $this->is_outview ? 'Outview' : 'Inside View';
    }
}
