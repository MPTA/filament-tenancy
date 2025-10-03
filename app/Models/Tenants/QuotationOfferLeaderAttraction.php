<?php

namespace App\Models\Tenants;

use App\Models\Base\Attraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferLeaderAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_leader_attractions';

    protected $fillable = [
        'quotation_offer_id',
        'attraction_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer that owns this attraction.
     */
    public function quotationOffer(): BelongsTo
    {
        return $this->belongsTo(QuotationOffer::class);
    }

    /**
     * Get the attraction for this entry.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    /**
     * Get the sub attractions for this attraction.
     */
    public function subAttractions(): HasMany
    {
        return $this->hasMany(QuotationOfferLeaderSubAttraction::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Get total cost including sub attractions.
     */
    public function getTotalCostAttribute(): float
    {
        $subAttractionsTotal = $this->subAttractions()->sum('price');
        return $this->price + $subAttractionsTotal;
    }

    /**
     * Get formatted total cost.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2);
    }

    /**
     * Scope to filter by offer.
     */
    public function scopeByOffer($query, $offerId)
    {
        return $query->where('quotation_offer_id', $offerId);
    }

    /**
     * Scope to filter by attraction.
     */
    public function scopeByAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }
}
