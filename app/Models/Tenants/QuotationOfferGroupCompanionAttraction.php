<?php

namespace App\Models\Tenants;

use App\Models\Base\Attraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_attractions';

    protected $fillable = [
        'quotation_offer_group_companion_id',
        'attraction_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group companion that owns this attraction cost.
     */
    public function quotationOfferGroupCompanion(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanion::class);
    }

    /**
     * Get the attraction for this cost.
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
        return $this->hasMany(QuotationOfferGroupCompanionSubAttraction::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Scope to filter by companion.
     */
    public function scopeByCompanion($query, $companionId)
    {
        return $query->where('quotation_offer_group_companion_id', $companionId);
    }

    /**
     * Scope to filter by attraction.
     */
    public function scopeByAttraction($query, $attractionId)
    {
        return $query->where('attraction_id', $attractionId);
    }
}
