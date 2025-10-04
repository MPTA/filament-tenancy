<?php

namespace App\Models\Tenants;

use App\Models\Base\SubAttraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferGroupCompanionSubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_group_companion_sub_attractions';

    protected $fillable = [
        'quotation_offer_group_companion_attraction_id',
        'sub_attraction_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer group companion attraction that owns this sub attraction.
     */
    public function quotationOfferGroupCompanionAttraction(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferGroupCompanionAttraction::class);
    }

    /**
     * Get the sub attraction for this cost.
     */
    public function subAttraction(): BelongsTo
    {
        return $this->belongsTo(SubAttraction::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2);
    }

    /**
     * Scope to filter by companion attraction.
     */
    public function scopeByCompanionAttraction($query, $attractionId)
    {
        return $query->where('quotation_offer_group_companion_attraction_id', $attractionId);
    }

    /**
     * Scope to filter by sub attraction.
     */
    public function scopeBySubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }
}
