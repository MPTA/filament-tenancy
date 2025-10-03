<?php

namespace App\Models\Tenants;

use App\Models\Base\SubAttraction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class QuotationOfferLeaderSubAttraction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'quotation_offer_leader_sub_attractions';

    protected $fillable = [
        'quotation_offer_leader_attraction_id',
        'sub_attraction_id',
        'price',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the quotation offer leader attraction that owns this sub attraction.
     */
    public function quotationOfferLeaderAttraction(): BelongsTo
    {
        return $this->belongsTo(QuotationOfferLeaderAttraction::class);
    }

    /**
     * Get the sub attraction for this entry.
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
        return number_format($this->price, 2);
    }

    /**
     * Scope to filter by leader attraction.
     */
    public function scopeByLeaderAttraction($query, $leaderAttractionId)
    {
        return $query->where('quotation_offer_leader_attraction_id', $leaderAttractionId);
    }

    /**
     * Scope to filter by sub attraction.
     */
    public function scopeBySubAttraction($query, $subAttractionId)
    {
        return $query->where('sub_attraction_id', $subAttractionId);
    }
}
